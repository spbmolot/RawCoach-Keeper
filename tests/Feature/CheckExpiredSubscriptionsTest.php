<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Plan;
use App\Models\UserSubscription;
use App\Events\SubscriptionStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Carbon\Carbon;

class CheckExpiredSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    protected Plan $standardPlan;
    protected Plan $trialPlan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->standardPlan = Plan::factory()->create([
            'type' => 'monthly',
            'role' => 'subscriber_standard',
        ]);

        $this->trialPlan = Plan::factory()->create([
            'type' => 'trial',
            'role' => 'subscriber_trial',
        ]);
    }

    /** @test */
    public function it_moves_expired_subscription_with_auto_renew_to_grace_period()
    {
        Event::fake();

        $user = User::factory()->create();

        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $this->standardPlan->id,
            'status' => 'active',
            'started_at' => now()->subDays(31),
            'ends_at' => now()->subDay(),
            'auto_renew' => true,
        ]);

        $this->artisan('subscriptions:check-expired --update')
            ->assertSuccessful();

        $subscription->refresh();

        $this->assertEquals('grace_period', $subscription->status);
        $this->assertNotNull($subscription->grace_period_ends_at);
        
        Event::assertDispatched(SubscriptionStatusChanged::class, function ($event) {
            return $event->newStatus === 'grace_period';
        });
    }

    /** @test */
    public function it_expires_subscription_without_auto_renew()
    {
        Event::fake();

        $user = User::factory()->create();

        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $this->standardPlan->id,
            'status' => 'active',
            'started_at' => now()->subDays(31),
            'ends_at' => now()->subDay(),
            'auto_renew' => false,
        ]);

        $this->artisan('subscriptions:check-expired --update')
            ->assertSuccessful();

        $subscription->refresh();

        $this->assertEquals('expired', $subscription->status);
        
        Event::assertDispatched(SubscriptionStatusChanged::class, function ($event) {
            return $event->newStatus === 'expired';
        });
    }

    /** @test */
    public function it_expires_trial_subscription_immediately()
    {
        Event::fake();

        $user = User::factory()->create();

        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $this->trialPlan->id,
            'status' => 'active',
            'started_at' => now()->subDays(8),
            'ends_at' => now()->subDay(),
            'auto_renew' => true, // Даже с auto_renew trial сразу истекает
        ]);

        $this->artisan('subscriptions:check-expired --update')
            ->assertSuccessful();

        $subscription->refresh();

        $this->assertEquals('expired', $subscription->status);
    }

    /** @test */
    public function it_expires_grace_period_after_deadline()
    {
        Event::fake();

        $user = User::factory()->create();

        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $this->standardPlan->id,
            'status' => 'grace_period',
            'started_at' => now()->subDays(34),
            'ends_at' => now()->subDays(4),
            'grace_period_ends_at' => now()->subDay(),
            'auto_renew' => true,
        ]);

        $this->artisan('subscriptions:check-expired --update')
            ->assertSuccessful();

        $subscription->refresh();

        $this->assertEquals('expired', $subscription->status);
        
        Event::assertDispatched(SubscriptionStatusChanged::class, function ($event) {
            return $event->newStatus === 'expired';
        });
    }

    /** @test */
    public function it_does_not_affect_active_subscriptions()
    {
        $user = User::factory()->create();

        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $this->standardPlan->id,
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => now()->addDays(30),
            'auto_renew' => true,
        ]);

        $this->artisan('subscriptions:check-expired --update')
            ->assertSuccessful();

        $subscription->refresh();

        $this->assertEquals('active', $subscription->status);
    }
}
