<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Day;
use App\Models\DayMeal;
use App\Models\Recipe;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menu = Menu::firstOrCreate(
            ['slug' => 'yanvar-2026'],
            [
                'title' => 'Меню на Январь 2026',
                'description' => 'Сбалансированное меню на 1200-1400 ккал для здорового похудения. 31 день разнообразных блюд.',
                'month' => 1,
                'year' => 2026,
                'total_calories' => 1300,
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        if (!$menu->wasRecentlyCreated) {
            return;
        }

        $breakfasts = Recipe::where('category', 'breakfast')->where('is_published', true)->pluck('id')->toArray();
        $lunches = Recipe::where('category', 'lunch')->where('is_published', true)->pluck('id')->toArray();
        $dinners = Recipe::where('category', 'dinner')->where('is_published', true)->pluck('id')->toArray();
        $snacks = Recipe::where('category', 'snack')->where('is_published', true)->pluck('id')->toArray();

        if (empty($breakfasts) || empty($lunches) || empty($dinners) || empty($snacks)) {
            $this->command->warn('Недостаточно рецептов для создания меню. Сначала запустите RecipeSeeder.');
            return;
        }

        for ($dayNumber = 1; $dayNumber <= 31; $dayNumber++) {
            $day = Day::create([
                'menu_id' => $menu->id,
                'day_number' => $dayNumber,
                'title' => "День $dayNumber",
                'is_active' => true,
            ]);

            $breakfastId = $breakfasts[($dayNumber - 1) % count($breakfasts)];
            $lunchId = $lunches[($dayNumber - 1) % count($lunches)];
            $dinnerId = $dinners[($dayNumber - 1) % count($dinners)];
            $snackId = $snacks[($dayNumber - 1) % count($snacks)];

            DayMeal::create([
                'day_id' => $day->id,
                'recipe_id' => $breakfastId,
                'meal_type' => 'breakfast',
                'order' => 1,
            ]);

            DayMeal::create([
                'day_id' => $day->id,
                'recipe_id' => $lunchId,
                'meal_type' => 'lunch',
                'order' => 2,
            ]);

            DayMeal::create([
                'day_id' => $day->id,
                'recipe_id' => $snackId,
                'meal_type' => 'snack',
                'order' => 3,
            ]);

            DayMeal::create([
                'day_id' => $day->id,
                'recipe_id' => $dinnerId,
                'meal_type' => 'dinner',
                'order' => 4,
            ]);

            $day->recalculateNutrition();
        }

        $this->command->info("Создано меню '{$menu->title}' с 31 днём.");
    }
}
