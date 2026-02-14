<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\Day;
use App\Models\PersonalPlan;
use App\Services\RecipeSwapService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct(
        private RecipeSwapService $swapService
    ) {
        $this->middleware('auth');
    }

    /**
     * Главная страница личного кабинета
     */
    public function index()
    {
        $user = auth()->user();
        $activeSubscription = $user->activeSubscription()->with('plan')->first();
        
        // Получаем текущее меню (с учётом подписки)
        $currentMenu = Menu::where('is_published', true)
            ->where('month', Carbon::now()->month)
            ->where('year', Carbon::now()->year)
            ->where(function($query) use ($user) {
                $this->filterMenuBySubscription($query, $user);
            })
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
        $hasSubscription = $user->hasActiveSubscription();
        $isFreePreview = false;
        
        $todayDay = $this->getDayForDate($user, $today);
        
        // Freemium: если нет подписки и нет меню на сегодня — показываем бесплатный день (day 1)
        if (!$todayDay && !$hasSubscription) {
            $todayDay = $this->getFreeDayPreview($today->month, $today->year);
            $isFreePreview = true;
        }
        
        if (!$todayDay) {
            return view('dashboard.today-empty', compact('hasSubscription'));
        }

        $todayDay->load(['meals.recipe.ingredients']);
        
        // Применяем замены пользователя
        $swaps = $hasSubscription 
            ? $this->swapService->getSwapsForDay($user, $todayDay->id) 
            : collect();
        
        $meals = $this->swapService->applySwaps($todayDay->meals, $swaps);
        
        $recipes = $meals
            ->groupBy('meal_type')
            ->map(fn($meals) => $meals->values());

        return view('dashboard.today', compact('todayDay', 'recipes', 'meals', 'today', 'hasSubscription', 'isFreePreview', 'swaps'));
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
        
        $endDate = $startDate->copy()->addDays(6);
        $hasSubscription = $user->hasActiveSubscription();
        
        // Batch-загрузка всех дней за неделю одним запросом
        $daysMap = $this->getDaysForPeriod($user, $startDate, $endDate);
        
        $weekDays = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $key = $date->month . '-' . $date->day;
            $day = $daysMap[$key] ?? null;
            
            if ($day && $hasSubscription) {
                $swaps = $this->swapService->getSwapsForDay($user, $day->id);
                $this->swapService->applySwaps($day->meals, $swaps);
            }
            
            $weekDays->push([
                'date' => $date,
                'day' => $day,
                'recipes_count' => $day ? $day->meals->count() : 0,
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

        // Batch-загрузка дней за период одним запросом
        $daysMap = $this->getDaysForPeriod($user, $startDate, $endDate);
        
        // Собираем все рецепты из дней (с учётом замен)
        $hasSubscription = $user->hasActiveSubscription();
        $recipes = collect();
        foreach ($daysMap as $day) {
            if ($hasSubscription) {
                $swaps = $this->swapService->getSwapsForDay($user, $day->id);
                $this->swapService->applySwaps($day->meals, $swaps);
            }
            
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
        
        // Batch-загрузка дней за период
        $daysMap = $this->getDaysForPeriod($user, $startDate, $endDate);
        
        // Собираем все рецепты из дней (с учётом замен)
        $hasSubscription = $user->hasActiveSubscription();
        $recipes = collect();
        foreach ($daysMap as $day) {
            if ($hasSubscription) {
                $swaps = $this->swapService->getSwapsForDay($user, $day->id);
                $this->swapService->applySwaps($day->meals, $swaps);
            }
            
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

        // Логируем изменения email отдельно (безопасность)
        if ($user->email !== $data['email']) {
            Log::channel('security')->info('User email changed', [
                'user_id' => $user->id,
                'old_email' => $user->email,
                'new_email' => $data['email'],
                'ip' => request()->ip(),
            ]);
        }

        $user->update($data);

        Log::channel('user-actions')->info('Profile updated', [
            'user_id' => $user->id,
            'fields' => array_keys($data),
            'ip' => request()->ip(),
        ]);

        return back()->with('success', 'Профиль успешно обновлен');
    }

    /**
     * Freemium: получить бесплатный день (day 1) текущего месяца
     */
    private function getFreeDayPreview($month, $year)
    {
        return Day::whereHas('menu', function($query) use ($month, $year) {
                $query->where('month', $month)
                    ->where('year', $year)
                    ->where('is_published', true)
                    ->where('type', 'current');
            })
            ->where('day_number', 1)
            ->with(['menu', 'meals.recipe'])
            ->first();
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
     * Batch-загрузка дней за период (оптимизация N+1)
     * Возвращает коллекцию indexed by "month-dayNumber"
     */
    private function getDaysForPeriod($user, Carbon $startDate, Carbon $endDate)
    {
        // Группируем запрашиваемые дни по month+year
        $periods = collect();
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $key = $current->year . '-' . $current->month;
            if (!$periods->has($key)) {
                $periods[$key] = [
                    'month' => $current->month,
                    'year' => $current->year,
                    'days' => collect(),
                ];
            }
            $periods[$key]['days']->push($current->day);
            $current->addDay();
        }

        $result = collect();
        foreach ($periods as $period) {
            $days = Day::whereHas('menu', function($query) use ($user, $period) {
                    $query->where('month', $period['month'])
                        ->where('year', $period['year'])
                        ->where('is_published', true);
                    $this->filterMenuBySubscription($query, $user);
                })
                ->whereIn('day_number', $period['days']->toArray())
                ->with(['menu', 'meals.recipe.ingredients'])
                ->get();

            foreach ($days as $day) {
                $result[$period['month'] . '-' . $day->day_number] = $day;
            }
        }

        return $result;
    }

    /**
     * Фильтрация меню по подписке пользователя
     */
    private function filterMenuBySubscription($query, $user)
    {
        $subscription = $user->activeSubscription()->with('plan')->first();
        
        // Администраторы имеют доступ ко всему контенту
        if ($user->hasRole('admin')) {
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
     * Генерация списка покупок из рецептов
     */
    private function generateShoppingList($recipes)
    {
        $ingredients = collect();
        
        foreach ($recipes as $recipe) {
            foreach ($recipe->ingredients as $recipeIngredient) {
                $key = mb_strtolower($recipeIngredient->ingredient_name);
                
                if ($ingredients->has($key)) {
                    $ingredients[$key]['amount'] += $recipeIngredient->amount;
                } else {
                    $ingredients[$key] = [
                        'ingredient' => $recipeIngredient,
                        'amount' => $recipeIngredient->amount,
                        'unit' => $recipeIngredient->unit,
                        'category' => $recipeIngredient->category ?? 'other',
                    ];
                }
            }
        }
        
        return $ingredients->groupBy('category');
    }
}
