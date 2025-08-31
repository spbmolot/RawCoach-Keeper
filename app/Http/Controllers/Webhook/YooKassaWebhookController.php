<?php

namespace App\Http\Controllers\Webhook;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Payments\YooKassaService;

class YooKassaWebhookController
{
    public function __invoke(Request $request, YooKassaService $service): Response
    {
        $service->handleWebhook($request->all());

        return response()->noContent();
    }
}
