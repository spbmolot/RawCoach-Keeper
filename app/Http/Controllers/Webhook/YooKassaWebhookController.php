<?php

namespace App\Http\Controllers\Webhook;

use App\Services\Payments\YooKassaService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class YooKassaWebhookController
{
    public function __construct(private YooKassaService $service)
    {
    }

    public function __invoke(Request $request): Response
    {
        $signature = $request->header('X-Webhook-Signature', '');

        $this->service->handleWebhook($request->all(), $signature);

        return response()->noContent();
    }
}
