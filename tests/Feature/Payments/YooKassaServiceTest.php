<?php

namespace Tests\Feature\Payments;

use App\Models\Payment;
use App\Models\User;
use App\Services\Payments\YooKassaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use YooKassa\Client as YooKassaClient;
use Mockery;
use Tests\TestCase;

class YooKassaServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_payment_via_yookassa_service(): void
    {
        $user = User::factory()->create();

        $sdk = Mockery::mock(YooKassaClient::class);
        $sdk->shouldReceive('createPayment')
            ->once()
            ->with([
                'user_id' => $user->id,
                'amount' => 1500,
                'currency' => 'RUB',
            ])
            ->andReturn([
                'id' => 'yk_1',
                'status' => 'pending',
            ]);

        $service = new YooKassaService($sdk, 'secret');

        $payment = $service->createPayment([
            'user_id' => $user->id,
            'amount' => 1500,
            'currency' => 'RUB',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'provider' => 'yookassa',
            'external_id' => 'yk_1',
            'status' => 'pending',
        ]);
    }

    public function test_it_handles_yookassa_webhook_and_updates_payment(): void
    {
        $user = User::factory()->create();

        $payment = Payment::create([
            'user_id' => $user->id,
            'provider' => 'yookassa',
            'external_id' => 'yk_1',
            'amount' => 1500,
            'currency' => 'RUB',
            'status' => 'pending',
        ]);

        $payload = [
            'object' => [
                'id' => 'yk_1',
                'status' => 'succeeded',
            ],
        ];

        $rawBody = json_encode($payload);
        $signature = hash_hmac('sha256', $rawBody, 'secret');

        $service = new YooKassaService(Mockery::mock(YooKassaClient::class), 'secret');

        $service->handleWebhook($payload, $rawBody, $signature);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'succeeded',
        ]);

        $this->assertNotNull($payment->fresh()->paid_at);
    }
}
