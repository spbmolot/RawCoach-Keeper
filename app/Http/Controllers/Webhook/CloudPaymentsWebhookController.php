<?php

namespace App\Http\Controllers\Webhook;

use App\Services\Payments\CloudPaymentsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CloudPaymentsWebhookController
{
    public function __construct(private CloudPaymentsService $service) {}

    public function __invoke(Request $request): JsonResponse
    {
        $signature = $request->header('Content-HMAC');
        $payload = $request->json()->all();
        $rawBody = $request->getContent();

        Log::channel('payments')->info('CloudPayments webhook received', [
            'transaction_id' => $payload['TransactionId'] ?? null,
            'status' => $payload['Status'] ?? null,
            'amount' => $payload['Amount'] ?? null,
            'ip' => $request->ip(),
        ]);

        if (empty($signature)) {
            Log::channel('security')->warning('CloudPayments webhook: missing HMAC signature', [
                'ip' => $request->ip(),
            ]);
        }

        try {
            $this->service->handleWebhook($payload, $rawBody, (string) $signature);
            Log::channel('payments')->info('CloudPayments webhook processed', [
                'transaction_id' => $payload['TransactionId'] ?? null,
            ]);
        } catch (RuntimeException $e) {
            Log::channel('payments')->error('CloudPayments webhook error: ' . $e->getMessage(), [
                'transaction_id' => $payload['TransactionId'] ?? null,
            ]);

            return response()->json(['code' => 13], 200);
        }

        return response()->json(['code' => 0]);
    }
}
