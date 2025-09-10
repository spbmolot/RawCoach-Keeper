<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\UserSubscription;

class PlanController extends Controller
{
    /**
     * Показать все доступные планы
     */
    public function index()
    {
        $plans = Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $user = auth()->user();
        $activeSubscription = $user ? $user->activeSubscription()->with('plan')->first() : null;

        return view('plans.index', compact('plans', 'activeSubscription'));
    }

    /**
     * Показать детали конкретного плана
     */
    public function show(Plan $plan)
    {
        if (!$plan->is_active) {
            abort(404, 'План не найден или неактивен');
        }

        $user = auth()->user();
        $activeSubscription = $user ? $user->activeSubscription()->with('plan')->first() : null;
        
        // Проверяем, есть ли у пользователя уже такой план
        $hasCurrentPlan = $activeSubscription && $activeSubscription->plan_id === $plan->id;

        return view('plans.show', compact('plan', 'activeSubscription', 'hasCurrentPlan'));
    }

    /**
     * Страница выбора плана для покупки
     */
    public function choose(Plan $plan)
    {
        if (!$plan->is_active) {
            abort(404, 'План не найден или неактивен');
        }

        // Проверяем авторизацию
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('intended_plan', $plan->id)
                ->with('message', 'Войдите в систему для оформления подписки');
        }

        $user = auth()->user();
        $activeSubscription = $user->activeSubscription()->with('plan')->first();

        // Если у пользователя уже есть активная подписка
        if ($activeSubscription) {
            // Если это тот же план
            if ($activeSubscription->plan_id === $plan->id) {
                return redirect()->route('dashboard')
                    ->with('info', 'У вас уже активна подписка на этот план');
            }

            // Если хочет перейти на другой план
            return view('plans.upgrade', compact('plan', 'activeSubscription'));
        }

        return view('plans.choose', compact('plan'));
    }

    /**
     * Сравнение планов
     */
    public function compare()
    {
        $plans = Plan::where('is_active', true)
            ->whereIn('type', ['monthly', 'yearly', 'personal'])
            ->orderBy('sort_order')
            ->get();

        return view('plans.compare', compact('plans'));
    }

    /**
     * Страница обновления плана
     */
    public function upgrade(Request $request)
    {
        $user = auth()->user();
        $activeSubscription = $user->activeSubscription()->with('plan')->first();

        if (!$activeSubscription) {
            return redirect()->route('plans.index')
                ->with('error', 'У вас нет активной подписки для обновления');
        }

        $availablePlans = Plan::where('is_active', true)
            ->where('id', '!=', $activeSubscription->plan_id)
            ->orderBy('sort_order')
            ->get();

        return view('plans.upgrade', compact('activeSubscription', 'availablePlans'));
    }
}
