<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\UserSubscription;
use App\Events\PaymentProcessed;
use App\Events\SubscriptionStatusChanged;
use CloudPayments\Manager as CloudPaymentsManager;
use RuntimeException;
use Illuminate\Support\Facades\Log;

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

        // Обновляем подписку при успешной оплате
        if ($newStatus === 'paid' && $oldStatus !== 'paid' && $payment->subscription_id) {
            $this->activateSubscription($payment);
        }

        Log::info('Payment status updated', [
            'payment_id' => $payment->id,
            'external_id' => $payment->external_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);

        // Dispatch events
        event(new PaymentProcessed($payment, $newStatus));
        if ($newStatus === 'paid' && $payment->subscription_id) {
            $subscription = UserSubscription::find($payment->subscription_id);
            if ($subscription) {
                event(new SubscriptionStatusChanged($subscription, 'active'));
            }
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

    /**
     * Активация подписки после успешной оплаты
     */
    private function activateSubscription(Payment $payment): void
    {
        $subscription = UserSubscription::find($payment->subscription_id);
        
        if ($subscription && $subscription->status === 'pending') {
            $subscription->update([
                'status' => 'active',
                'starts_at' => now(),
            ]);

            Log::info('Subscription activated', [
                'subscription_id' => $subscription->id,
                'payment_id' => $payment->id,
            ]);
        }
    }
}
