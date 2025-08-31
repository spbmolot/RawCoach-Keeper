<?php

namespace App\Http\Controllers\Webhook;

use App\Services\Payments\YooKassaService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class YooKassaWebhookController
{
    public function __construct(private YooKassaService $service) {}

    public function __invoke(Request $request): Response
    {
        $signatureHeader = $request->header('Content-Signature', '');
        $signature = str_starts_with($signatureHeader, 'sha256=')
            ? substr($signatureHeader, 7)
            : '';

        $payload = $request->json()->all();
        $rawBody = $request->getContent();

        $this->service->handleWebhook($payload, $rawBody, $signature);

        return response()->noContent();
    }
}
