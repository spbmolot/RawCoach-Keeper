<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\DayMeal;
use App\Models\User;
use App\Models\UserMealSwap;
use Illuminate\Support\Collection;

class RecipeSwapService
{
    /**
     * Найти альтернативные рецепты для замены
     *
     * Логика подбора:
     * 1. Тот же meal_type (категория рецепта)
     * 2. Калорийность ±30% от оригинала
     * 3. Исключаем аллергены пользователя
     * 4. Исключаем уже использованные в этом дне рецепты
     * 5. Сортируем по близости калорийности
     */
    public function getAlternatives(DayMeal $dayMeal, User $user, int $limit = 10): Collection
    {
        $originalRecipe = $dayMeal->recipe;
        if (!$originalRecipe) {
            return collect();
        }

        $mealType = $dayMeal->meal_type;
        $calories = (float) $originalRecipe->calories;
        $caloriesMin = $calories * 0.7;
        $caloriesMax = $calories * 1.3;

        // Рецепты, уже присутствующие в этом дне (исключим их)
        $dayRecipeIds = DayMeal::where('day_id', $dayMeal->day_id)
            ->pluck('recipe_id')
            ->toArray();

        // Уже использованные замены пользователя для этого дня
        $swappedRecipeIds = UserMealSwap::where('user_id', $user->id)
            ->whereHas('dayMeal', fn($q) => $q->where('day_id', $dayMeal->day_id))
            ->pluck('replacement_recipe_id')
            ->toArray();

        $excludeIds = array_unique(array_merge($dayRecipeIds, $swappedRecipeIds));

        $query = Recipe::published()
            ->whereNotIn('id', $excludeIds)
            ->where('category', $mealType)
            ->where('calories', '>=', $caloriesMin)
            ->where('calories', '<=', $caloriesMax);

        // Исключаем аллергены пользователя
        $userAllergies = $user->allergies;
        if ($userAllergies && is_array($userAllergies)) {
            foreach ($userAllergies as $allergen) {
                $query->whereJsonDoesntContain('allergens', $allergen);
            }
        }

        // Сортируем по близости калорийности к оригиналу
        $alternatives = $query
            ->orderByRaw('ABS(calories - ?) ASC', [$calories])
            ->limit($limit)
            ->get();

        // Если мало результатов по category = meal_type, расширяем поиск (убираем фильтр категории)
        if ($alternatives->count() < 3) {
            $fallbackQuery = Recipe::published()
                ->whereNotIn('id', array_merge($excludeIds, $alternatives->pluck('id')->toArray()))
                ->where('calories', '>=', $caloriesMin)
                ->where('calories', '<=', $caloriesMax);

            if ($userAllergies && is_array($userAllergies)) {
                foreach ($userAllergies as $allergen) {
                    $fallbackQuery->whereJsonDoesntContain('allergens', $allergen);
                }
            }

            $extra = $fallbackQuery
                ->orderByRaw('ABS(calories - ?) ASC', [$calories])
                ->limit($limit - $alternatives->count())
                ->get();

            $alternatives = $alternatives->merge($extra);
        }

        return $alternatives;
    }

    /**
     * Выполнить замену рецепта
     */
    public function swap(User $user, DayMeal $dayMeal, Recipe $newRecipe, ?string $reason = null): UserMealSwap
    {
        $originalRecipe = $dayMeal->recipe;

        return UserMealSwap::updateOrCreate(
            [
                'user_id' => $user->id,
                'day_meal_id' => $dayMeal->id,
            ],
            [
                'original_recipe_id' => $originalRecipe->id,
                'replacement_recipe_id' => $newRecipe->id,
                'reason' => $reason,
            ]
        );
    }

    /**
     * Сбросить замену (вернуть оригинальный рецепт)
     */
    public function reset(User $user, DayMeal $dayMeal): bool
    {
        return UserMealSwap::where('user_id', $user->id)
            ->where('day_meal_id', $dayMeal->id)
            ->delete() > 0;
    }

    /**
     * Сбросить все замены пользователя для конкретного дня
     */
    public function resetDay(User $user, int $dayId): int
    {
        return UserMealSwap::where('user_id', $user->id)
            ->whereHas('dayMeal', fn($q) => $q->where('day_id', $dayId))
            ->delete();
    }

    /**
     * Получить все замены пользователя для дня
     * Возвращает коллекцию indexed by day_meal_id
     */
    public function getSwapsForDay(User $user, int $dayId): Collection
    {
        return UserMealSwap::where('user_id', $user->id)
            ->whereHas('dayMeal', fn($q) => $q->where('day_id', $dayId))
            ->with('replacementRecipe.ingredients')
            ->get()
            ->keyBy('day_meal_id');
    }

    /**
     * Применить замены к коллекции meals
     * Возвращает meals с подставленными рецептами-заменами
     */
    public function applySwaps(Collection $meals, Collection $swaps): Collection
    {
        return $meals->map(function ($meal) use ($swaps) {
            if ($swaps->has($meal->id)) {
                $swap = $swaps->get($meal->id);
                $meal->setAttribute('swapped', true);
                $meal->setAttribute('original_recipe', $meal->recipe);
                $meal->setRelation('recipe', $swap->replacementRecipe);
                $meal->setAttribute('swap_id', $swap->id);
            } else {
                $meal->setAttribute('swapped', false);
            }
            return $meal;
        });
    }
}
