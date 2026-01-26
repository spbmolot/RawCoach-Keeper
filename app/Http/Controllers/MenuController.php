<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\Day;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    /**
     * Каталог всех меню
     */
    public function index(Request $request)
    {
        $menus = Cache::remember('menus_published', 600, function () {
            return Menu::where('is_published', true)
                ->with('days')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->paginate(12);
        });
        
        return view('menus.index', compact('menus'));
    }

    /**
     * Показать конкретное меню
     */
    public function show(Menu $menu)
    {
        if (!$menu->is_published) {
            abort(404, 'Меню не найдено');
        }
        
        $menu->load(['days.meals.recipe']);
        
        return view('menus.show', compact('menu'));
    }

    /**
     * Экспорт меню в PDF
     */
    public function export(Menu $menu, Request $request)
    {
        $user = auth()->user();
        
        // Проверяем права на экспорт
        if (!$user || !$user->hasPermissionTo('export.menus')) {
            abort(403, 'Недостаточно прав для экспорта');
        }
        
        if (!$this->canAccessMenu($user, $menu)) {
            abort(403, 'Нет доступа к этому меню');
        }
        
        $format = $request->get('format', 'pdf');
        
        $days = $menu->days()
            ->with(['recipes.nutrition', 'recipes.ingredients.ingredient'])
            ->orderBy('date')
            ->get();
        
        $filename = "menu-{$menu->slug}-{$menu->period_start->format('Y-m')}.{$format}";
        
        if ($format === 'pdf') {
            // return PDF::loadView('exports.menu-pdf', compact('menu', 'days'))->download($filename);
        } else {
            // return Excel::download(new MenuExport($menu, $days), $filename);
        }
        
        return back()->with('success', 'Меню экспортировано');
    }

    /**
     * Архив меню
     */
    public function archive(Request $request)
    {
        $user = auth()->user();
        
        if (!$user || !$user->activeSubscription()) {
            return redirect()->route('plans.index')
                ->with('error', 'Для доступа к архиву необходима подписка');
        }
        
        $subscription = $user->activeSubscription();
        
        // Проверяем, есть ли доступ к архиву
        if (!in_array($subscription->plan->slug, ['yearly', 'personal'])) {
            return redirect()->route('plans.upgrade')
                ->with('error', 'Доступ к архиву доступен только для годовой и персональной подписки');
        }
        
        $menus = Menu::where('is_published', true)
            ->where('type', 'archive')
            ->with(['days', 'recipes'])
            ->orderBy('period_start', 'desc')
            ->paginate(12);
        
        return view('menus.archive', compact('menus'));
    }

    /**
     * Раннее меню (для годовых подписчиков)
     */
    public function early()
    {
        $user = auth()->user();
        
        if (!$user || !$user->activeSubscription()) {
            return redirect()->route('plans.index')
                ->with('error', 'Для раннего доступа необходима подписка');
        }
        
        $subscription = $user->activeSubscription();
        
        if (!in_array($subscription->plan->slug, ['yearly', 'personal'])) {
            return redirect()->route('plans.upgrade')
                ->with('error', 'Ранний доступ доступен только для годовой и персональной подписки');
        }
        
        $earlyMenus = Menu::where('is_published', true)
            ->where('type', 'early')
            ->where('period_start', '>', Carbon::now())
            ->with(['days', 'recipes'])
            ->orderBy('period_start', 'asc')
            ->get();
        
        return view('menus.early', compact('earlyMenus'));
    }

    /**
     * Поиск по меню
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100'
        ]);
        
        $query = $request->get('q');
        $user = auth()->user();
        
        $menuQuery = Menu::where('is_published', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        
        // Фильтрация по подписке
        if ($user && $user->activeSubscription()) {
            $this->filterMenuBySubscription($menuQuery, $user);
        } else {
            $menuQuery->where('type', 'demo');
        }
        
        $menus = $menuQuery->with(['days', 'recipes'])
            ->orderBy('period_start', 'desc')
            ->paginate(12);
        
        return view('menus.search', compact('menus', 'query'));
    }

    /**
     * Добавить меню в избранное
     */
    public function favorite(Menu $menu)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Необходима авторизация'], 401);
        }
        
        if (!$this->canAccessMenu($user, $menu)) {
            return response()->json(['error' => 'Нет доступа к этому меню'], 403);
        }
        
        $user->favoriteMenus()->toggle($menu->id);
        
        $isFavorite = $user->favoriteMenus()->where('menu_id', $menu->id)->exists();
        
        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
            'message' => $isFavorite ? 'Меню добавлено в избранное' : 'Меню удалено из избранного'
        ]);
    }

    /**
     * Избранные меню пользователя
     */
    public function favorites()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $favoriteMenus = $user->favoriteMenus()
            ->where('is_published', true)
            ->with(['days', 'recipes'])
            ->orderBy('menu_user.created_at', 'desc')
            ->paginate(12);
        
        return view('menus.favorites', compact('favoriteMenus'));
    }

    /**
     * Проверка доступа к меню
     */
    private function canAccessMenu($user, $menu): bool
    {
        if ($menu->type === 'demo') {
            return true;
        }
        
        if (!$user || !$user->hasActiveSubscription()) {
            return false;
        }
        
        $subscription = $user->activeSubscription()->with('plan')->first();
        $plan = $subscription->plan;
        
        return match($menu->type) {
            'current' => true, // Доступно всем подписчикам
            'archive', 'early' => in_array($plan->slug, ['yearly', 'personal']),
            'personal' => $plan->slug === 'personal' && $menu->user_id === $user->id,
            'trial' => $plan->slug === 'trial',
            default => false
        };
    }

    /**
     * Фильтрация меню по подписке пользователя
     */
    private function filterMenuBySubscription($query, $user)
    {
        $subscription = $user->activeSubscription()->with('plan')->first();
        
        if (!$subscription) {
            $query->where('type', 'demo');
            return;
        }

        $plan = $subscription->plan;
        
        if ($plan->slug === 'monthly') {
            $query->whereIn('type', ['current', 'demo']);
        } elseif ($plan->slug === 'yearly') {
            $query->whereIn('type', ['current', 'archive', 'early', 'demo']);
        } elseif ($plan->slug === 'personal') {
            $query->whereIn('type', ['current', 'archive', 'early', 'demo'])
                  ->orWhere(function($q) use ($user) {
                      $q->where('type', 'personal')->where('user_id', $user->id);
                  });
        } elseif ($plan->slug === 'trial') {
            $query->whereIn('type', ['trial', 'demo']);
        }
    }

    /**
     * Получить доступные типы меню для пользователя
     */
    private function getAvailableMenuTypes($user): array
    {
        $types = ['demo' => 'Демо'];
        
        if ($user && $user->hasActiveSubscription()) {
            $plan = $user->activeSubscription()->with('plan')->first()->plan;
            
            $types['current'] = 'Текущие';
            
            if (in_array($plan->slug, ['yearly', 'personal'])) {
                $types['archive'] = 'Архив';
                $types['early'] = 'Ранний доступ';
            }
            
            if ($plan->slug === 'personal') {
                $types['personal'] = 'Персональные';
            }
            
            if ($plan->slug === 'trial') {
                $types['trial'] = 'Пробные';
            }
        }
        
        return $types;
    }

    /**
     * Получить доступные месяцы
     */
    private function getAvailableMonths(): array
    {
        return [
            1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
            5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
            9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
        ];
    }

    /**
     * Получить доступные годы
     */
    private function getAvailableYears(): array
    {
        $currentYear = Carbon::now()->year;
        $years = [];
        
        for ($year = $currentYear - 2; $year <= $currentYear + 1; $year++) {
            $years[$year] = $year;
        }
        
        return $years;
    }
}
