<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Plan;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function guest_cannot_access_api_dashboard()
    {
        $response = $this->getJson('/api/v1/dashboard');
        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_access_api_dashboard()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user',
                    'subscription',
                    'today_menu',
                    'personal_plans',
                    'stats',
                ],
            ]);
    }

    /** @test */
    public function api_dashboard_today_returns_json()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard/today');

        $response->assertStatus(200)->assertJsonStructure(['success']);
    }

    /** @test */
    public function api_dashboard_week_returns_json()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard/week');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['week_start', 'week_end', 'days'],
            ]);
    }

    /** @test */
    public function api_dashboard_profile_returns_user_data()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard/profile');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    /** @test */
    public function api_dashboard_profile_update_validates_input()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/dashboard/profile', [
                'name' => '', // required
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function api_dashboard_profile_update_works()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/dashboard/profile', [
                'name' => 'Новое имя',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->user->refresh();
        $this->assertEquals('Новое имя', $this->user->name);
    }

    /** @test */
    public function api_shopping_list_returns_json()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard/shopping-list');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['period', 'shopping_list'],
            ]);
    }
}
