<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function guest_can_view_recipes_index()
    {
        $response = $this->get(route('recipes.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function guest_can_view_published_recipe()
    {
        $recipe = Recipe::create([
            'title' => 'Тестовый рецепт',
            'slug' => 'test-recipe',
            'description' => 'Описание',
            'instructions' => 'Шаг 1',
            'prep_time' => 10,
            'cook_time' => 20,
            'servings' => 2,
            'difficulty' => 'easy',
            'calories' => 300,
            'proteins' => 20,
            'fats' => 10,
            'carbs' => 30,
            'category' => 'breakfast',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('recipes.show', $recipe));
        $response->assertStatus(200);
    }

    /** @test */
    public function recipes_search_page_loads()
    {
        $response = $this->get(route('recipes.search', ['q' => 'салат']));
        $response->assertStatus(200);
    }

    /** @test */
    public function recipes_popular_page_loads()
    {
        $response = $this->get(route('recipes.popular'));
        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_view_favorites()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('recipes.favorites'));
        $response->assertStatus(200);
    }

    /** @test */
    public function guest_cannot_view_favorites()
    {
        $response = $this->get(route('recipes.favorites'));
        $response->assertRedirect(route('login'));
    }
}
