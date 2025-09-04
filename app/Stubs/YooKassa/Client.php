<?php

namespace YooKassa;

/**
 * Заглушка для YooKassa\Client
 * Позволяет приложению запускаться без реальных платежных зависимостей
 */
class Client
{
    public function __construct()
    {
        // Пустой конструктор
    }

    public function setAuth($shopId, $secretKey): void
    {
        // Заглушка для аутентификации
    }

    public function createPayment($request): object
    {
        // Возвращаем фиктивный ответ
        return (object) [
            'id' => 'stub_' . uniqid(),
            'amount' => (object) ['value' => 0, 'currency' => 'RUB'],
            'status' => 'pending',
            'confirmation' => (object) ['confirmation_url' => '/'],
            'payment_method' => null,
        ];
    }

    public function getPaymentInfo($paymentId): object
    {
        return (object) [
            'id' => $paymentId,
            'status' => 'pending',
            'amount' => (object) ['value' => 0, 'currency' => 'RUB'],
        ];
    }

    public function createRefund($request): object
    {
        return (object) [
            'id' => 'refund_' . uniqid(),
            'status' => 'succeeded',
        ];
    }
}
