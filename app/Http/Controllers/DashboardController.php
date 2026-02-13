<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\Day;
use App\Models\PersonalPlan;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $activeSubscription = $user->activeSubscription()->with('plan')->first();
        
        // Получаем текущее меню
        $currentMenu = Menu::where('is_published', true)
            ->where('month', Carbon::now()->month)
            ->where('year', Carbon::now()->year)
            ->with(['days.meals.recipe'])
            ->first();
        
        // Получаем недавние рецепты
        $recentRecipes = Recipe::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'user', 
            'activeSubscription', 
            'currentMenu',
            'recentRecipes'
        ));
    }

    /**
     * Страница "Сегодня" - меню на текущий день
     */
    public function today()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $dayNumber = $today->day;
        
        $todayDay = $this->getDayForDate($user, $today);
        
        if (!$todayDay) {
            return view('dashboard.today-empty');
        }

        $todayDay->load(['meals.recipe.ingredients']);
        
        $recipes = $todayDay->meals
            ->groupBy('meal_type')
            ->map(fn($meals) => $meals->pluck('recipe')->filter());

        return view('dashboard.today', compact('todayDay', 'recipes', 'today'));
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
            $day = $this->getDayForDate($user, $date);
            
            $weekDays->push([
                'date' => $date,
                'day' => $day,
                'recipes_count' => $day ? $day->meals()->count() : 0,
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
        
        // Получаем меню за месяц
        $menu = Menu::where('month', $month)
            ->where('year', $year)
            ->where('is_published', true)
            ->where(function($query) use ($user) {
                $this->filterMenuBySubscription($query, $user);
            })
            ->with(['days.meals.recipe'])
            ->first();
        
        // Преобразуем дни в формат с датами
        $menuDays = collect();
        if ($menu) {
            foreach ($menu->days as $day) {
                $dayDate = Carbon::create($year, $month, $day->day_number);
                if ($dayDate->month == $month) {
                    $menuDays[$dayDate->format('Y-m-d')] = $day;
                }
            }
        }

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

        // Получаем дни за период
        $days = collect();
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $day = $this->getDayForDate($user, $currentDate);
            if ($day) {
                $days->push($day);
            }
            $currentDate->addDay();
        }
        
        // Собираем все рецепты из дней
        $recipes = collect();
        foreach ($days as $day) {
            $day->load(['meals.recipe.ingredients']);
            foreach ($day->meals as $meal) {
                if ($meal->recipe) {
                    $recipes->push($meal->recipe);
                }
            }
        }

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
        
        // Получаем дни за период
        $days = collect();
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $day = $this->getDayForDate($user, $currentDate);
            if ($day) {
                $days->push($day);
            }
            $currentDate->addDay();
        }
        
        // Собираем все рецепты из дней
        $recipes = collect();
        foreach ($days as $day) {
            $day->load(['meals.recipe.ingredients']);
            foreach ($day->meals as $meal) {
                if ($meal->recipe) {
                    $recipes->push($meal->recipe);
                }
            }
        }

        $shoppingList = $this->generateShoppingList($recipes);
        
        $filename = "shopping-list-{$startDate->format('Y-m-d')}-{$endDate->format('Y-m-d')}.pdf";
        
        $pdf = Pdf::loadView('exports.shopping-list', compact('shoppingList', 'startDate', 'endDate'));
        
        return $pdf->download($filename);
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
        $user->load(['activeSubscription.plan', 'favoriteRecipes', 'payments']);
        
        // Статистика пользователя
        $stats = [
            'days_with_us' => $user->created_at->diffInDays(now()),
            'favorite_recipes' => $user->favoriteRecipes->count(),
            'payments_count' => $user->payments->where('status', 'succeeded')->count(),
            'total_spent' => $user->payments->where('status', 'succeeded')->sum('amount'),
        ];
        
        // Прогресс заполнения профиля
        $profileFields = [
            'name' => !empty($user->name),
            'email' => !empty($user->email),
            'phone' => !empty($user->phone),
            'birth_date' => !empty($user->birth_date),
            'gender' => !empty($user->gender),
            'height' => !empty($user->height),
            'weight' => !empty($user->weight),
            'activity_level' => !empty($user->activity_level),
        ];
        $profileProgress = round((array_sum($profileFields) / count($profileFields)) * 100);
        
        // Списки для выбора
        $dietaryOptions = [
            'vegetarian' => 'Вегетарианство',
            'vegan' => 'Веганство',
            'pescatarian' => 'Пескетарианство',
            'gluten_free' => 'Без глютена',
            'lactose_free' => 'Без лактозы',
            'keto' => 'Кето',
            'paleo' => 'Палео',
            'low_carb' => 'Низкоуглеводная',
            'high_protein' => 'Высокобелковая',
        ];
        
        $allergyOptions = [
            'nuts' => 'Орехи',
            'peanuts' => 'Арахис',
            'dairy' => 'Молочные продукты',
            'eggs' => 'Яйца',
            'fish' => 'Рыба',
            'shellfish' => 'Морепродукты',
            'soy' => 'Соя',
            'wheat' => 'Пшеница',
            'sesame' => 'Кунжут',
        ];
        
        return view('dashboard.profile', compact(
            'user', 
            'stats', 
            'profileProgress', 
            'profileFields',
            'dietaryOptions',
            'allergyOptions'
        ));
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
            'height' => 'nullable|numeric|min:100|max:250',
            'weight' => 'nullable|numeric|min:30|max:300',
            'target_weight' => 'nullable|numeric|min:30|max:300',
            'activity_level' => 'nullable|in:sedentary,lightly_active,moderately_active,very_active,extremely_active',
            'dietary_preferences' => 'nullable|array',
            'allergies' => 'nullable|array',
            'bio' => 'nullable|string|max:500',
            'email_notifications' => 'nullable|boolean',
            'push_notifications' => 'nullable|boolean',
        ]);

        $data = $request->only([
            'name', 'email', 'phone', 'birth_date', 'gender', 
            'height', 'weight', 'target_weight', 'activity_level', 
            'dietary_preferences', 'allergies', 'bio'
        ]);
        
        // Обработка чекбоксов уведомлений
        $data['email_notifications'] = $request->boolean('email_notifications');
        $data['push_notifications'] = $request->boolean('push_notifications');

        $user->update($data);

        return back()->with('success', 'Профиль успешно обновлен');
    }

    /**
     * Получить день меню для конкретной даты
     */
    private function getDayForDate($user, $date)
    {
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
            ->with(['menu', 'meals.recipe'])
            ->first();
    }

    /**
     * Фильтрация меню по подписке пользователя
     */
    private function filterMenuBySubscription($query, $user)
    {
        $subscription = $user->activeSubscription()->with('plan')->first();
        
        // Root-пользователи имеют доступ ко всему контенту
        if ($user->hasRole('root')) {
            return; // Без фильтрации — доступно всё
        }

        if (!$subscription) {
            // Только демо контент для пользователей без подписки
            $query->where('type', 'demo');
            return;
        }

        $plan = $subscription->plan;
        
        // Пробная подписка - ограниченный контент
        if ($plan->type === 'trial') {
            $query->whereIn('type', ['trial', 'current']);
        }
        // Месячная подписка - текущие меню
        elseif ($plan->type === 'monthly' && !str_contains($plan->slug, 'personal')) {
            $query->where('type', 'current');
        }
        // Годовая подписка - текущие + архивы + ранний доступ
        elseif ($plan->type === 'yearly' && !str_contains($plan->slug, 'personal')) {
            $query->whereIn('type', ['current', 'archive', 'early']);
        }
        // Персональная подписка (месячная или годовая) - все + персональные
        elseif (str_contains($plan->slug, 'personal')) {
            $query->whereIn('type', ['current', 'archive', 'early', 'personal'])
                  ->where(function($q) use ($user) {
                      $q->where('type', '!=', 'personal')
                        ->orWhere('user_id', $user->id);
                  });
        }
        // Fallback - только текущие
        else {
            $query->where('type', 'current');
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
