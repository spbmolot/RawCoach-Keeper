<?php

namespace Tests\Feature\Webhook;

use App\Services\Payments\YooKassaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class YooKassaWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_controller_extracts_signature_from_header(): void
    {
        $payload = ['object' => ['id' => 'yk_1', 'status' => 'succeeded']];
        $rawBody = json_encode($payload);
        $signature = 'test-signature';

        $service = Mockery::mock(YooKassaService::class);
        $service->shouldReceive('handleWebhook')->once()->with($payload, $rawBody, $signature);

        $this->app->instance(YooKassaService::class, $service);

        $this->postJson('/webhook/yookassa', $payload, ['Content-Signature' => 'sha256='.$signature])
            ->assertNoContent();
    }

    public function test_controller_passes_empty_signature_if_wrong_format(): void
    {
        $payload = ['object' => ['id' => 'yk_1', 'status' => 'succeeded']];
        $rawBody = json_encode($payload);

        $service = Mockery::mock(YooKassaService::class);
        $service->shouldReceive('handleWebhook')->once()->with($payload, $rawBody, '');

        $this->app->instance(YooKassaService::class, $service);

        $this->postJson('/webhook/yookassa', $payload, ['Content-Signature' => 'invalid-format'])
            ->assertNoContent();
    }

    public function test_returns_400_on_invalid_signature(): void
    {
        $payload = ['object' => ['id' => 'yk_1', 'status' => 'succeeded']];

        $service = new YooKassaService(Mockery::mock(), 'secret');
        $this->app->instance(YooKassaService::class, $service);

        $this->postJson('/webhook/yookassa', $payload, ['Content-Signature' => 'sha256=неверный-хэш'])
            ->assertStatus(400);
    }
}
