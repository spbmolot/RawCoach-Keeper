<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Plan;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Services\SubscriptionService;
use App\Events\SubscriptionStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Carbon\Carbon;

class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SubscriptionService $service;
    protected User $user;
    protected Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(SubscriptionService::class);

        $this->plan = Plan::factory()->create([
            'name' => 'Стандарт',
            'type' => 'monthly',
            'price' => 1990,
            'duration_days' => 30,
            'role' => 'subscriber_standard',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_activates_subscription_on_successful_payment()
    {
        Event::fake();

        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'status' => 'pending',
            'started_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        $payment = Payment::create([
            'user_id' => $this->user->id,
            'subscription_id' => $subscription->id,
            'plan_id' => $this->plan->id,
            'amount' => 1990,
            'currency' => 'RUB',
            'status' => 'paid',
            'provider' => 'yookassa',
        ]);

        $this->service->activateSubscription($payment);

        $subscription->refresh();

        $this->assertEquals('active', $subscription->status);
        Event::assertDispatched(SubscriptionStatusChanged::class);
    }

    /** @test */
    public function it_does_not_activate_already_active_subscription()
    {
        Event::fake();

        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        $payment = Payment::create([
            'user_id' => $this->user->id,
            'subscription_id' => $subscription->id,
            'plan_id' => $this->plan->id,
            'amount' => 1990,
            'currency' => 'RUB',
            'status' => 'paid',
            'provider' => 'yookassa',
        ]);

        $this->service->activateSubscription($payment);

        // Событие не должно быть отправлено повторно
        Event::assertNotDispatched(SubscriptionStatusChanged::class);
    }

    /** @test */
    public function it_deactivates_subscription_on_refund()
    {
        Event::fake();

        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        $payment = Payment::create([
            'user_id' => $this->user->id,
            'subscription_id' => $subscription->id,
            'plan_id' => $this->plan->id,
            'amount' => 1990,
            'currency' => 'RUB',
            'status' => 'refunded',
            'provider' => 'yookassa',
        ]);

        $this->service->deactivateSubscription($payment, 'Возврат средств');

        $subscription->refresh();

        $this->assertEquals('cancelled', $subscription->status);
        $this->assertNotNull($subscription->cancelled_at);
    }
}
