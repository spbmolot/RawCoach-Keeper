<?php

namespace Tests\Feature\Webhook;

use App\Services\Payments\CloudPaymentsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CloudPaymentsWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_controller_uses_service_via_di(): void
    {
        $payload = ['TransactionId' => 'cp_1', 'Status' => 'Completed'];
        $signature = 'test-signature';

        $service = Mockery::mock(CloudPaymentsService::class);
        $service->shouldReceive('handleWebhook')->once()->with($payload, $signature);

        $this->app->instance(CloudPaymentsService::class, $service);

        $this->postJson('/webhook/cloudpayments', $payload, ['Content-HMAC' => $signature])
            ->assertOk()
            ->assertJson(['code' => 0]);
    }
}
