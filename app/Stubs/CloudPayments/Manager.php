<?php

namespace CloudPayments;

/**
 * Заглушка для CloudPayments\Manager
 * Позволяет приложению запускаться без реальных платежных зависимостей
 */
class Manager
{
    public function __construct($publicId = '', $apiSecret = '')
    {
        // Пустой конструктор
    }

    public function createPayment($amount, $currency, $accountId, $description, $email, $data = []): object
    {
        // Возвращаем фиктивный ответ
        return (object) [
            'TransactionId' => 'stub_' . uniqid(),
            'Amount' => $amount,
            'Currency' => $currency,
            'Status' => 'Pending',
            'PaymentUrl' => '/',
        ];
    }

    public function getPayment($transactionId): object
    {
        return (object) [
            'TransactionId' => $transactionId,
            'Status' => 'Pending',
            'Amount' => 0,
            'Currency' => 'RUB',
        ];
    }

    public function refundPayment($transactionId, $amount): object
    {
        return (object) [
            'TransactionId' => 'refund_' . uniqid(),
            'Status' => 'Completed',
            'Amount' => $amount,
        ];
    }
}
