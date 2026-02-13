<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\SubscriptionLog;
use Illuminate\Support\Facades\Log;

class SubscriptionLogService
{
    /**
     * Логировать создание подписки
     */
    public function logCreated(UserSubscription $subscription, ?Payment $payment = null): void
    {
        SubscriptionLog::log(
            'created',
            $subscription->user,
            $subscription,
            $payment,
            [
                'new_status' => $subscription->status,
                'new_plan' => $subscription->plan?->name,
                'amount' => $payment?->amount,
            ]
        );

        Log::channel('subscriptions')->info('Subscription created', [
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'plan' => $subscription->plan?->name,
        ]);
    }

    /**
     * Логировать активацию подписки
     */
    public function logActivated(UserSubscription $subscription, ?Payment $payment = null): void
    {
        SubscriptionLog::log(
            'activated',
            $subscription->user,
            $subscription,
            $payment,
            [
                'new_status' => 'active',
                'new_plan' => $subscription->plan?->name,
                'amount' => $payment?->amount,
            ]
        );

        Log::channel('subscriptions')->info('Subscription activated', [
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'plan' => $subscription->plan?->name,
        ]);
    }

    /**
     * Логировать отмену автопродления
     */
    public function logCancelled(UserSubscription $subscription): void
    {
        SubscriptionLog::log(
            'cancelled',
            $subscription->user,
            $subscription,
            null,
            [
                'old_status' => 'active',
                'new_status' => 'active', // Статус не меняется, только auto_renew
                'metadata' => ['auto_renew' => false],
            ]
        );

        Log::channel('subscriptions')->info('Subscription auto-renew cancelled', [
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'ends_at' => $subscription->ends_at,
        ]);
    }

    /**
     * Логировать изменение статуса
     */
    public function logStatusChange(UserSubscription $subscription, string $oldStatus, string $newStatus): void
    {
        $action = match($newStatus) {
            'expired' => 'expired',
            'grace_period' => 'grace_period',
            'active' => $oldStatus === 'grace_period' ? 'renewed' : 'activated',
            default => 'status_changed',
        };

        SubscriptionLog::log(
            $action,
            $subscription->user,
            $subscription,
            null,
            [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]
        );

        Log::channel('subscriptions')->info('Subscription status changed', [
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);
    }

    /**
     * Логировать смену тарифа
     */
    public function logPlanChange(UserSubscription $subscription, string $oldPlan, string $newPlan, bool $isUpgrade): void
    {
        SubscriptionLog::log(
            $isUpgrade ? 'upgraded' : 'downgraded',
            $subscription->user,
            $subscription,
            null,
            [
                'old_plan' => $oldPlan,
                'new_plan' => $newPlan,
            ]
        );

        Log::channel('subscriptions')->info('Subscription plan changed', [
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'old_plan' => $oldPlan,
            'new_plan' => $newPlan,
            'type' => $isUpgrade ? 'upgrade' : 'downgrade',
        ]);
    }

    /**
     * Логировать запланированную смену тарифа
     */
    public function logScheduledChange(UserSubscription $subscription, string $currentPlan, string $scheduledPlan): void
    {
        SubscriptionLog::log(
            'scheduled_change',
            $subscription->user,
            $subscription,
            null,
            [
                'old_plan' => $currentPlan,
                'new_plan' => $scheduledPlan,
                'metadata' => ['scheduled_at' => now()->toIso8601String()],
            ]
        );

        Log::channel('subscriptions')->info('Subscription plan change scheduled', [
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'current_plan' => $currentPlan,
            'scheduled_plan' => $scheduledPlan,
        ]);
    }

    /**
     * Логировать успешный платёж
     */
    public function logPaymentSuccess(Payment $payment, ?UserSubscription $subscription = null): void
    {
        SubscriptionLog::log(
            'payment_success',
            $payment->user,
            $subscription,
            $payment,
            [
                'amount' => $payment->amount,
                'metadata' => [
                    'provider' => $payment->provider,
                    'external_id' => $payment->external_id,
                ],
            ]
        );

        Log::channel('subscriptions')->info('Payment successful', [
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'amount' => $payment->amount,
        ]);
    }

    /**
     * Логировать неудачный платёж
     */
    public function logPaymentFailed(Payment $payment, ?UserSubscription $subscription = null): void
    {
        SubscriptionLog::log(
            'payment_failed',
            $payment->user,
            $subscription,
            $payment,
            [
                'amount' => $payment->amount,
                'metadata' => [
                    'provider' => $payment->provider,
                    'failure_reason' => $payment->failure_reason,
                ],
            ]
        );

        Log::channel('subscriptions')->warning('Payment failed', [
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'amount' => $payment->amount,
            'reason' => $payment->failure_reason,
        ]);
    }

    /**
     * Логировать назначение роли
     */
    public function logRoleAssigned(User $user, string $role, ?UserSubscription $subscription = null): void
    {
        SubscriptionLog::log(
            'role_assigned',
            $user,
            $subscription,
            null,
            [
                'metadata' => ['role' => $role],
            ]
        );

        Log::channel('subscriptions')->info('Role assigned', [
            'user_id' => $user->id,
            'role' => $role,
        ]);
    }

    /**
     * Логировать снятие роли
     */
    public function logRoleRemoved(User $user, string $role, ?UserSubscription $subscription = null): void
    {
        SubscriptionLog::log(
            'role_removed',
            $user,
            $subscription,
            null,
            [
                'metadata' => ['role' => $role],
            ]
        );

        Log::channel('subscriptions')->info('Role removed', [
            'user_id' => $user->id,
            'role' => $role,
        ]);
    }
}
