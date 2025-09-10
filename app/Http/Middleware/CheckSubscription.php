<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $requiredAccess = 'current'): Response
    {
        $user = $request->user();

        // Если пользователь не авторизован
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Для доступа к этому контенту необходимо войти в систему.');
        }

        // Проверяем активную подписку
        $activeSubscription = $user->activeSubscription()->with('plan')->first();

        if (!$activeSubscription) {
            return redirect()->route('plans.index')
                ->with('error', 'Для доступа к этому контенту необходима активная подписка.');
        }

        // Проверяем права доступа в зависимости от типа контента
        switch ($requiredAccess) {
            case 'current':
                // Доступ к текущему контенту - есть у всех подписчиков
                if ($this->hasCurrentAccess($user, $activeSubscription)) {
                    return $next($request);
                }
                break;

            case 'archive':
                // Доступ к архиву - только Premium и Personal
                if ($this->hasArchiveAccess($user, $activeSubscription)) {
                    return $next($request);
                }
                break;

            case 'early':
                // Ранний доступ - только Premium и Personal
                if ($this->hasEarlyAccess($user, $activeSubscription)) {
                    return $next($request);
                }
                break;

            case 'personal':
                // Персональные планы - только Personal
                if ($this->hasPersonalAccess($user, $activeSubscription)) {
                    return $next($request);
                }
                break;

            case 'trial':
                // Пробный доступ - для всех, включая trial
                if ($this->hasTrialAccess($user, $activeSubscription)) {
                    return $next($request);
                }
                break;

            default:
                // По умолчанию проверяем текущий доступ
                if ($this->hasCurrentAccess($user, $activeSubscription)) {
                    return $next($request);
                }
        }

        // Если доступ запрещен
        return $this->redirectToUpgrade($requiredAccess);
    }

    /**
     * Проверка доступа к текущему контенту
     */
    private function hasCurrentAccess($user, $subscription): bool
    {
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $plan = $subscription->plan;
        
        // Все активные планы дают доступ к текущему контенту
        return in_array($plan->type, ['monthly', 'yearly', 'personal', 'trial']);
    }

    /**
     * Проверка доступа к архиву
     */
    private function hasArchiveAccess($user, $subscription): bool
    {
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $plan = $subscription->plan;
        
        // Только Premium (yearly) и Personal планы дают доступ к архиву
        return in_array($plan->type, ['yearly', 'personal']) || 
               ($plan->limits && ($plan->limits['archive_access'] ?? false));
    }

    /**
     * Проверка раннего доступа
     */
    private function hasEarlyAccess($user, $subscription): bool
    {
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $plan = $subscription->plan;
        
        // Только Premium и Personal планы дают ранний доступ
        return in_array($plan->type, ['yearly', 'personal']) || 
               ($plan->limits && ($plan->limits['early_access'] ?? false));
    }

    /**
     * Проверка доступа к персональным планам
     */
    private function hasPersonalAccess($user, $subscription): bool
    {
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $plan = $subscription->plan;
        
        // Только Personal план дает доступ к персональным планам
        return $plan->type === 'personal' || 
               ($plan->limits && ($plan->limits['personal_plans'] ?? false));
    }

    /**
     * Проверка пробного доступа
     */
    private function hasTrialAccess($user, $subscription): bool
    {
        if (!$subscription) {
            return false;
        }

        // Пробный доступ доступен всем с любой подпиской, включая trial
        return $subscription->isActive() || $subscription->isInTrial();
    }

    /**
     * Перенаправление на страницу обновления подписки
     */
    private function redirectToUpgrade(string $requiredAccess)
    {
        $messages = [
            'archive' => 'Для доступа к архиву планов необходима подписка Premium или Индивидуальная.',
            'early' => 'Ранний доступ к новым планам доступен только подписчикам Premium и Индивидуальной подписки.',
            'personal' => 'Персональные планы питания доступны только с Индивидуальной подпиской.',
            'current' => 'Для доступа к этому контенту необходима активная подписка.',
        ];

        $message = $messages[$requiredAccess] ?? $messages['current'];

        return redirect()->route('plans.index')
            ->with('error', $message)
            ->with('upgrade_required', $requiredAccess);
    }
}
