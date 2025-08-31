<?php

namespace App\Http\Controllers\Webhook;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Payments\CloudPaymentsService;

class CloudPaymentsWebhookController
{
    public function __invoke(Request $request, CloudPaymentsService $service): Response
    {
        $service->handleWebhook($request->all());

        return response()->noContent();
    }
}
