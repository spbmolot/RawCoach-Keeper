<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\UserSubscription;
use App\Services\Payments\YooKassaService;
use App\Services\Payments\CloudPaymentsService;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PaymentService
{
    public function __construct(
        private YooKassaService $yooKassaService,
        private CloudPaymentsService $cloudPaymentsService
    ) {
    }

    /**
     * Создание платежа через выбранного провайдера
     */
    public function createPayment(array $data): Payment
    {
        $provider = $data['provider'] ?? config('payments.default_provider');

        if (!$provider || !config("payments.providers.{$provider}.enabled")) {
            throw new RuntimeException('Платежный провайдер не настроен');
        }

        return match ($provider) {
            'yookassa' => $this->yooKassaService->createPayment($data),
            'cloudpayments' => $this->cloudPaymentsService->createPayment($data),
            default => throw new RuntimeException("Неподдерживаемый провайдер: {$provider}"),
        };
    }

    /**
     * Создание платежа для подписки
     */
    public function createSubscriptionPayment(UserSubscription $subscription, array $additionalData = []): Payment
    {
        $user = $subscription->user;
        $plan = $subscription->plan;

        $paymentData = array_merge([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'amount' => $plan->price,
            'currency' => $plan->currency ?? 'RUB',
            'description' => "Оплата подписки '{$plan->name}' для {$user->name}",
            'email' => $user->email,
            'metadata' => [
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
                'user_id' => $user->id,
            ],
        ], $additionalData);

        return $this->createPayment($paymentData);
    }

    /**
     * Получение информации о платеже
     */
    public function getPayment(Payment $payment): ?array
    {
        return match ($payment->provider) {
            'yookassa' => $this->yooKassaService->getPayment($payment->external_id),
            'cloudpayments' => $this->cloudPaymentsService->getPayment($payment->external_id),
            default => null,
        };
    }

    /**
     * Возврат платежа
     */
    public function refundPayment(Payment $payment, float $amount = null, string $reason = null): bool
    {
        $refundAmount = $amount ?? $payment->amount;

        $success = match ($payment->provider) {
            'yookassa' => $this->yooKassaService->refundPayment($payment->external_id, $refundAmount, $reason),
            'cloudpayments' => $this->cloudPaymentsService->refundPayment($payment->external_id, $refundAmount, $reason),
            default => false,
        };

        if ($success) {
            $payment->update([
                'status' => 'refunded',
                'refunded_at' => now(),
                'failure_reason' => $reason,
            ]);

            // Деактивируем подписку при возврате
            if ($payment->subscription_id) {
                $this->deactivateSubscription($payment);
            }

            Log::info('Payment refunded successfully', [
                'payment_id' => $payment->id,
                'amount' => $refundAmount,
                'reason' => $reason,
            ]);
        }

        return $success;
    }

    /**
     * Обработка webhook от провайдера
     */
    public function handleWebhook(string $provider, array $payload, string $rawBody, string $signature): void
    {
        match ($provider) {
            'yookassa' => $this->yooKassaService->handleWebhook($payload, $rawBody, $signature),
            'cloudpayments' => $this->cloudPaymentsService->handleWebhook($payload, $rawBody, $signature),
            default => throw new RuntimeException("Неподдерживаемый провайдер: {$provider}"),
        };
    }

    /**
     * Получение статистики платежей
     */
    public function getPaymentStats(array $filters = []): array
    {
        $query = Payment::query();

        // Применяем фильтры
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['provider'])) {
            $query->where('provider', $filters['provider']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Агрегатные запросы вместо загрузки всех записей в память
        $totals = (clone $query)->selectRaw("
            COUNT(*) as total_count,
            COALESCE(SUM(amount), 0) as total_amount,
            COUNT(CASE WHEN status = 'paid' THEN 1 END) as succeeded_count,
            COALESCE(SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END), 0) as succeeded_amount,
            COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_count,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
            COUNT(CASE WHEN status = 'refunded' THEN 1 END) as refunded_count,
            COALESCE(SUM(CASE WHEN status = 'refunded' THEN amount ELSE 0 END), 0) as refunded_amount
        ")->first();

        $byProvider = (clone $query)->selectRaw("
            provider,
            COUNT(*) as count,
            COALESCE(SUM(amount), 0) as amount,
            COALESCE(SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END), 0) as succeeded_amount
        ")->groupBy('provider')->get()->keyBy('provider')->map(fn($row) => [
            'count' => (int) $row->count,
            'amount' => (float) $row->amount,
            'succeeded_amount' => (float) $row->succeeded_amount,
        ]);

        return [
            'total_count' => (int) $totals->total_count,
            'total_amount' => (float) $totals->total_amount,
            'succeeded_count' => (int) $totals->succeeded_count,
            'succeeded_amount' => (float) $totals->succeeded_amount,
            'failed_count' => (int) $totals->failed_count,
            'pending_count' => (int) $totals->pending_count,
            'refunded_count' => (int) $totals->refunded_count,
            'refunded_amount' => (float) $totals->refunded_amount,
            'by_provider' => $byProvider,
            'conversion_rate' => $totals->total_count > 0
                ? round(($totals->succeeded_count / $totals->total_count) * 100, 2)
                : 0,
        ];
    }

    /**
     * Получение активных подписок пользователя
     */
    public function getUserActiveSubscriptions(int $userId): array
    {
        return UserSubscription::where('user_id', $userId)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->with(['plan', 'payments'])
            ->get()
            ->toArray();
    }

    /**
     * Проверка доступа пользователя к контенту
     */
    public function hasContentAccess(int $userId, string $contentType = null): bool
    {
        $activeSubscriptions = UserSubscription::where('user_id', $userId)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->with('plan')
            ->get();

        if ($activeSubscriptions->isEmpty()) {
            return false;
        }

        // Если не указан тип контента, проверяем общий доступ
        if (!$contentType) {
            return true;
        }

        // Проверяем доступ к конкретному типу контента
        foreach ($activeSubscriptions as $subscription) {
            $planFeatures = $subscription->plan->features ?? [];
            
            if (in_array($contentType, $planFeatures) || in_array('all_content', $planFeatures)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Деактивация подписки при возврате платежа
     */
    private function deactivateSubscription(Payment $payment): void
    {
        $subscription = UserSubscription::find($payment->subscription_id);
        
        if ($subscription && $subscription->status === 'active') {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => 'Возврат платежа',
            ]);

            Log::info('Subscription deactivated due to refund', [
                'subscription_id' => $subscription->id,
                'payment_id' => $payment->id,
            ]);
        }
    }

    /**
     * Автоматическое продление подписок
     */
    public function processAutoRenewals(int $days, bool $dryRun = false): array
    {
        $result = [
            'found' => 0,
            'processed' => 0,
            'errors' => 0,
            'skipped' => 0,
            'details' => [],
        ];

        $expiring = UserSubscription::where('status', 'active')
            ->where('auto_renew', true)
            ->where('ends_at', '<=', now()->addDays($days))
            ->where('ends_at', '>', now())
            ->with(['user', 'plan', 'scheduledPlan'])
            ->get();

        $result['found'] = $expiring->count();

        foreach ($expiring as $subscription) {
            // Определяем план для продления (текущий или запланированный)
            $renewalPlan = $subscription->hasScheduledPlanChange() 
                ? $subscription->scheduledPlan 
                : $subscription->plan;

            if ($dryRun) {
                $result['skipped']++;
                $planInfo = $subscription->hasScheduledPlanChange()
                    ? "смена на {$renewalPlan->name}, сумма {$renewalPlan->price}"
                    : "продление {$renewalPlan->name}, сумма {$renewalPlan->price}";
                $result['details'][] = [
                    'subscription_id' => $subscription->id,
                    'success' => true,
                    'message' => "Будет создан платеж: {$planInfo}",
                ];
                continue;
            }

            try {
                // Если есть запланированная смена плана - применяем её
                if ($subscription->hasScheduledPlanChange()) {
                    $subscription->applyScheduledPlanChange();
                    $subscription->refresh();
                    
                    Log::info('Scheduled plan change applied', [
                        'subscription_id' => $subscription->id,
                        'new_plan_id' => $renewalPlan->id,
                    ]);
                }

                $payment = $this->createSubscriptionPayment($subscription, [
                    'description' => 'Автопродление подписки ' . $renewalPlan->name,
                ]);

                $result['processed']++;
                $result['details'][] = [
                    'subscription_id' => $subscription->id,
                    'success' => true,
                    'message' => 'Платеж создан: #' . $payment->id . ' на сумму ' . $renewalPlan->price,
                ];

                Log::info('Auto-renewal payment created', [
                    'subscription_id' => $subscription->id,
                    'payment_id' => $payment->id,
                    'plan_id' => $renewalPlan->id,
                ]);
            } catch (\Exception $e) {
                $result['errors']++;
                $result['details'][] = [
                    'subscription_id' => $subscription->id,
                    'success' => false,
                    'message' => 'Ошибка: ' . $e->getMessage(),
                ];

                Log::error('Auto-renewal failed', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $result;
    }
}
