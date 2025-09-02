<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\UserFavorite;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RecipeController extends Controller
{
    /**
     * Список рецептов
     */
    public function index(Request $request): JsonResponse
    {
        $query = Recipe::with(['nutrition', 'ingredients'])
            ->where('status', 'published');

        // Фильтрация по типу приема пищи
        if ($request->has('meal_type')) {
            $query->where('meal_type', $request->get('meal_type'));
        }

        // Фильтрация по калорийности
        if ($request->has('calories_min')) {
            $query->whereHas('nutrition', function ($q) use ($request) {
                $q->where('calories', '>=', $request->get('calories_min'));
            });
        }

        if ($request->has('calories_max')) {
            $query->whereHas('nutrition', function ($q) use ($request) {
                $q->where('calories', '<=', $request->get('calories_max'));
            });
        }

        // Сортировка
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $recipes = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $recipes
        ]);
    }

    /**
     * Детали рецепта
     */
    public function show(Recipe $recipe): JsonResponse
    {
        $recipe->load(['nutrition', 'ingredients']);

        return response()->json([
            'success' => true,
            'data' => $recipe
        ]);
    }

    /**
     * Поиск рецептов
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Поисковый запрос не может быть пустым'
            ], 400);
        }

        $recipes = Recipe::with(['nutrition', 'ingredients'])
            ->where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('ingredients', function ($ingredientQuery) use ($query) {
                      $ingredientQuery->where('name', 'like', "%{$query}%");
                  });
            })
            ->orderBy('title')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $recipes
        ]);
    }

    /**
     * Избранные рецепты
     */
    public function favorites(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $favoriteRecipeIds = UserFavorite::where('user_id', $user->id)
            ->where('favorable_type', Recipe::class)
            ->pluck('favorable_id');

        $recipes = Recipe::with(['nutrition', 'ingredients'])
            ->whereIn('id', $favoriteRecipeIds)
            ->orderBy('title')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $recipes
        ]);
    }

    /**
     * Добавить/убрать из избранного
     */
    public function favorite(Request $request, Recipe $recipe): JsonResponse
    {
        $user = auth()->user();

        $favorite = UserFavorite::where('user_id', $user->id)
            ->where('favorable_type', Recipe::class)
            ->where('favorable_id', $recipe->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'Рецепт удален из избранного';
            $isFavorite = false;
        } else {
            UserFavorite::create([
                'user_id' => $user->id,
                'favorable_type' => Recipe::class,
                'favorable_id' => $recipe->id,
            ]);
            $message = 'Рецепт добавлен в избранное';
            $isFavorite = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'is_favorite' => $isFavorite
            ]
        ]);
    }
}
