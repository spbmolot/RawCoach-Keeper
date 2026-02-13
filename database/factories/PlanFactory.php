<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'slug' => $this->faker->unique()->slug(2),
            'type' => $this->faker->randomElement(['monthly', 'yearly', 'trial', 'personal']),
            'price' => $this->faker->randomFloat(2, 0, 5000),
            'currency' => 'RUB',
            'duration_days' => $this->faker->randomElement([7, 30, 365]),
            'description' => $this->faker->sentence(),
            'features' => ['feature1', 'feature2'],
            'limits' => [],
            'role' => 'subscriber_standard',
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }

    public function trial(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Пробный',
            'type' => 'trial',
            'price' => 0,
            'duration_days' => 7,
            'role' => 'subscriber_trial',
        ]);
    }

    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Стандарт',
            'type' => 'monthly',
            'price' => 1990,
            'duration_days' => 30,
            'role' => 'subscriber_standard',
        ]);
    }

    public function yearly(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Премиум',
            'type' => 'yearly',
            'price' => 17910,
            'duration_days' => 365,
            'role' => 'subscriber_premium',
        ]);
    }

    public function personal(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Индивидуальный',
            'type' => 'personal',
            'price' => 4990,
            'duration_days' => 30,
            'role' => 'subscriber_personal',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
