<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\RecipeView;
use Carbon\Carbon;

class RecipeController extends Controller
{
    /**
     * Каталог всех рецептов
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Recipe::where('is_published', true);
        
        // Фильтрация по подписке пользователя
        if ($user && $user->activeSubscription()) {
            $this->filterRecipesBySubscription($query, $user);
        } else {
            // Только демо контент для неавторизованных
            $query->where('is_demo', true);
        }
        
        // Фильтры
        if ($request->filled('meal_type')) {
            $query->where('meal_type', $request->meal_type);
        }
        
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }
        
        if ($request->filled('cooking_time')) {
            $cookingTime = $request->cooking_time;
            if ($cookingTime === 'quick') {
                $query->where('cooking_time', '<=', 30);
            } elseif ($cookingTime === 'medium') {
                $query->whereBetween('cooking_time', [31, 60]);
            } elseif ($cookingTime === 'long') {
                $query->where('cooking_time', '>', 60);
            }
        }
        
        if ($request->filled('dietary_tags')) {
            $dietaryTags = $request->dietary_tags;
            $query->where(function($q) use ($dietaryTags) {
                foreach ($dietaryTags as $tag) {
                    $q->whereJsonContains('dietary_tags', $tag);
                }
            });
        }
        
        // Сортировка
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if ($sortBy === 'popularity') {
            $query->withCount('views')->orderBy('views_count', $sortOrder);
        } elseif ($sortBy === 'cooking_time') {
            $query->orderBy('cooking_time', $sortOrder);
        } elseif ($sortBy === 'calories') {
            $query->join('recipe_nutrition', 'recipes.id', '=', 'recipe_nutrition.recipe_id')
                  ->orderBy('recipe_nutrition.calories', $sortOrder)
                  ->select('recipes.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $recipes = $query->with(['nutrition', 'author'])
            ->paginate(12);
        
        // Получаем доступные фильтры
        $availableFilters = $this->getAvailableFilters();
        
        return view('recipes.index', compact('recipes', 'availableFilters'));
    }

    /**
     * Показать конкретный рецепт
     */
    public function show(Recipe $recipe)
    {
        if (!$recipe->is_published) {
            abort(404, 'Рецепт не найден');
        }
        
        $user = auth()->user();
        
        // Проверяем доступ к рецепту
        if (!$this->canAccessRecipe($user, $recipe)) {
            if (!$user) {
                return redirect()->route('login')
                    ->with('message', 'Войдите в систему для доступа к этому рецепту');
            }
            
            return redirect()->route('plans.index')
                ->with('error', 'Для доступа к этому рецепту необходима соответствующая подписка');
        }
        
        // Загружаем связанные данные
        $recipe->load([
            'nutrition',
            'ingredients.ingredient',
            'author',
            'tags'
        ]);
        
        // Записываем просмотр
        if ($user) {
            $this->recordView($user, $recipe);
        }
        
        // Получаем похожие рецепты
        $similarRecipes = $this->getSimilarRecipes($recipe, $user);
        
        // Проверяем, добавлен ли в избранное
        $isFavorite = $user ? $user->favoriteRecipes()->where('recipe_id', $recipe->id)->exists() : false;
        
        return view('recipes.show', compact('recipe', 'similarRecipes', 'isFavorite'));
    }

