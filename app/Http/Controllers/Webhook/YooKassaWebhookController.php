<?php

namespace App\Http\Controllers\Webhook;

use App\Services\Payments\YooKassaService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class YooKassaWebhookController
{
    public function __invoke(Request $request, YooKassaService $service): Response
    {
        $signature = $request->header('sha256');
        $payload = $request->getContent();
        $secret = config('services.yookassa.webhook_secret');

        if (! $signature || ! $secret || ! hash_equals(hash_hmac('sha256', $payload, $secret), $signature)) {
            return response()->json(['message' => 'Invalid signature'], Response::HTTP_FORBIDDEN);
        }

        try {
            $service->handleWebhook($request->json()->all());
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Processing failed'], Response::HTTP_BAD_REQUEST);
        }

        return response()->noContent();
    }
}
