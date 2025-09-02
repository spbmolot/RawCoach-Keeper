<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Day;
use App\Models\Recipe;
use App\Models\PersonalPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Главная страница личного кабинета
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        
        // Получаем активную подписку
        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->with('plan')
            ->first();

        // Получаем меню на сегодня
        $todayMenu = $this->getTodayMenu();

        // Получаем персональные планы
        $personalPlans = PersonalPlan::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('nutritionist')
            ->latest()
            ->take(3)
            ->get();

        // Статистика
        $stats = [
            'total_recipes_viewed' => $user->recipe_views ?? 0,
            'favorite_recipes_count' => $user->favorites()->where('favorable_type', Recipe::class)->count(),
            'favorite_menus_count' => $user->favorites()->where('favorable_type', Menu::class)->count(),
            'personal_plans_count' => PersonalPlan::where('user_id', $user->id)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->load('roles'),
                'subscription' => $activeSubscription,
                'today_menu' => $todayMenu,
                'personal_plans' => $personalPlans,
                'stats' => $stats,
            ]
        ]);
    }

    /**
     * Меню на сегодня
     */
    public function today(): JsonResponse
    {
        $todayMenu = $this->getTodayMenu();

        if (!$todayMenu) {
            return response()->json([
                'success' => false,
                'message' => 'Меню на сегодня не найдено'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $todayMenu
        ]);
    }

    /**
     * Меню на неделю
     */
    public function week(): JsonResponse
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $weekDays = Day::with(['recipes.nutrition', 'menu'])
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->orderBy('date')
            ->get()
            ->groupBy(function ($day) {
                return Carbon::parse($day->date)->format('Y-m-d');
            });

        return response()->json([
            'success' => true,
            'data' => [
                'week_start' => $startOfWeek->format('Y-m-d'),
                'week_end' => $endOfWeek->format('Y-m-d'),
                'days' => $weekDays
            ]
        ]);
    }

    /**
     * Календарь меню
     */
    public function calendar(Request $request): JsonResponse
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $days = Day::with(['recipes', 'menu'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get()
            ->keyBy(function ($day) {
                return Carbon::parse($day->date)->format('Y-m-d');
            });

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'month' => $month,
                'days' => $days
            ]
        ]);
    }

    /**
     * Список покупок
     */
    public function shoppingList(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', Carbon::now()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->addDays(6)->format('Y-m-d'));

        // Получаем все рецепты за период
        $recipes = Recipe::whereHas('days', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        })
        ->with(['ingredients'])
        ->get();

        // Группируем ингредиенты
        $shoppingList = [];
        foreach ($recipes as $recipe) {
            foreach ($recipe->ingredients as $ingredient) {
                $key = $ingredient->name;
                if (!isset($shoppingList[$key])) {
                    $shoppingList[$key] = [
                        'name' => $ingredient->name,
                        'amount' => 0,
                        'unit' => $ingredient->unit,
                        'category' => $ingredient->category ?? 'Прочее'
                    ];
                }
                $shoppingList[$key]['amount'] += $ingredient->amount;
            }
        }

        // Группируем по категориям
        $groupedList = collect($shoppingList)->groupBy('category');

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'shopping_list' => $groupedList
            ]
        ]);
    }

    /**
     * Профиль пользователя
     */
    public function profile(): JsonResponse
    {
        $user = auth()->user();
        
        return response()->json([
            'success' => true,
            'data' => $user->load(['roles', 'permissions'])
        ]);
    }

    /**
     * Обновление профиля
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'height' => ['nullable', 'integer', 'min:100', 'max:250'],
            'weight' => ['nullable', 'numeric', 'min:30', 'max:300'],
            'activity_level' => ['nullable', 'in:sedentary,light,moderate,active,very_active'],
            'dietary_preferences' => ['nullable', 'array'],
            'allergies' => ['nullable', 'array'],
            'health_conditions' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Профиль успешно обновлен',
            'data' => $user->fresh()
        ]);
    }

    /**
     * Получить меню на сегодня
     */
    private function getTodayMenu()
    {
        $today = Carbon::now()->format('Y-m-d');
        
        return Day::with(['recipes.nutrition', 'recipes.ingredients', 'menu'])
            ->where('date', $today)
            ->first();
    }
}
