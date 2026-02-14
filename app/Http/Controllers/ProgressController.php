<?php

namespace App\Http\Controllers;

use App\Models\FoodDiaryEntry;
use App\Models\WeightLog;
use App\Models\Achievement;
use App\Models\Recipe;
use App\Services\AchievementService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function __construct(
        private AchievementService $achievementService
    ) {}

    /**
     * Главная страница прогресса
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Дневник за сегодня
        $todayEntries = $user->foodDiaryEntries()
            ->where('date', $today)
            ->with('recipe')
            ->orderByRaw("CASE meal_type WHEN 'breakfast' THEN 1 WHEN 'lunch' THEN 2 WHEN 'dinner' THEN 3 WHEN 'snack' THEN 4 ELSE 5 END")
            ->get();

        $todayTotals = [
            'calories' => $todayEntries->sum('calories'),
            'proteins' => $todayEntries->sum('proteins'),
            'fats' => $todayEntries->sum('fats'),
            'carbs' => $todayEntries->sum('carbs'),
        ];

        // Последние 30 дней — вес
        $weightLogs = $user->weightLogs()
            ->where('date', '>=', $today->copy()->subDays(90))
            ->orderBy('date')
            ->get();

        // КБЖУ за неделю
        $weekStart = $today->copy()->startOfWeek();
        $weekStats = $user->foodDiaryEntries()
            ->where('date', '>=', $weekStart)
            ->where('date', '<=', $today)
            ->selectRaw('date, SUM(calories) as calories, SUM(proteins) as proteins, SUM(fats) as fats, SUM(carbs) as carbs')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // КБЖУ за месяц (средние по дням)
        $monthStart = $today->copy()->subDays(30);
        $dailyTotals = $user->foodDiaryEntries()
            ->where('date', '>=', $monthStart)
            ->selectRaw('date, SUM(calories) as cal, SUM(proteins) as p, SUM(fats) as f, SUM(carbs) as c')
            ->groupBy('date')
            ->get();

        $monthAvg = (object) [
            'avg_calories' => $dailyTotals->count() > 0 ? round($dailyTotals->avg('cal'), 1) : null,
            'avg_proteins' => $dailyTotals->count() > 0 ? round($dailyTotals->avg('p'), 1) : null,
            'avg_fats' => $dailyTotals->count() > 0 ? round($dailyTotals->avg('f'), 1) : null,
            'avg_carbs' => $dailyTotals->count() > 0 ? round($dailyTotals->avg('c'), 1) : null,
        ];

        // Достижения
        $this->achievementService->checkAndUnlock($user);
        $allAchievements = Achievement::orderBy('sort_order')->get();
        $userAchievementIds = $user->achievements()->pluck('achievements.id')->toArray();

        // Серия дней
        $streak = $this->achievementService->getDiaryStreak($user);

        return view('dashboard.progress', compact(
            'todayEntries',
            'todayTotals',
            'weightLogs',
            'weekStats',
            'monthAvg',
            'allAchievements',
            'userAchievementIds',
            'streak',
            'today'
        ));
    }

    /**
     * Добавить запись в дневник
     */
    public function storeDiary(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'recipe_id' => 'nullable|exists:recipes,id',
            'custom_name' => 'required_without:recipe_id|nullable|string|max:255',
            'calories' => 'required|numeric|min:0|max:9999',
            'proteins' => 'required|numeric|min:0|max:999',
            'fats' => 'required|numeric|min:0|max:999',
            'carbs' => 'required|numeric|min:0|max:999',
            'portion_size' => 'nullable|numeric|min:0.1|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        // Если выбран рецепт — подставить КБЖУ автоматически
        if (!empty($validated['recipe_id'])) {
            $recipe = Recipe::find($validated['recipe_id']);
            if ($recipe) {
                $portion = $validated['portion_size'] ?? 1;
                $validated['calories'] = $recipe->calories * $portion;
                $validated['proteins'] = $recipe->proteins * $portion;
                $validated['fats'] = $recipe->fats * $portion;
                $validated['carbs'] = $recipe->carbs * $portion;
                $validated['custom_name'] = null;
            }
        }

        $validated['user_id'] = $user->id;
        $validated['portion_size'] = $validated['portion_size'] ?? 1;

        FoodDiaryEntry::create($validated);

        // Проверяем достижения
        $this->achievementService->checkAndUnlock($user);

        return back()->with('success', 'Запись добавлена в дневник');
    }

    /**
     * Удалить запись из дневника
     */
    public function destroyDiary(FoodDiaryEntry $entry)
    {
        $user = auth()->user();
        if ($entry->user_id !== $user->id) {
            abort(403);
        }

        $entry->delete();

        return back()->with('success', 'Запись удалена');
    }

    /**
     * Добавить запись веса
     */
    public function storeWeight(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'weight' => 'required|numeric|min:20|max:300',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        WeightLog::updateOrCreate(
            ['user_id' => $user->id, 'date' => $validated['date']],
            ['weight' => $validated['weight'], 'notes' => $validated['notes'] ?? null]
        );

        // Обновляем текущий вес в профиле
        $user->update(['weight' => $validated['weight']]);

        // Проверяем достижения
        $this->achievementService->checkAndUnlock($user);

        return back()->with('success', 'Вес записан');
    }

    /**
     * Поиск рецептов для автозаполнения дневника (JSON)
     */
    public function searchRecipes(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $recipes = Recipe::where('is_published', true)
            ->where('title', 'ilike', "%{$query}%")
            ->select('id', 'title', 'calories', 'proteins', 'fats', 'carbs', 'category')
            ->limit(10)
            ->get();

        return response()->json($recipes);
    }
}
