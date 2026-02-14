<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Log;

class SubscriptionRoleService
{
    /**
     * Список активных ролей подписчиков
     */
    private const ACTIVE_SUBSCRIBER_ROLES = [
        'subscriber_trial',
        'subscriber_standard',
        'subscriber_premium',
        'subscriber_personal',
    ];

    /**
     * Список всех ролей подписчиков (включая истёкшие)
     */
    private const ALL_SUBSCRIBER_ROLES = [
        'subscriber_trial',
        'subscriber_standard',
        'subscriber_premium',
        'subscriber_personal',
        'subscriber_lapsed',
        'subscriber_expired',
    ];

    /**
     * Роли платных подписок (не trial)
     */
    private const PAID_SUBSCRIBER_ROLES = [
        'subscriber_standard',
        'subscriber_premium',
        'subscriber_personal',
    ];

    /**
     * Синхронизировать роль пользователя на основе активной подписки
     */
    public function syncUserRole(User $user): void
    {
        $activeSubscription = $user->activeSubscription()->with('plan')->first();

        if ($activeSubscription && $activeSubscription->plan && $activeSubscription->plan->role) {
            $this->assignSubscriberRole($user, $activeSubscription->plan->role);
        } else {
            // Нет активной подписки - определяем роль на основе истории
            $this->assignExpiredRole($user);
        }
    }

    /**
     * Назначить роль подписчика
     */
    public function assignSubscriberRole(User $user, string $role): void
    {
        // Удаляем все роли подписчиков
        $this->removeAllSubscriberRoles($user);

        // Назначаем новую роль
        if (in_array($role, self::ALL_SUBSCRIBER_ROLES)) {
            $user->assignRole($role);
            
            Log::info('Subscriber role assigned', [
                'user_id' => $user->id,
                'role' => $role,
            ]);
        }
    }

    /**
     * Назначить роль для пользователя с истёкшей подпиской
     * lapsed = был только trial
     * expired = был платный подписчик
     */
    public function assignExpiredRole(User $user): void
    {
        // Удаляем все активные роли подписчиков
        $this->removeAllSubscriberRoles($user);

        // Проверяем, была ли платная подписка
        $hadPaidSubscription = $user->subscriptions()
            ->whereHas('plan', function($query) {
                $query->where('type', '!=', 'trial');
            })
            ->exists();

        // Проверяем, была ли хоть какая-то подписка (включая trial)
        $hadAnySubscription = $user->subscriptions()->exists();

        if ($hadPaidSubscription) {
            // Был платный подписчик → subscriber_expired (с доступом к истории)
            $user->assignRole('subscriber_expired');
            Log::info('Assigned subscriber_expired role (was paid subscriber)', [
                'user_id' => $user->id,
            ]);
        } elseif ($hadAnySubscription) {
            // Был только trial → subscriber_lapsed (без доступа к истории)
            $user->assignRole('subscriber_lapsed');
            Log::info('Assigned subscriber_lapsed role (was trial only)', [
                'user_id' => $user->id,
            ]);
        }
        // Если не было подписок вообще - остаётся роль 'user'
    }

    /**
     * Удалить все роли подписчиков (включая lapsed/expired)
     */
    public function removeAllSubscriberRoles(User $user): void
    {
        foreach (self::ALL_SUBSCRIBER_ROLES as $role) {
            if ($user->hasRole($role)) {
                $user->removeRole($role);
            }
        }

        Log::info('All subscriber roles removed', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Обработать изменение статуса подписки
     */
    public function handleSubscriptionStatusChange(UserSubscription $subscription, string $newStatus): void
    {
        $user = $subscription->user;
        $plan = $subscription->plan;

        switch ($newStatus) {
            case 'active':
                if ($plan && $plan->role) {
                    $this->assignSubscriberRole($user, $plan->role);
                }
                break;

            case 'cancelled':
            case 'expired':
                // Проверяем, есть ли другие активные подписки
                $otherActiveSubscription = $user->subscriptions()
                    ->where('id', '!=', $subscription->id)
                    ->where('status', 'active')
                    ->where('ends_at', '>', now())
                    ->with('plan')
                    ->first();

                if ($otherActiveSubscription && $otherActiveSubscription->plan && $otherActiveSubscription->plan->role) {
                    $this->assignSubscriberRole($user, $otherActiveSubscription->plan->role);
                } else {
                    // Назначаем роль lapsed или expired в зависимости от истории
                    $this->assignExpiredRole($user);
                }
                break;

            case 'grace_period':
                // В grace period сохраняем роль
                break;

            case 'upgraded':
                // При апгрейде роль назначится новой подпиской
                break;

            default:
                // pending, paused и другие - ничего не делаем
                break;
        }
    }

    /**
     * Синхронизировать роли всех пользователей (для миграции)
     */
    public function syncAllUsersRoles(): array
    {
        $result = [
            'processed' => 0,
            'roles_assigned' => 0,
            'expired_assigned' => 0,
        ];

        User::with(['subscriptions.plan'])->chunk(100, function ($users) use (&$result) {
            foreach ($users as $user) {
                $activeSubscription = $user->activeSubscription()->with('plan')->first();

                if ($activeSubscription && $activeSubscription->plan && $activeSubscription->plan->role) {
                    $this->assignSubscriberRole($user, $activeSubscription->plan->role);
                    $result['roles_assigned']++;
                } else {
                    // Нет активной подписки - назначаем lapsed или expired
                    $hadAnySubscription = $user->subscriptions()->exists();
                    if ($hadAnySubscription) {
                        $this->assignExpiredRole($user);
                        $result['expired_assigned']++;
                    }
                }

                $result['processed']++;
            }
        });

        return $result;
    }
}
