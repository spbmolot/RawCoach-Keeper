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
            'favorite_recipes_count' => $user->favoriteRecipes()->count(),
            'favorite_menus_count' => $user->favoriteMenus()->count(),
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

        $weekDays = collect();
        $currentDate = $startOfWeek->copy();
        
        while ($currentDate <= $endOfWeek) {
            $day = $this->getDayForDate($currentDate);
            $weekDays[$currentDate->format('Y-m-d')] = $day;
            $currentDate->addDay();
        }

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

        // Получаем меню за месяц
        $menu = Menu::where('month', $month)
            ->where('year', $year)
            ->where('is_published', true)
            ->with(['days.meals.recipe'])
            ->first();
        
        // Преобразуем дни в формат с датами
        $days = collect();
        if ($menu) {
            foreach ($menu->days as $day) {
                $dayDate = Carbon::create($year, $month, $day->day_number);
                if ($dayDate->month == $month) {
                    $days[$dayDate->format('Y-m-d')] = $day;
                }
            }
        }

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
        $startDate = Carbon::parse($request->get('start_date', Carbon::now()->format('Y-m-d')));
        $endDate = Carbon::parse($request->get('end_date', Carbon::now()->addDays(6)->format('Y-m-d')));

        // Получаем дни за период
        $recipes = collect();
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $day = $this->getDayForDate($currentDate);
            if ($day) {
                $day->load(['meals.recipe.ingredients']);
                foreach ($day->meals as $meal) {
                    if ($meal->recipe) {
                        $recipes->push($meal->recipe);
                    }
                }
            }
            $currentDate->addDay();
        }

        // Группируем ингредиенты
        $shoppingList = [];
        foreach ($recipes as $recipe) {
            foreach ($recipe->ingredients as $ingredient) {
                $key = $ingredient->ingredient_name ?? $ingredient->name ?? 'unknown';
                if (!isset($shoppingList[$key])) {
                    $shoppingList[$key] = [
                        'name' => $key,
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
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d')
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
     * Получить день меню для конкретной даты
     */
    private function getDayForDate($date)
    {
        $user = auth()->user();
        $dayNumber = $date->day;
        $month = $date->month;
        $year = $date->year;
        
        return Day::whereHas('menu', function($query) use ($user, $month, $year) {
                $query->where('month', $month)
                    ->where('year', $year)
                    ->where('is_published', true);
                $this->filterMenuBySubscription($query, $user);
            })
            ->where('day_number', $dayNumber)
            ->with(['menu', 'meals.recipe.ingredients'])
            ->first();
    }

    /**
     * Получить меню на сегодня
     */
    private function getTodayMenu()
    {
        return $this->getDayForDate(Carbon::now());
    }

    /**
     * Фильтрация меню по подписке пользователя
     */
    private function filterMenuBySubscription($query, $user)
    {
        if (!$user) {
            $query->where('type', 'demo');
            return;
        }

        $subscription = $user->activeSubscription()->with('plan')->first();

        if ($user->hasRole('root') || $user->hasRole('admin')) {
            return;
        }

        if (!$subscription) {
            $query->where('type', 'demo');
            return;
        }

        $plan = $subscription->plan;

        if ($plan->type === 'trial') {
            $query->whereIn('type', ['trial', 'current']);
        } elseif ($plan->type === 'monthly' && !str_contains($plan->slug, 'personal')) {
            $query->where('type', 'current');
        } elseif ($plan->type === 'yearly' && !str_contains($plan->slug, 'personal')) {
            $query->whereIn('type', ['current', 'archive', 'early']);
        } elseif (str_contains($plan->slug, 'personal')) {
            $query->whereIn('type', ['current', 'archive', 'early', 'personal'])
                  ->where(function($q) use ($user) {
                      $q->where('type', '!=', 'personal')
                        ->orWhere('user_id', $user->id);
                  });
        } else {
            $query->where('type', 'current');
        }
    }
}
