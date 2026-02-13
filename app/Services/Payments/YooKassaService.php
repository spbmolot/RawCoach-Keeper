<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\UserSubscription;
use App\Services\SubscriptionService;
use App\Events\PaymentProcessed;
use App\Mail\PaymentFailed;
use YooKassa\Client as YooKassaClient;
use RuntimeException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class YooKassaService
{
    public function __construct(
        private YooKassaClient $sdk, 
        private string $webhookSecret
    ) {
    }

    /**
     * Создание платежа
     */
    public function createPayment(array $data): Payment
    {
        try {
            // Строим простой запрос (совместимо со стубом клиента)
            $request = [
                'amount' => [
                    'value' => number_format($data['amount'], 2, '.', ''),
                    'currency' => $data['currency'] ?? 'RUB',
                ],
                'description' => $data['description'] ?? 'Оплата подписки RawPlan',
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => $data['return_url'] ?? route('dashboard'),
                ],
                'capture' => true,
                'metadata' => $data['metadata'] ?? [],
            ];

            $response = $this->sdk->createPayment($request);

            // Так как используем заглушки SDK, полагаемся на входные данные для суммы/валюты
            return Payment::create([
                'user_id' => $data['user_id'],
                'subscription_id' => $data['subscription_id'] ?? null,
                'provider' => 'yookassa',
                'external_id' => $response->id ?? ('stub_' . uniqid()),
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'RUB',
                'status' => 'pending',
                'description' => $data['description'] ?? 'Оплата подписки RawPlan',
                'payload' => [
                    'request' => $request,
                    'confirmation_url' => $response->confirmation->confirmation_url ?? null,
                ],
                'metadata' => $data['metadata'] ?? [],
            ]);
        } catch (\Exception $e) {
            Log::error('YooKassa payment creation failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw new RuntimeException('Ошибка создания платежа: ' . $e->getMessage());
        }
    }

    /**
     * Получение информации о платеже
     */
    public function getPayment(string $paymentId): ?array
    {
        try {
            $response = $this->sdk->getPaymentInfo($paymentId);
            return $response->toArray();
        } catch (\Exception $e) {
            Log::error('YooKassa get payment failed', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Возврат платежа
     */
    public function refundPayment(string $paymentId, float $amount, string $reason = null): bool
    {
        try {
            $request = [
                'amount' => [
                    'value' => number_format($amount, 2, '.', ''),
                    'currency' => 'RUB',
                ],
                'payment_id' => $paymentId,
            ];

            if ($reason) {
                $request['description'] = $reason;
            }

            $response = $this->sdk->createRefund($request);
            
            return $response->getStatus() === 'succeeded';
        } catch (\Exception $e) {
            Log::error('YooKassa refund failed', [
                'payment_id' => $paymentId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Обработка webhook
     */
    public function handleWebhook(array $payload, string $rawBody, string $signature): void
    {
        $expected = hash_hmac('sha256', $rawBody, $this->webhookSecret);

        if (!hash_equals($expected, $signature)) {
            throw new RuntimeException('Invalid signature');
        }

        $paymentData = $payload['object'] ?? [];
        $payment = Payment::where('external_id', $paymentData['id'] ?? null)->first();

        if (!$payment) {
            Log::warning('Payment not found for webhook', ['payload' => $payload]);
            return;
        }

        $this->updatePaymentStatus($payment, $paymentData);
    }

    /**
     * Обновление статуса платежа
     */
    private function updatePaymentStatus(Payment $payment, array $paymentData): void
    {
        $oldStatus = $payment->status;
        $ykStatus = $paymentData['status'] ?? $payment->status;
        $newStatus = match ($ykStatus) {
            'succeeded' => 'paid',
            'canceled' => 'cancelled',
            default => 'pending',
        };

        $update = [
            'status' => $newStatus,
            'payload' => array_merge($payment->payload ?? [], $paymentData),
        ];

        // Обновляем даты в зависимости от статуса
        switch ($newStatus) {
            case 'paid':
                $update['paid_at'] = now();
                break;
            case 'cancelled':
                $update['failed_at'] = now();
                $update['failure_reason'] = $paymentData['cancellation_details']['reason'] ?? 'Платеж отменен';
                break;
        }

        $payment->update($update);

        // Используем единый сервис для активации подписки (предотвращает дублирование)
        if ($newStatus === 'paid' && $oldStatus !== 'paid' && $payment->subscription_id) {
            $subscriptionService = app(SubscriptionService::class);
            $subscriptionService->activateSubscription($payment);
        }

        // Обработка неудачного платежа
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            $this->handleFailedPayment($payment, $paymentData);
        }

        Log::info('Payment status updated', [
            'payment_id' => $payment->id,
            'external_id' => $payment->external_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);

        // Dispatch payment event (subscription event dispatched by SubscriptionService)
        event(new PaymentProcessed($payment, $newStatus));
    }

    /**
     * Обработка неудачного платежа
     */
    private function handleFailedPayment(Payment $payment, array $paymentData): void
    {
        $subscription = $payment->subscription_id 
            ? UserSubscription::find($payment->subscription_id) 
            : null;

        // Если это платёж за продление и подписка активна - переводим в grace period
        if ($subscription && $subscription->status === 'active' && $subscription->ends_at <= now()) {
            $subscription->update([
                'status' => 'grace_period',
                'grace_period_ends_at' => now()->addDays(3),
            ]);

            Log::info('Subscription moved to grace period due to failed payment', [
                'subscription_id' => $subscription->id,
                'payment_id' => $payment->id,
            ]);
        }

        // Отправляем уведомление пользователю
        try {
            $user = $payment->user;
            if ($user) {
                Mail::to($user)->send(new PaymentFailed($payment));
                
                Log::info('Payment failed notification sent', [
                    'payment_id' => $payment->id,
                    'user_id' => $user->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment failed notification', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
