<?php

namespace App\Http\Controllers\Webhook;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CloudPaymentsWebhookController
{
    public function __invoke(Request $request): Response
    {
        // TODO: validate and process CloudPayments webhook
        return response()->noContent();
    }
}
