<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlansController extends Controller
{
    /**
     * Отображение страницы с планами питания
     */
    public function index()
    {
        // Получаем все активные планы, отсортированные по популярности
        $plans = Plan::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('price', 'asc')
            ->get();

        // Получаем текущую подписку пользователя (если авторизован)
        $currentSubscription = null;
        if (Auth::check()) {
            $currentSubscription = UserSubscription::where('user_id', Auth::id())
                ->where('status', 'active')
                ->with('plan')
                ->first();
        }

        return view('plans.index-public', compact('plans', 'currentSubscription'));
    }

    /**
     * Отображение детальной информации о плане
     */
    public function show(Plan $plan)
    {
        // Проверяем, что план активен
        if (!$plan->is_active) {
            abort(404);
        }

        // Получаем текущую подписку пользователя
        $currentSubscription = null;
        if (Auth::check()) {
            $currentSubscription = UserSubscription::where('user_id', Auth::id())
                ->where('status', 'active')
                ->with('plan')
                ->first();
        }

        // Получаем связанные планы для рекомендаций
        $relatedPlans = Plan::where('is_active', true)
            ->where('id', '!=', $plan->id)
            ->limit(3)
            ->get();

        return view('plans.show', compact('plan', 'currentSubscription', 'relatedPlans'));
    }

    /**
     * Сравнение планов
     */
    public function compare(Request $request)
    {
        $planIds = $request->input('plans', []);
        
        if (empty($planIds) || count($planIds) > 3) {
            return redirect()->route('plans.index')
                ->with('error', 'Выберите от 1 до 3 планов для сравнения');
        }

        $plans = Plan::whereIn('id', $planIds)
            ->where('is_active', true)
            ->get();

        if ($plans->count() !== count($planIds)) {
            return redirect()->route('plans.index')
                ->with('error', 'Некоторые планы недоступны');
        }

        return view('plans.compare', compact('plans'));
    }

    /**
     * Страница выбора плана для покупки
     */
    public function choose(Plan $plan)
    {
        if (!$plan->is_active) {
            abort(404, 'План не найден или неактивен');
        }

        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('intended_plan', $plan->id)
                ->with('message', 'Войдите в систему для оформления подписки');
        }

        $user = auth()->user();
        $activeSubscription = $user->getCachedSubscription();

        if ($activeSubscription) {
            if ($activeSubscription->plan_id === $plan->id) {
                return redirect()->route('dashboard')
                    ->with('info', 'У вас уже активна подписка на этот план');
            }

            return view('plans.upgrade', compact('plan', 'activeSubscription'));
        }

        return view('plans.choose', compact('plan'));
    }

    /**
     * Страница обновления плана для авторизованных пользователей
     */
    public function upgrade()
    {
        $user = Auth::user();
        $currentSubscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('plan')
            ->first();

        if (!$currentSubscription) {
            return redirect()->route('plans.index')
                ->with('error', 'У вас нет активной подписки для обновления');
        }

        // Получаем планы, доступные для обновления
        $availablePlans = Plan::where('is_active', true)
            ->where('id', '!=', $currentSubscription->plan_id)
            ->where('price', '>', $currentSubscription->plan->price)
            ->orderBy('price', 'asc')
            ->get();

        return view('plans.upgrade', compact('currentSubscription', 'availablePlans'));
    }
}
