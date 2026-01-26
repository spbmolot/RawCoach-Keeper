<?php

namespace YooKassa;

use Illuminate\Support\Facades\Log;

/**
 * Заглушка для YooKassa\Client
 * Позволяет приложению запускаться без реальных платежных зависимостей
 * В sandbox режиме эмулирует поведение реального API
 */
class Client
{
    private ?string $shopId = null;
    private ?string $secretKey = null;
    private static array $payments = [];

    public function __construct()
    {
        // Пустой конструктор
    }

    public function setAuth($shopId, $secretKey): void
    {
        $this->shopId = $shopId;
        $this->secretKey = $secretKey;
        
        Log::info('YooKassa Stub: Auth set', ['shop_id' => $shopId]);
    }

    public function createPayment($request): object
    {
        $paymentId = 'test_' . uniqid();
        $amount = $request['amount']['value'] ?? 0;
        $currency = $request['amount']['currency'] ?? 'RUB';
        $returnUrl = $request['confirmation']['return_url'] ?? '/';
        
        // Сохраняем платёж в памяти для тестирования
        self::$payments[$paymentId] = [
            'id' => $paymentId,
            'status' => 'pending',
            'amount' => ['value' => $amount, 'currency' => $currency],
            'description' => $request['description'] ?? '',
            'metadata' => $request['metadata'] ?? [],
            'created_at' => now()->toIso8601String(),
        ];
        
        // URL для тестовой оплаты
        $confirmationUrl = route('payment.test.confirm', ['payment_id' => $paymentId]);
        
        Log::info('YooKassa Stub: Payment created', [
            'payment_id' => $paymentId,
            'amount' => $amount,
            'currency' => $currency,
        ]);

        return (object) [
            'id' => $paymentId,
            'amount' => (object) ['value' => $amount, 'currency' => $currency],
            'status' => 'pending',
            'confirmation' => (object) ['confirmation_url' => $confirmationUrl],
            'payment_method' => null,
            'created_at' => now()->toIso8601String(),
        ];
    }

    public function getPaymentInfo($paymentId): object
    {
        $payment = self::$payments[$paymentId] ?? null;
        
        if ($payment) {
            return (object) [
                'id' => $paymentId,
                'status' => $payment['status'],
                'amount' => (object) $payment['amount'],
            ];
        }
        
        return (object) [
            'id' => $paymentId,
            'status' => 'pending',
            'amount' => (object) ['value' => 0, 'currency' => 'RUB'],
        ];
    }

    public function createRefund($request): object
    {
        $refundId = 'refund_' . uniqid();
        
        Log::info('YooKassa Stub: Refund created', [
            'refund_id' => $refundId,
            'payment_id' => $request['payment_id'] ?? null,
            'amount' => $request['amount']['value'] ?? 0,
        ]);
        
        return (object) [
            'id' => $refundId,
            'status' => 'succeeded',
        ];
    }
    
    /**
     * Симуляция успешной оплаты (для тестирования)
     */
    public static function simulateSuccess(string $paymentId): void
    {
        if (isset(self::$payments[$paymentId])) {
            self::$payments[$paymentId]['status'] = 'succeeded';
        }
    }
    
    /**
     * Симуляция отмены платежа (для тестирования)
     */
    public static function simulateCancel(string $paymentId): void
    {
        if (isset(self::$payments[$paymentId])) {
            self::$payments[$paymentId]['status'] = 'canceled';
        }
    }
}
