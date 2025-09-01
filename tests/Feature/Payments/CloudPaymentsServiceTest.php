<?php

namespace Tests\Feature\Payments;

use App\Models\Payment;
use App\Models\User;
use App\Services\Payments\CloudPaymentsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use CloudPayments\Manager as CloudPaymentsManager;
use Mockery;
use Tests\TestCase;

class CloudPaymentsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_payment_via_cloudpayments_service(): void
    {
        $user = User::factory()->create();

        $sdk = Mockery::mock(CloudPaymentsManager::class);
        $sdk->shouldReceive('createPayment')
            ->once()
            ->with([
                'user_id' => $user->id,
                'amount' => 1000,
                'currency' => 'RUB',
            ])
            ->andReturn([
                'TransactionId' => 'cp_1',
                'Status' => 'Pending',
            ]);

        $service = new CloudPaymentsService($sdk, 'secret');

        $payment = $service->createPayment([
            'user_id' => $user->id,
            'amount' => 1000,
            'currency' => 'RUB',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'provider' => 'cloudpayments',
            'external_id' => 'cp_1',
            'status' => 'Pending',
        ]);
    }

    public function test_it_handles_cloudpayments_webhook_and_updates_payment(): void
    {
        $user = User::factory()->create();

        $payment = Payment::create([
            'user_id' => $user->id,
            'provider' => 'cloudpayments',
            'external_id' => 'cp_1',
            'amount' => 1000,
            'currency' => 'RUB',
            'status' => 'Pending',
        ]);

        $payload = [
            'TransactionId' => 'cp_1',
            'Status' => 'Completed',
        ];

        $rawBody = json_encode($payload);
        $signature = base64_encode(hash_hmac('sha256', $rawBody, 'secret', true));

        $service = new CloudPaymentsService(Mockery::mock(CloudPaymentsManager::class), 'secret');

        $service->handleWebhook($payload, $rawBody, $signature);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'Completed',
        ]);

        $this->assertNotNull($payment->fresh()->paid_at);
    }
}
