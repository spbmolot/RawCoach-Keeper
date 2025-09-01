<?php

namespace Tests\Feature\Webhook;

use App\Services\Payments\YooKassaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;
use YooKassa\Client as YooKassaClient;

class YooKassaWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists(YooKassaClient::class)) {
            class_alias(\stdClass::class, YooKassaClient::class);
        }

        config(['app.key' => 'base64:' . base64_encode(random_bytes(32))]);
    }

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

    public function test_returns_400_if_signature_has_wrong_format(): void
    {
        $payload = ['object' => ['id' => 'yk_1', 'status' => 'succeeded']];

        $service = Mockery::mock(YooKassaService::class);
        $service->shouldNotReceive('handleWebhook');

        $this->app->instance(YooKassaService::class, $service);

        $this->postJson('/webhook/yookassa', $payload, ['Content-Signature' => 'invalid-format'])
            ->assertStatus(400);
    }

    public function test_returns_400_on_invalid_signature(): void
    {
        $payload = ['object' => ['id' => 'yk_1', 'status' => 'succeeded']];

        $service = new YooKassaService(Mockery::mock(YooKassaClient::class), 'secret');
        $this->app->instance(YooKassaService::class, $service);

        $this->postJson('/webhook/yookassa', $payload, ['Content-Signature' => 'sha256=неверный-хэш'])
            ->assertStatus(400);
    }
}
