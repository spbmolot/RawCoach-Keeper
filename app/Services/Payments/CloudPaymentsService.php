<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\UserSubscription;
use App\Services\SubscriptionService;
use App\Events\PaymentProcessed;
use App\Mail\PaymentFailed;
use CloudPayments\Manager as CloudPaymentsManager;
use RuntimeException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CloudPaymentsService
{
    public function __construct(
        private CloudPaymentsManager $sdk, 
        private string $apiSecret
    ) {
    }

    /**
     * Создание платежа
     */
    public function createPayment(array $data): Payment
    {
        try {
            $paymentData = [
                'Amount' => $data['amount'],
                'Currency' => $data['currency'] ?? 'RUB',
                'IpAddress' => request()->ip(),
                'Description' => $data['description'] ?? 'Оплата подписки RawPlan',
                'AccountId' => $data['user_id'],
                'Email' => $data['email'] ?? null,
                'JsonData' => json_encode($data['metadata'] ?? []),
            ];

            $response = $this->sdk->createPayment($paymentData);

            return Payment::create([
                'user_id' => $data['user_id'],
                'subscription_id' => $data['subscription_id'] ?? null,
                'provider' => 'cloudpayments',
                'external_id' => $response['TransactionId'] ?? null,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'RUB',
                'status' => $this->mapStatus($response['Status'] ?? 'pending'),
                'description' => $data['description'] ?? 'Оплата подписки RawPlan',
                'payload' => $response,
                'metadata' => $data['metadata'] ?? [],
            ]);
        } catch (\Exception $e) {
            Log::error('CloudPayments payment creation failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw new RuntimeException('Ошибка создания платежа: ' . $e->getMessage());
        }
    }

    /**
     * Получение информации о платеже
     */
    public function getPayment(string $transactionId): ?array
    {
        try {
            $response = $this->sdk->getPayment(['TransactionId' => $transactionId]);
            return $response['Success'] ? $response['Model'] : null;
        } catch (\Exception $e) {
            Log::error('CloudPayments get payment failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Возврат платежа
     */
    public function refundPayment(string $transactionId, float $amount, string $reason = null): bool
    {
        try {
            $requestData = [
                'TransactionId' => $transactionId,
                'Amount' => $amount,
            ];

            if ($reason) {
                $requestData['JsonData'] = json_encode(['reason' => $reason]);
            }

            $response = $this->sdk->refundPayment($requestData);
            return $response['Success'] ?? false;
        } catch (\Exception $e) {
            Log::error('CloudPayments refund failed', [
                'transaction_id' => $transactionId,
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
        $expected = base64_encode(hash_hmac('sha256', $rawBody, $this->apiSecret, true));

        if (!hash_equals($expected, $signature)) {
            throw new RuntimeException('Invalid signature');
        }

        $payment = Payment::where('external_id', $payload['TransactionId'] ?? null)->first();

        if (!$payment) {
            Log::warning('Payment not found for webhook', ['payload' => $payload]);
            return;
        }

        $this->updatePaymentStatus($payment, $payload);
    }

    /**
     * Обновление статуса платежа
     */
    private function updatePaymentStatus(Payment $payment, array $paymentData): void
    {
        $oldStatus = $payment->status;
        $newStatus = $this->mapStatus($paymentData['Status'] ?? '');

        $update = [
            'status' => $newStatus,
            'payload' => array_merge($payment->payload ?? [], $paymentData),
        ];

        // Обновляем даты в зависимости от статуса
        switch ($newStatus) {
            case 'paid':
                $update['paid_at'] = now();
                break;
            case 'failed':
                $update['failed_at'] = now();
                $update['failure_reason'] = $paymentData['Reason'] ?? 'Платеж не прошел';
                break;
        }

        $payment->update($update);

        // Используем единый сервис для активации подписки (предотвращает дублирование)
        if ($newStatus === 'paid' && $oldStatus !== 'paid' && $payment->subscription_id) {
            $subscriptionService = app(SubscriptionService::class);
            $subscriptionService->activateSubscription($payment);
        }

        // Обработка неудачного платежа
        if (in_array($newStatus, ['failed', 'cancelled']) && !in_array($oldStatus, ['failed', 'cancelled'])) {
            $this->handleFailedPayment($payment);
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
    private function handleFailedPayment(Payment $payment): void
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

    /**
     * Маппинг статусов CloudPayments в наши статусы
     */
    private function mapStatus(string $status): string
    {
        return match ($status) {
            'Completed' => 'paid',
            'Declined' => 'failed',
            'Cancelled' => 'cancelled',
            'Authorized' => 'pending',
            default => 'pending',
        };
    }
}
