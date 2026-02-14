<?php

namespace App\Http\Controllers\Webhook;

use App\Services\Payments\YooKassaService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class YooKassaWebhookController
{
    public function __construct(private YooKassaService $service) {}

    public function __invoke(Request $request): Response
    {
        $signatureHeader = $request->header('Content-Signature', '');

        Log::channel('payments')->info('YooKassa webhook received', [
            'event' => $request->json('event'),
            'payment_id' => $request->json('object.id'),
            'status' => $request->json('object.status'),
            'ip' => $request->ip(),
        ]);

        if (!str_starts_with($signatureHeader, 'sha256=')) {
            Log::channel('security')->warning('YooKassa webhook: invalid signature header', [
                'ip' => $request->ip(),
                'header' => $signatureHeader,
            ]);
            return response('', 400);
        }

        $signature = substr($signatureHeader, 7);
        $payload = $request->json()->all();
        $rawBody = $request->getContent();

        try {
            $this->service->handleWebhook($payload, $rawBody, $signature);
            Log::channel('payments')->info('YooKassa webhook processed', [
                'event' => $payload['event'] ?? 'unknown',
                'payment_id' => $payload['object']['id'] ?? null,
            ]);
        } catch (RuntimeException $e) {
            Log::channel('payments')->error('YooKassa webhook error: ' . $e->getMessage(), [
                'event' => $payload['event'] ?? 'unknown',
                'payment_id' => $payload['object']['id'] ?? null,
            ]);

            return response('', 400);
        }

        return response()->noContent();
    }
}
