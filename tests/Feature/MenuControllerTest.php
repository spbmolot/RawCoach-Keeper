<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function guest_can_view_menus_index()
    {
        $response = $this->get(route('menus.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_view_archive()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('menus.archive'));
        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_view_search()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('menus.search'));
        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_view_favorites()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('menus.favorites'));
        $response->assertStatus(200);
    }

    /** @test */
    public function guest_cannot_access_menu_favorites()
    {
        $response = $this->get(route('menus.favorites'));
        $response->assertRedirect(route('login'));
    }
}
