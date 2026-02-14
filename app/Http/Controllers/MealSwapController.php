<?php

namespace App\Http\Controllers;

use App\Models\DayMeal;
use App\Models\Recipe;
use App\Services\RecipeSwapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MealSwapController extends Controller
{
    public function __construct(
        private RecipeSwapService $swapService
    ) {
        $this->middleware('auth');
    }

    /**
     * Получить список альтернативных рецептов для замены
     */
    public function alternatives(DayMeal $dayMeal): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasActiveSubscription() && !$user->hasRole('root')) {
            return response()->json([
                'success' => false,
                'message' => 'Замена рецептов доступна только подписчикам',
            ], 403);
        }

        $alternatives = $this->swapService->getAlternatives($dayMeal, $user);

        Log::channel('user-actions')->debug('Meal swap alternatives loaded', [
            'user_id' => $user->id,
            'day_meal_id' => $dayMeal->id,
            'original_recipe' => $dayMeal->recipe->title,
            'alternatives_count' => $alternatives->count(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'original' => [
                    'id' => $dayMeal->recipe->id,
                    'title' => $dayMeal->recipe->title,
                    'calories' => $dayMeal->recipe->calories,
                    'proteins' => $dayMeal->recipe->proteins,
                    'fats' => $dayMeal->recipe->fats,
                    'carbs' => $dayMeal->recipe->carbs,
                    'image_url' => $dayMeal->recipe->image_url,
                    'cook_time' => $dayMeal->recipe->total_time,
                ],
                'alternatives' => $alternatives->map(fn(Recipe $r) => [
                    'id' => $r->id,
                    'title' => $r->title,
                    'slug' => $r->slug,
                    'calories' => $r->calories,
                    'proteins' => $r->proteins,
                    'fats' => $r->fats,
                    'carbs' => $r->carbs,
                    'image_url' => $r->image_url,
                    'cook_time' => $r->total_time,
                    'difficulty' => $r->difficulty,
                    'calorie_diff' => round($r->calories - $dayMeal->recipe->calories, 1),
                ])->values(),
            ],
        ]);
    }

    /**
     * Заменить рецепт
     */
    public function swap(Request $request, DayMeal $dayMeal): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasActiveSubscription() && !$user->hasRole('root')) {
            return response()->json([
                'success' => false,
                'message' => 'Замена рецептов доступна только подписчикам',
            ], 403);
        }

        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $newRecipe = Recipe::findOrFail($request->recipe_id);

        $swap = $this->swapService->swap($user, $dayMeal, $newRecipe, $request->reason);

        Log::channel('user-actions')->info('Meal recipe swapped', [
            'user_id' => $user->id,
            'day_meal_id' => $dayMeal->id,
            'original_recipe_id' => $dayMeal->recipe_id,
            'new_recipe_id' => $newRecipe->id,
            'new_recipe' => $newRecipe->title,
            'reason' => $request->reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Рецепт успешно заменён',
            'data' => [
                'swap_id' => $swap->id,
                'new_recipe' => [
                    'id' => $newRecipe->id,
                    'title' => $newRecipe->title,
                    'slug' => $newRecipe->slug,
                    'calories' => $newRecipe->calories,
                    'proteins' => $newRecipe->proteins,
                    'fats' => $newRecipe->fats,
                    'carbs' => $newRecipe->carbs,
                    'image_url' => $newRecipe->image_url,
                    'cook_time' => $newRecipe->total_time,
                ],
            ],
        ]);
    }

    /**
     * Сбросить замену (вернуть оригинальный рецепт)
     */
    public function reset(DayMeal $dayMeal): JsonResponse
    {
        $user = auth()->user();

        $deleted = $this->swapService->reset($user, $dayMeal);

        if ($deleted) {
            Log::channel('user-actions')->info('Meal swap reset', [
                'user_id' => $user->id,
                'day_meal_id' => $dayMeal->id,
                'restored_recipe' => $dayMeal->recipe->title,
            ]);
        }

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Замена не найдена',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Оригинальный рецепт восстановлен',
            'data' => [
                'original_recipe' => [
                    'id' => $dayMeal->recipe->id,
                    'title' => $dayMeal->recipe->title,
                    'slug' => $dayMeal->recipe->slug,
                    'calories' => $dayMeal->recipe->calories,
                    'proteins' => $dayMeal->recipe->proteins,
                    'fats' => $dayMeal->recipe->fats,
                    'carbs' => $dayMeal->recipe->carbs,
                    'image_url' => $dayMeal->recipe->image_url,
                    'cook_time' => $dayMeal->recipe->total_time,
                ],
            ],
        ]);
    }
}
