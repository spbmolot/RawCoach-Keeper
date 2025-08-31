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

        $this->service->handleWebhook($request->json()->all(), $signature);

        return response()->noContent();
    }
}
