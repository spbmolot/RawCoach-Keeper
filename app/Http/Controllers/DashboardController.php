<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\Day;
use App\Models\PersonalPlan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Главная страница личного кабинета
     */
    public function index()
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription()->with('plan')->first();
        
        // Получаем меню на сегодня
        $today = Carbon::today();
        $todayMenu = $this->getTodayMenu($user, $today);
        
        // Получаем статистику
        $stats = $this->getUserStats($user);
        
        // Получаем последние активности
        $recentActivities = $this->getRecentActivities($user);

        return view('dashboard.index', compact(
            'user', 
            'subscription', 
            'todayMenu', 
            'stats', 
            'recentActivities'
        ));
    }

    /**
     * Страница "Сегодня" - меню на текущий день
     */
    public function today()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $todayMenu = $this->getTodayMenu($user, $today);
        
        if (!$todayMenu) {
            return view('dashboard.today-empty');
        }

        $recipes = $todayMenu->recipes()
            ->with(['nutrition', 'ingredients'])
            ->get()
            ->groupBy('meal_type');

        return view('dashboard.today', compact('todayMenu', 'recipes', 'today'));
    }

    /**
     * Страница недельного планирования
     */
    public function week(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->get('date') 
            ? Carbon::parse($request->get('date'))->startOfWeek()
            : Carbon::now()->startOfWeek();
        
        $weekDays = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dayMenu = $this->getTodayMenu($user, $date);
            
            $weekDays->push([
                'date' => $date,
                'menu' => $dayMenu,
                'recipes_count' => $dayMenu ? $dayMenu->recipes()->count() : 0,
            ]);
        }

        return view('dashboard.week', compact('weekDays', 'startDate'));
    }

    /**
     * Календарь меню
     */
    public function calendar(Request $request)
    {
        $user = auth()->user();
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        // Получаем все дни с меню за месяц
        $menuDays = Day::whereHas('menu', function($query) use ($user) {
                $this->filterMenuBySubscription($query, $user);
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['menu', 'recipes'])
            ->get()
            ->keyBy(function($day) {
                return $day->date->format('Y-m-d');
            });

        return view('dashboard.calendar', compact('menuDays', 'startDate', 'endDate'));
    }

    /**
     * Список покупок
     */
    public function shoppingList(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->get('start_date') 
            ? Carbon::parse($request->get('start_date'))
            : Carbon::today();
        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))
            : $startDate->copy()->addDays(6);

        // Получаем все рецепты за период
        $recipes = Recipe::whereHas('days', function($query) use ($user, $startDate, $endDate) {
                $query->whereHas('menu', function($menuQuery) use ($user) {
                    $this->filterMenuBySubscription($menuQuery, $user);
                })
                ->whereBetween('date', [$startDate, $endDate]);
            })
            ->with(['ingredients.ingredient'])
            ->get();

        // Группируем ингредиенты
        $shoppingList = $this->generateShoppingList($recipes);

        return view('dashboard.shopping-list', compact(
            'shoppingList', 
            'startDate', 
            'endDate',
            'recipes'
        ));
    }

    /**
     * Экспорт списка покупок
     */
    public function exportShoppingList(Request $request)
    {
        $user = auth()->user();
        
        // Проверяем права на экспорт
        if (!$user->hasPermissionTo('export.shopping_lists')) {
            abort(403, 'Недостаточно прав для экспорта');
        }

        $startDate = Carbon::parse($request->get('start_date'));
        $endDate = Carbon::parse($request->get('end_date'));
        
        $recipes = Recipe::whereHas('days', function($query) use ($user, $startDate, $endDate) {
                $query->whereHas('menu', function($menuQuery) use ($user) {
                    $this->filterMenuBySubscription($menuQuery, $user);
                })
                ->whereBetween('date', [$startDate, $endDate]);
            })
            ->with(['ingredients.ingredient'])
            ->get();

        $shoppingList = $this->generateShoppingList($recipes);
        
        $filename = "shopping-list-{$startDate->format('Y-m-d')}-{$endDate->format('Y-m-d')}.pdf";
        
        // Здесь будет генерация PDF
        // return PDF::loadView('exports.shopping-list', compact('shoppingList'))->download($filename);
        
        return back()->with('success', 'Список покупок экспортирован');
    }

    /**
     * Персональные планы пользователя
     */
    public function personalPlans()
    {
        $user = auth()->user();
        
        $personalPlans = PersonalPlan::where('user_id', $user->id)
            ->with(['questionnaire', 'nutritionist'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.personal-plans', compact('personalPlans'));
    }

    /**
     * Профиль пользователя
     */
    public function profile()
    {
        $user = auth()->user();
        return view('dashboard.profile', compact('user'));
    }

    /**
     * Обновление профиля
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'height' => 'nullable|integer|min:100|max:250',
            'weight' => 'nullable|integer|min:30|max:300',
            'activity_level' => 'nullable|in:sedentary,light,moderate,active,very_active',
            'dietary_preferences' => 'nullable|array',
            'allergies' => 'nullable|array',
        ]);

        $user->update($request->only([
            'name', 'email', 'phone', 'birth_date', 'gender', 
            'height', 'weight', 'activity_level', 'dietary_preferences', 'allergies'
        ]));

        return back()->with('success', 'Профиль успешно обновлен');
    }

    /**
     * Получить меню на конкретный день для пользователя
     */
    private function getTodayMenu($user, $date)
    {
        return Menu::whereHas('days', function($query) use ($date) {
                $query->where('date', $date->format('Y-m-d'));
            })
            ->where(function($query) use ($user) {
                $this->filterMenuBySubscription($query, $user);
            })
            ->with(['days' => function($query) use ($date) {
                $query->where('date', $date->format('Y-m-d'));
            }])
            ->first();
    }

    /**
     * Фильтрация меню по подписке пользователя
     */
    private function filterMenuBySubscription($query, $user)
    {
        $subscription = $user->activeSubscription()->with('plan')->first();
        
        if (!$subscription) {
            // Только демо контент для пользователей без подписки
            $query->where('type', 'demo');
            return;
        }

        $plan = $subscription->plan;
        
        // Базовая подписка - только текущие меню
        if ($plan->slug === 'monthly') {
            $query->where('type', 'current');
        }
        // Годовая - текущие + архивы + ранний доступ
        elseif ($plan->slug === 'yearly') {
            $query->whereIn('type', ['current', 'archive', 'early']);
        }
        // Персональная - все + персональные
        elseif ($plan->slug === 'personal') {
            $query->whereIn('type', ['current', 'archive', 'early', 'personal'])
                  ->where(function($q) use ($user) {
                      $q->where('type', '!=', 'personal')
                        ->orWhere('user_id', $user->id);
                  });
        }
        // Пробная - только пробный контент
        elseif ($plan->slug === 'trial') {
            $query->where('type', 'trial');
        }
    }

    /**
     * Получить статистику пользователя
     */
    private function getUserStats($user)
    {
        $subscription = $user->activeSubscription()->first();
        
        return [
            'recipes_viewed' => $user->recipeViews()->count(),
            'shopping_lists_generated' => $user->shoppingLists()->count(),
            'subscription_days_left' => $subscription ? $subscription->ends_at->diffInDays(Carbon::now()) : 0,
            'favorite_recipes' => $user->favoriteRecipes()->count(),
        ];
    }

    /**
     * Получить последние активности пользователя
     */
    private function getRecentActivities($user)
    {
        // Здесь можно добавить логику для отслеживания активности
        return collect();
    }

    /**
     * Генерация списка покупок из рецептов
     */
    private function generateShoppingList($recipes)
    {
        $ingredients = collect();
        
        foreach ($recipes as $recipe) {
            foreach ($recipe->ingredients as $recipeIngredient) {
                $key = $recipeIngredient->ingredient->id;
                
                if ($ingredients->has($key)) {
                    $ingredients[$key]['amount'] += $recipeIngredient->amount;
                } else {
                    $ingredients[$key] = [
                        'ingredient' => $recipeIngredient->ingredient,
                        'amount' => $recipeIngredient->amount,
                        'unit' => $recipeIngredient->unit,
                    ];
                }
            }
        }
        
        return $ingredients->groupBy('ingredient.category');
    }
}
