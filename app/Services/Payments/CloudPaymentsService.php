<?php

namespace App\Services\Payments;

use App\Models\Payment;
use RuntimeException;

class CloudPaymentsService
{
    public function __construct(private $sdk, private string $apiSecret)
    {
    }

    public function createPayment(array $data): Payment
    {
        $response = $this->sdk->createPayment($data);

        return Payment::create([
            'user_id' => $data['user_id'],
            'provider' => 'cloudpayments',
            'external_id' => $response['TransactionId'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'RUB',
            'status' => $response['Status'] ?? 'pending',
            'payload' => $response,
        ]);
    }

    public function handleWebhook(array $payload, string $rawBody, string $signature): void
    {

        $expected = base64_encode(hash_hmac('sha256', $rawBody, $this->apiSecret, true));

        if (!hash_equals($expected, $signature)) {
            throw new RuntimeException('Invalid signature');
        }

        $payment = Payment::where('external_id', $payload['TransactionId'] ?? null)->first();

        if ($payment) {
            $update = [
                'status' => $payload['Status'] ?? $payment->status,
                'payload' => $payload,
            ];

            if (($payload['Status'] ?? '') === 'Completed') {
                $update['paid_at'] = now();
            }

            $payment->update($update);
        }
    }
}
