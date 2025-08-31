<?php

namespace App\Services\Payments;

use CloudPayments\Manager;

class CloudPaymentsService
{
    protected Manager $manager;

    public function __construct()
    {
        $this->manager = new Manager(
            config('services.cloudpayments.public_id'),
            config('services.cloudpayments.secret_key')
        );
    }

    public function createPayment(array $data): array
    {
        return $this->manager->chargeCard(
            $data['amount'],
            $data['currency'] ?? 'RUB',
            $data['ip_address'],
            $data['name'],
            $data['cryptogram'],
            $data['params'] ?? []
        );
    }

    public function handleWebhook(array $payload): void
    {
        if (($payload['Status'] ?? '') === 'Completed') {
            // handle successful payment
        }
    }
}
