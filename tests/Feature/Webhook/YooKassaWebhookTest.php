<?php

namespace Tests\Feature\Webhook;

use App\Services\Payments\YooKassaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class YooKassaWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_controller_uses_service_via_di(): void
    {
        $payload = ['object' => ['id' => 'yk_1', 'status' => 'succeeded']];
        $signature = 'test-signature';

        $service = Mockery::mock(YooKassaService::class);
        $service->shouldReceive('handleWebhook')->once()->with($payload, $signature);

        $this->app->instance(YooKassaService::class, $service);

        $this->postJson('/webhook/yookassa', $payload, ['sha256' => $signature])
            ->assertNoContent();
    }
}