    /**
     * Поиск рецептов
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100'
        ]);
        
        $query = $request->get('q');
        $user = auth()->user();
        
        $recipeQuery = Recipe::where('is_published', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('instructions', 'like', "%{$query}%")
                  ->orWhereHas('ingredients.ingredient', function($ingredientQuery) use ($query) {
                      $ingredientQuery->where('name', 'like', "%{$query}%");
                  });
            });
        
        // Фильтрация по подписке
        if ($user && $user->activeSubscription()) {
            $this->filterRecipesBySubscription($recipeQuery, $user);
        } else {
            $recipeQuery->where('is_demo', true);
        }
        
        $recipes = $recipeQuery->with(['nutrition', 'author'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('recipes.search', compact('recipes', 'query'));
    }

    /**
     * Добавить рецепт в избранное
     */
    public function favorite(Recipe $recipe)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Необходима авторизация'], 401);
        }
        
        if (!$this->canAccessRecipe($user, $recipe)) {
            return response()->json(['error' => 'Нет доступа к этому рецепту'], 403);
        }
        
        $user->favoriteRecipes()->toggle($recipe->id);
        
        $isFavorite = $user->favoriteRecipes()->where('recipe_id', $recipe->id)->exists();
        
        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
            'message' => $isFavorite ? 'Рецепт добавлен в избранное' : 'Рецепт удален из избранного'
        ]);
    }

    /**
     * Избранные рецепты пользователя
     */
    public function favorites()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $favoriteRecipes = $user->favoriteRecipes()
            ->where('is_published', true)
            ->with(['nutrition', 'author'])
            ->orderBy('recipe_user.created_at', 'desc')
            ->paginate(12);
        
        return view('recipes.favorites', compact('favoriteRecipes'));
    }

    /**
     * Рецепты по типу приема пищи
     */
    public function byMealType(string $mealType)
    {
        $validMealTypes = ['breakfast', 'lunch', 'dinner', 'snack'];
        
        if (!in_array($mealType, $validMealTypes)) {
            abort(404);
        }
        
        $user = auth()->user();
        
        $query = Recipe::where('is_published', true)
            ->where('meal_type', $mealType);
        
        if ($user && $user->activeSubscription()) {
            $this->filterRecipesBySubscription($query, $user);
        } else {
            $query->where('is_demo', true);
        }
        
        $recipes = $query->with(['nutrition', 'author'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        $mealTypeNames = [
            'breakfast' => 'Завтраки',
            'lunch' => 'Обеды', 
            'dinner' => 'Ужины',
            'snack' => 'Перекусы'
        ];
        
        $pageTitle = $mealTypeNames[$mealType];
        
        return view('recipes.by-meal-type', compact('recipes', 'mealType', 'pageTitle'));
    }

    /**
     * Популярные рецепты
     */
    public function popular()
    {
        $user = auth()->user();
        
        $query = Recipe::where('is_published', true)
            ->withCount('views');
        
        if ($user && $user->activeSubscription()) {
            $this->filterRecipesBySubscription($query, $user);
        } else {
            $query->where('is_demo', true);
        }
        
        $recipes = $query->with(['nutrition', 'author'])
            ->orderBy('views_count', 'desc')
            ->paginate(12);
        
        return view('recipes.popular', compact('recipes'));
    }

    /**
     * Экспорт рецепта
     */
    public function export(Recipe $recipe, Request $request)
    {
        $user = auth()->user();
        
        if (!$user || !$user->hasPermissionTo('export.recipes')) {
            abort(403, 'Недостаточно прав для экспорта');
        }
        
        if (!$this->canAccessRecipe($user, $recipe)) {
            abort(403, 'Нет доступа к этому рецепту');
        }
        
        $format = $request->get('format', 'pdf');
        $filename = "recipe-{$recipe->slug}.{$format}";
        
        $recipe->load(['nutrition', 'ingredients.ingredient']);
        
        if ($format === 'pdf') {
            // return PDF::loadView('exports.recipe-pdf', compact('recipe'))->download($filename);
        } else {
            // return Excel::download(new RecipeExport($recipe), $filename);
        }
        
        return back()->with('success', 'Рецепт экспортирован');
    }

    /**
     * Проверка доступа к рецепту
     */
    private function canAccessRecipe($user, $recipe): bool
    {
        if ($recipe->is_demo) {
            return true;
        }
        
        if (!$user || !$user->activeSubscription()) {
            return false;
        }
        
        // Все подписчики имеют доступ к опубликованным рецептам
        return true;
    }

    /**
     * Фильтрация рецептов по подписке пользователя
     */
    private function filterRecipesBySubscription($query, $user)
    {
        $subscription = $user->activeSubscription();
        
        if (!$subscription) {
            $query->where('is_demo', true);
            return;
        }

        // Все подписчики имеют доступ ко всем опубликованным рецептам
        // Дополнительные ограничения можно добавить при необходимости
    }

    /**
     * Запись просмотра рецепта
     */
    private function recordView($user, $recipe)
    {
        // Проверяем, не просматривал ли пользователь этот рецепт недавно
        $recentView = RecipeView::where('user_id', $user->id)
            ->where('recipe_id', $recipe->id)
            ->where('created_at', '>', Carbon::now()->subHour())
            ->first();
        
        if (!$recentView) {
            RecipeView::create([
                'user_id' => $user->id,
                'recipe_id' => $recipe->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    /**
     * Получить похожие рецепты
     */
    private function getSimilarRecipes($recipe, $user, $limit = 4)
    {
        $query = Recipe::where('is_published', true)
            ->where('id', '!=', $recipe->id)
            ->where('meal_type', $recipe->meal_type);
        
        if ($user && $user->activeSubscription()) {
            $this->filterRecipesBySubscription($query, $user);
        } else {
            $query->where('is_demo', true);
        }
        
        return $query->with(['nutrition', 'author'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Получить доступные фильтры
     */
    private function getAvailableFilters(): array
    {
        return [
            'meal_types' => [
                'breakfast' => 'Завтрак',
                'lunch' => 'Обед',
                'dinner' => 'Ужин',
                'snack' => 'Перекус'
            ],
            'difficulties' => [
                'easy' => 'Легкий',
                'medium' => 'Средний',
                'hard' => 'Сложный'
            ],
            'cooking_times' => [
                'quick' => 'До 30 мин',
                'medium' => '30-60 мин',
                'long' => 'Более 60 мин'
            ],
            'dietary_tags' => [
                'vegetarian' => 'Вегетарианское',
                'vegan' => 'Веганское',
                'gluten_free' => 'Без глютена',
                'dairy_free' => 'Без молочных продуктов',
                'low_carb' => 'Низкоуглеводное',
                'high_protein' => 'Высокобелковое'
            ]
        ];
    }
}
