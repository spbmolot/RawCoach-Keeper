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

        try {
            $this->service->handleWebhook($payload, $rawBody, (string) $signature);
        } catch (RuntimeException $e) {
            Log::error('CloudPayments webhook error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json(['code' => 13], 200);
        }

        return response()->json(['code' => 0]);
    }
}
