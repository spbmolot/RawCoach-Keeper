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

        if (!str_starts_with($signatureHeader, 'sha256=')) {
            return response('', 400);
        }

        $signature = substr($signatureHeader, 7);
        $payload = $request->json()->all();
        $rawBody = $request->getContent();

        try {
            $this->service->handleWebhook($payload, $rawBody, $signature);
        } catch (RuntimeException $e) {
            Log::error('YooKassa webhook error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response('', 400);
        }

        return response()->noContent();
    }
}
