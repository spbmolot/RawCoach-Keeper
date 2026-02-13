<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Plan;
use App\Models\UserSubscription;
use App\Services\SubscriptionRoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SubscriptionRoleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SubscriptionRoleService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(SubscriptionRoleService::class);

        // Создаём роли
        Role::create(['name' => 'user']);
        Role::create(['name' => 'subscriber_trial']);
        Role::create(['name' => 'subscriber_standard']);
        Role::create(['name' => 'subscriber_premium']);
        Role::create(['name' => 'subscriber_lapsed']);
        Role::create(['name' => 'subscriber_expired']);

        $this->user = User::factory()->create();
        $this->user->assignRole('user');
    }

    /** @test */
    public function it_assigns_subscriber_role_on_activation()
    {
        $plan = Plan::factory()->create([
            'type' => 'monthly',
            'role' => 'subscriber_standard',
        ]);

        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        $this->service->handleSubscriptionStatusChange($subscription, 'active');

        $this->assertTrue($this->user->fresh()->hasRole('subscriber_standard'));
    }

    /** @test */
    public function it_removes_subscriber_role_on_expiration()
    {
        $plan = Plan::factory()->create([
            'type' => 'monthly',
            'role' => 'subscriber_standard',
        ]);

        $this->user->assignRole('subscriber_standard');

        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $plan->id,
            'status' => 'expired',
            'started_at' => now()->subDays(30),
            'ends_at' => now()->subDay(),
        ]);

        $this->service->handleSubscriptionStatusChange($subscription, 'expired');

        $this->assertFalse($this->user->fresh()->hasRole('subscriber_standard'));
        $this->assertTrue($this->user->fresh()->hasRole('subscriber_expired'));
    }

    /** @test */
    public function it_assigns_lapsed_role_for_trial_only_users()
    {
        $trialPlan = Plan::factory()->create([
            'type' => 'trial',
            'role' => 'subscriber_trial',
        ]);

        $this->user->assignRole('subscriber_trial');

        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $trialPlan->id,
            'status' => 'expired',
            'started_at' => now()->subDays(7),
            'ends_at' => now()->subDay(),
        ]);

        $this->service->handleSubscriptionStatusChange($subscription, 'expired');

        $this->assertFalse($this->user->fresh()->hasRole('subscriber_trial'));
        $this->assertTrue($this->user->fresh()->hasRole('subscriber_lapsed'));
    }

    /** @test */
    public function it_assigns_expired_role_for_paid_subscribers()
    {
        $plan = Plan::factory()->create([
            'type' => 'monthly',
            'role' => 'subscriber_standard',
        ]);

        $this->user->assignRole('subscriber_standard');

        // Создаём историю платной подписки
        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $plan->id,
            'status' => 'expired',
            'started_at' => now()->subDays(30),
            'ends_at' => now()->subDay(),
        ]);

        $this->service->handleSubscriptionStatusChange($subscription, 'expired');

        $this->assertFalse($this->user->fresh()->hasRole('subscriber_standard'));
        $this->assertTrue($this->user->fresh()->hasRole('subscriber_expired'));
    }

    /** @test */
    public function it_keeps_role_during_grace_period()
    {
        $plan = Plan::factory()->create([
            'type' => 'monthly',
            'role' => 'subscriber_standard',
        ]);

        $this->user->assignRole('subscriber_standard');

        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $plan->id,
            'status' => 'grace_period',
            'started_at' => now()->subDays(30),
            'ends_at' => now()->subDay(),
            'grace_period_ends_at' => now()->addDays(3),
        ]);

        $this->service->handleSubscriptionStatusChange($subscription, 'grace_period');

        // Роль должна сохраниться
        $this->assertTrue($this->user->fresh()->hasRole('subscriber_standard'));
    }

    /** @test */
    public function it_upgrades_role_when_plan_changes()
    {
        $standardPlan = Plan::factory()->create([
            'type' => 'monthly',
            'role' => 'subscriber_standard',
        ]);

        $premiumPlan = Plan::factory()->create([
            'type' => 'yearly',
            'role' => 'subscriber_premium',
        ]);

        $this->user->assignRole('subscriber_standard');

        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $premiumPlan->id,
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => now()->addDays(365),
        ]);

        $this->service->handleSubscriptionStatusChange($subscription, 'active');

        $this->assertFalse($this->user->fresh()->hasRole('subscriber_standard'));
        $this->assertTrue($this->user->fresh()->hasRole('subscriber_premium'));
    }
}
