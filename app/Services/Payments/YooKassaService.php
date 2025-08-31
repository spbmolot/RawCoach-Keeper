<?php

namespace App\Services\Payments;

use YooKassa\Client;
use YooKassa\Model\NotificationEventType;

class YooKassaService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuth(
            config('services.yookassa.shop_id'),
            config('services.yookassa.secret_key')
        );
    }

    public function createPayment(array $data): array
    {
        return $this->client->createPayment([
            'amount' => [
                'value' => $data['amount'],
                'currency' => $data['currency'] ?? 'RUB',
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => $data['return_url'],
            ],
            'description' => $data['description'] ?? '',
        ], uniqid('', true));
    }

    public function handleWebhook(array $payload): void
    {
        if (($payload['event'] ?? null) === NotificationEventType::PAYMENT_SUCCEEDED) {
            // handle successful payment
        }
    }
}
