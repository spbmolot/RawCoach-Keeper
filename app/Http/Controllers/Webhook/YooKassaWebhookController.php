<?php

namespace App\Http\Controllers\Webhook;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class YooKassaWebhookController
{
    public function __invoke(Request $request): Response
    {
        // TODO: validate and process YooKassa webhook
        return response()->noContent();
    }
}
