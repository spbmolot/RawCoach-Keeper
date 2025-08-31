<?php

namespace App\Http\Controllers\Webhook;

use App\Services\Payments\CloudPaymentsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CloudPaymentsWebhookController
{
    public function __construct(private CloudPaymentsService $service) {}

    public function __invoke(Request $request): JsonResponse
    {
        $signature = $request->header('Content-HMAC');
        $payload = $request->json()->all();
        $rawBody = $request->getContent();

        $this->service->handleWebhook($payload, $rawBody, (string) $signature);

        return response()->json(['code' => 0]);
    }
}
