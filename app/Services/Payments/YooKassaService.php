<?php

namespace App\Services\Payments;

use App\Models\Payment;
use YooKassa\Client as YooKassaClient;
use RuntimeException;

class YooKassaService
{
    public function __construct(private YooKassaClient $sdk, private string $webhookSecret)
    {
    }

    public function createPayment(array $data): Payment
    {
        $response = $this->sdk->createPayment($data);

        return Payment::create([
            'user_id' => $data['user_id'],
            'provider' => 'yookassa',
            'external_id' => $response['id'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'RUB',
            'status' => $response['status'] ?? 'pending',
            'payload' => $response,
        ]);
    }

    public function handleWebhook(array $payload, string $rawBody, string $signature): void
    {
        $expected = hash_hmac('sha256', $rawBody, $this->webhookSecret);

        if (!hash_equals($expected, $signature)) {
            throw new RuntimeException('Invalid signature');
        }

        $payment = Payment::where('external_id', $payload['object']['id'] ?? null)->first();

        if ($payment) {
            $update = [
                'status' => $payload['object']['status'] ?? $payment->status,
                'payload' => $payload,
            ];

            if (($payload['object']['status'] ?? '') === 'succeeded') {
                $update['paid_at'] = now();
            }

            $payment->update($update);
        }
    }
}
