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
        $rawBody = json_encode($payload);
        $signature = 'test-signature';

        $service = Mockery::mock(CloudPaymentsService::class);
        $service->shouldReceive('handleWebhook')->once()->with($payload, $rawBody, $signature);

        $this->app->instance(CloudPaymentsService::class, $service);

        $this->postJson('/webhook/cloudpayments', $payload, ['Content-HMAC' => $signature])
            ->assertOk()
            ->assertJson(['code' => 0]);
    }

    public function test_returns_code_13_on_invalid_signature(): void
    {
        $payload = ['TransactionId' => 'cp_1', 'Status' => 'Completed'];

        $service = new CloudPaymentsService(Mockery::mock(), 'secret');
        $this->app->instance(CloudPaymentsService::class, $service);

        $this->postJson('/webhook/cloudpayments', $payload, ['Content-HMAC' => 'invalid'])
            ->assertOk()
            ->assertJson(['code' => 13]);
    }
}
