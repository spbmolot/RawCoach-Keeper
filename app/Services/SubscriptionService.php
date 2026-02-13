<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\UserSubscription;
use App\Events\SubscriptionStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Активация подписки после успешной оплаты
     * Единая точка входа для активации - предотвращает дублирование
     */
    public function activateSubscription(Payment $payment): bool
    {
        if (!$payment->subscription_id) {
            Log::warning('Cannot activate subscription: no subscription_id', [
                'payment_id' => $payment->id,
            ]);
            return false;
        }

        $subscription = UserSubscription::find($payment->subscription_id);

        if (!$subscription) {
            Log::warning('Cannot activate subscription: subscription not found', [
                'payment_id' => $payment->id,
                'subscription_id' => $payment->subscription_id,
            ]);
            return false;
        }

        // Проверяем, не активирована ли уже подписка (защита от дублирования)
        if ($subscription->status === 'active') {
            Log::info('Subscription already active, skipping activation', [
                'subscription_id' => $subscription->id,
                'payment_id' => $payment->id,
            ]);
            return true;
        }

        try {
            DB::beginTransaction();

            $subscription->update([
                'status' => 'active',
                'started_at' => $subscription->started_at ?? now(),
            ]);

            // Если использовался купон, увеличиваем счетчик
            if ($payment->coupon_id && $payment->coupon) {
                $payment->coupon->increment('used_count');
                
                $payment->coupon->couponUsages()->create([
                    'user_id' => $payment->user_id,
                    'payment_id' => $payment->id,
                    'used_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            // Dispatch event ПОСЛЕ успешного коммита
            event(new SubscriptionStatusChanged($subscription->fresh(), 'active'));

            Log::info('Subscription activated successfully', [
                'subscription_id' => $subscription->id,
                'payment_id' => $payment->id,
                'user_id' => $subscription->user_id,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to activate subscription', [
                'subscription_id' => $subscription->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Деактивация подписки при неудачном платеже
     */
    public function deactivateSubscription(Payment $payment, string $reason = 'Платеж не прошел'): bool
    {
        if (!$payment->subscription_id) {
            return false;
        }

        $subscription = UserSubscription::find($payment->subscription_id);

        if (!$subscription) {
            return false;
        }

        // Не деактивируем уже отменённые подписки
        if (in_array($subscription->status, ['cancelled', 'expired'])) {
            return true;
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        event(new SubscriptionStatusChanged($subscription->fresh(), 'cancelled'));

        Log::info('Subscription deactivated', [
            'subscription_id' => $subscription->id,
            'payment_id' => $payment->id,
            'reason' => $reason,
        ]);

        return true;
    }
}
