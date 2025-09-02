<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\UserSubscription;
use YooKassa\Client as YooKassaClient;
use YooKassa\Model\Confirmation\ConfirmationRedirect;
use YooKassa\Model\MonetaryAmount;
use YooKassa\Model\PaymentData\PaymentDataBankCard;
use YooKassa\Request\Payments\CreatePaymentRequest;
use RuntimeException;
use Illuminate\Support\Facades\Log;

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
            $request = CreatePaymentRequest::builder()
                ->setAmount($data['amount'])
                ->setCurrency($data['currency'] ?? 'RUB')
                ->setDescription($data['description'] ?? 'Оплата подписки RawPlan')
                ->setConfirmation([
                    'type' => 'redirect',
                    'return_url' => $data['return_url'] ?? route('dashboard.index'),
                ])
                ->setCapture(true)
                ->setMetadata($data['metadata'] ?? [])
                ->build();

            $response = $this->sdk->createPayment($request);

            return Payment::create([
                'user_id' => $data['user_id'],
                'subscription_id' => $data['subscription_id'] ?? null,
                'provider' => 'yookassa',
                'external_id' => $response->getId(),
                'amount' => $response->getAmount()->getValue(),
                'currency' => $response->getAmount()->getCurrency(),
                'status' => $response->getStatus(),
                'description' => $data['description'] ?? 'Оплата подписки RawPlan',
                'provider_data' => [
                    'confirmation_url' => $response->getConfirmation()->getConfirmationUrl(),
                    'payment_method' => $response->getPaymentMethod()?->getType(),
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
        $newStatus = $paymentData['status'] ?? $payment->status;

        $update = [
            'status' => $newStatus,
            'provider_data' => array_merge($payment->provider_data ?? [], $paymentData),
        ];

        // Обновляем даты в зависимости от статуса
        switch ($newStatus) {
            case 'succeeded':
                $update['paid_at'] = now();
                break;
            case 'canceled':
                $update['failed_at'] = now();
                $update['failure_reason'] = $paymentData['cancellation_details']['reason'] ?? 'Платеж отменен';
                break;
        }

        $payment->update($update);

        // Обновляем подписку при успешной оплате
        if ($newStatus === 'succeeded' && $oldStatus !== 'succeeded' && $payment->subscription_id) {
            $this->activateSubscription($payment);
        }

        Log::info('Payment status updated', [
            'payment_id' => $payment->id,
            'external_id' => $payment->external_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);
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
