<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Plan;
use App\Models\UserSubscription;
use App\Models\Menu;
use App\Models\Day;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::factory()->monthly()->create();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function guest_cannot_access_dashboard()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_access_dashboard()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index');
    }

    /** @test */
    public function dashboard_today_page_loads()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('dashboard.today'));
        $response->assertStatus(200);
    }

    /** @test */
    public function dashboard_week_page_loads()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('dashboard.week'));
        $response->assertStatus(200);
    }

    /** @test */
    public function dashboard_calendar_page_loads()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('dashboard.calendar'));
        $response->assertStatus(200);
    }

    /** @test */
    public function dashboard_shopping_list_page_loads()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('dashboard.shopping-list'));
        $response->assertStatus(200);
    }

    /** @test */
    public function dashboard_profile_page_loads()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('dashboard.profile'));
        $response->assertStatus(200);
    }

    /** @test */
    public function dashboard_personal_plans_page_loads()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('dashboard.personal-plans'));
        $response->assertStatus(200);
    }
}
