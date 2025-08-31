<?php

namespace App\Http\Controllers\Webhook;

use App\Services\Payments\CloudPaymentsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CloudPaymentsWebhookController
{
    public function __construct(private CloudPaymentsService $service)
    {
    }

    public function __invoke(Request $request): Response
    {
        $signature = $request->header('Content-HMAC', '');

        $this->service->handleWebhook($request->all(), $signature);

        return response()->noContent();
    }
}
