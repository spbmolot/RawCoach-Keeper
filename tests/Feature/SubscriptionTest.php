<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Plan;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Services\SubscriptionService;
use App\Services\SubscriptionRoleService;
use App\Events\SubscriptionStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Carbon\Carbon;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Plan $trialPlan;
    protected Plan $standardPlan;
    protected Plan $premiumPlan;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаём тестовые планы
        $this->trialPlan = Plan::factory()->create([
            'name' => 'Пробный',
            'slug' => 'trial',
            'type' => 'trial',
            'price' => 0,
            'duration_days' => 7,
            'role' => 'subscriber_trial',
            'is_active' => true,
        ]);

        $this->standardPlan = Plan::factory()->create([
            'name' => 'Стандарт',
            'slug' => 'standard-monthly',
            'type' => 'monthly',
            'price' => 1990,
            'duration_days' => 30,
            'role' => 'subscriber_standard',
            'is_active' => true,
        ]);

        $this->premiumPlan = Plan::factory()->create([
            'name' => 'Премиум',
            'slug' => 'premium-yearly',
            'type' => 'yearly',
            'price' => 17910,
            'duration_days' => 365,
            'role' => 'subscriber_premium',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_create_trial_subscription()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('subscription.create', $this->trialPlan));

        $response->assertRedirect();
        
        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $this->user->id,
            'plan_id' => $this->trialPlan->id,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function user_cannot_use_trial_twice()
    {
        $this->actingAs($this->user);

        // Создаём первую trial подписку
        UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->trialPlan->id,
            'status' => 'expired',
            'started_at' => now()->subDays(14),
            'ends_at' => now()->subDays(7),
        ]);

        // Пытаемся создать вторую
        $response = $this->post(route('subscription.create', $this->trialPlan));

        $response->assertSessionHas('error');
        
        // Должна быть только одна подписка
        $this->assertEquals(1, $this->user->subscriptions()->count());
    }

    /** @test */
    public function user_with_active_subscription_cannot_create_another()
    {
        $this->actingAs($this->user);

        // Создаём активную подписку
        UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->standardPlan->id,
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => now()->addDays(30),
            'auto_renew' => true,
        ]);

        // Пытаемся создать ещё одну
        $response = $this->post(route('subscription.create', $this->premiumPlan));

        $response->assertSessionHas('error');
    }

    /** @test */
    public function cancelling_subscription_only_disables_auto_renew()
    {
        $this->actingAs($this->user);

        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->standardPlan->id,
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => now()->addDays(30),
            'auto_renew' => true,
        ]);

        $response = $this->post(route('subscription.cancel', $subscription));

        $response->assertSessionHas('success');

        $subscription->refresh();

        // Статус должен остаться active
        $this->assertEquals('active', $subscription->status);
        // Автопродление должно быть выключено
        $this->assertFalse($subscription->auto_renew);
        // Дата отмены должна быть заполнена
        $this->assertNotNull($subscription->cancelled_at);
    }

    /** @test */
    public function upgrade_schedules_plan_change_for_next_renewal()
    {
        $this->actingAs($this->user);

        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->standardPlan->id,
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => now()->addDays(15),
            'auto_renew' => true,
        ]);

        $response = $this->post(route('subscription.upgrade', $this->premiumPlan));

        $response->assertSessionHas('success');

        $subscription->refresh();

        // Текущий план не должен измениться
        $this->assertEquals($this->standardPlan->id, $subscription->plan_id);
        // Должен быть запланирован новый план
        $this->assertEquals($this->premiumPlan->id, $subscription->scheduled_plan_id);
    }

    /** @test */
    public function downgrade_schedules_plan_change_for_next_renewal()
    {
        $this->actingAs($this->user);

        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->premiumPlan->id,
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => now()->addDays(100),
            'auto_renew' => true,
        ]);

        $response = $this->post(route('subscription.downgrade', $this->standardPlan));

        $response->assertSessionHas('success');

        $subscription->refresh();

        // Текущий план не должен измениться
        $this->assertEquals($this->premiumPlan->id, $subscription->plan_id);
        // Должен быть запланирован новый план
        $this->assertEquals($this->standardPlan->id, $subscription->scheduled_plan_id);
    }

    /** @test */
    public function cannot_switch_to_trial_from_paid_subscription()
    {
        $this->actingAs($this->user);

        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->standardPlan->id,
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => now()->addDays(30),
            'auto_renew' => true,
        ]);

        $response = $this->post(route('subscription.upgrade', $this->trialPlan));

        $response->assertSessionHas('error');

        $subscription->refresh();
        $this->assertNull($subscription->scheduled_plan_id);
    }

    /** @test */
    public function scheduled_plan_change_can_be_cancelled()
    {
        $this->actingAs($this->user);

        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->standardPlan->id,
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => now()->addDays(30),
            'auto_renew' => true,
            'scheduled_plan_id' => $this->premiumPlan->id,
            'scheduled_at' => now(),
        ]);

        $response = $this->post(route('subscription.cancel-scheduled', $subscription));

        $response->assertSessionHas('success');

        $subscription->refresh();
        $this->assertNull($subscription->scheduled_plan_id);
        $this->assertNull($subscription->scheduled_at);
    }
}
