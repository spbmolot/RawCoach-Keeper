<?php

namespace App\Http\Controllers\Webhook;

use App\Services\Payments\CloudPaymentsService;
codex/implement-cloudpayments-webhook-handling
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Payments\CloudPaymentsService;

class CloudPaymentsWebhookController
{
codex/implement-cloudpayments-webhook-handling
    public function __construct(private CloudPaymentsService $service) {}

    public function __invoke(Request $request): JsonResponse
    {
        $signature = $request->header('Content-HMAC');
        $apiKey = config('services.cloudpayments.api_key');
        $expected = base64_encode(hash_hmac('sha256', $request->getContent(), $apiKey, true));

        if (! hash_equals($expected, (string) $signature)) {
            return response()->json(['code' => 13], Response::HTTP_FORBIDDEN);
        }

        $payload = $request->json()->all();
        $this->service->handleWebhook($payload);

        return response()->json(['code' => 0]);
    }
}
