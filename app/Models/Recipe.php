<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Recipe extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'instructions',
        'prep_time',
        'cook_time',
        'servings',
        'difficulty',
        'calories',
        'proteins',
        'fats',
        'carbs',
        'fiber',
        'category',
        'cuisine',
        'dietary_tags',
        'allergens',
        'main_image',
        'gallery_images',
        'is_published',
        'published_at',
        'views_count',
        'rating',
        'ratings_count',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'dietary_tags' => 'array',
            'allergens' => 'array',
            'gallery_images' => 'array',
            'calories' => 'decimal:1',
            'proteins' => 'decimal:1',
            'fats' => 'decimal:1',
            'carbs' => 'decimal:1',
            'fiber' => 'decimal:1',
            'views_count' => 'integer',
            'rating' => 'decimal:2',
            'ratings_count' => 'integer',
        ];
    }

    /**
     * Ингредиенты рецепта
     */
    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class)->orderBy('order');
    }

    /**
     * Дни меню, где используется рецепт
     */
    public function dayMeals(): HasMany
    {
        return $this->hasMany(DayMeal::class);
    }

    /**
     * Пользователи, добавившие в избранное
     */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorite_recipes');
    }

    /**
     * Просмотры рецепта
     */
    public function views(): HasMany
    {
        return $this->hasMany(RecipeView::class);
    }

    /**
     * Автор рецепта
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Опубликованные рецепты
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Рекомендуемые рецепты
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * По категории
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * По типу кухни
     */
    public function scopeByCuisine($query, string $cuisine)
    {
        return $query->where('cuisine_type', $cuisine);
    }

    /**
     * По сложности
     */
    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * С диетическими тегами
     */
    public function scopeWithDietaryTag($query, string $tag)
    {
        return $query->whereJsonContains('dietary_tags', $tag);
    }

    /**
     * Без аллергенов
     */
    public function scopeWithoutAllergen($query, string $allergen)
    {
        return $query->whereJsonDoesntContain('allergens', $allergen);
    }

    /**
     * По калорийности
     */
    public function scopeByCalories($query, int $min = null, int $max = null)
    {
        if ($min) {
            $query->where('calories', '>=', $min);
        }
        if ($max) {
            $query->where('calories', '<=', $max);
        }
        return $query;
    }

    /**
     * Быстрые рецепты (до 30 минут)
     */
    public function scopeQuick($query)
    {
        return $query->where('total_time', '<=', 30);
    }

    /**
     * Алиас для main_image (используется в шаблонах как $recipe->image)
     */
    public function getImageAttribute(): ?string
    {
        return $this->main_image;
    }

    /**
     * Полный URL изображения (для schema.org и API)
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->main_image ? Storage::url($this->main_image) : null;
    }

    /**
     * Общее время приготовления
     */
    public function getTotalTimeAttribute(): int
    {
        return $this->prep_time + $this->cook_time;
    }

    /**
     * Получить название категории
     */
    public function getCategoryName(): string
    {
        $categories = [
            'breakfast' => 'Завтрак',
            'lunch' => 'Обед',
            'dinner' => 'Ужин',
            'snack' => 'Перекус',
            'dessert' => 'Десерт',
        ];

        return $categories[$this->category] ?? 'Не указано';
    }

    /**
     * Получить название сложности
     */
    public function getDifficultyName(): string
    {
        $difficulties = [
            'easy' => 'Легко',
            'medium' => 'Средне',
            'hard' => 'Сложно',
        ];

        return $difficulties[$this->difficulty] ?? 'Не указано';
    }

    /**
     * Форматированное время
     */
    public function getFormattedTime(): string
    {
        $total = $this->total_time;
        
        if ($total < 60) {
            return $total . ' мин';
        }
        
        $hours = floor($total / 60);
        $minutes = $total % 60;
        
        return $hours . ' ч' . ($minutes > 0 ? ' ' . $minutes . ' мин' : '');
    }

    /**
     * Проверка избранного для пользователя
     */
    public function isFavoritedByUser(User $user): bool
    {
        return $this->favoritedByUsers()->where('user_id', $user->id)->exists();
    }

    /**
     * Проверка совместимости с предпочтениями пользователя
     */
    public function isCompatibleWithUser(User $user): bool
    {
        // Проверка аллергенов
        if ($user->allergens && $this->allergens) {
            $userAllergens = is_string($user->allergens) ? json_decode($user->allergens, true) : $user->allergens;
            $recipeAllergens = is_string($this->allergens) ? json_decode($this->allergens, true) : $this->allergens;
            
            if (array_intersect($userAllergens, $recipeAllergens)) {
                return false;
            }
        }

        // Проверка нелюбимых продуктов
        if ($user->dislikes && $this->ingredients()->count() > 0) {
            $userDislikes = is_string($user->dislikes) ? json_decode($user->dislikes, true) : $user->dislikes;
            
            foreach ($this->ingredients as $ingredient) {
                if (in_array($ingredient->ingredient_name, $userDislikes)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Получить БЖУ на 100 грамм
     */
    public function getNutritionPer100g(): array
    {
        $totalWeight = $this->ingredients()->sum('amount');
        
        if ($totalWeight == 0) {
            return [
                'calories' => 0,
                'proteins' => 0,
                'fats' => 0,
                'carbs' => 0,
            ];
        }

        $ratio = 100 / $totalWeight;

        return [
            'calories' => round($this->calories * $ratio, 1),
            'proteins' => round($this->proteins * $ratio, 1),
            'fats' => round($this->fats * $ratio, 1),
            'carbs' => round($this->carbs * $ratio, 1),
        ];
    }

    /**
     * Получить список покупок для рецепта
     */
    public function getShoppingList(): array
    {
        $ingredients = [];

        foreach ($this->ingredients as $ingredient) {
            $category = $ingredient->category ?? 'Прочее';
            
            if (!isset($ingredients[$category])) {
                $ingredients[$category] = [];
            }
            
            $ingredients[$category][] = [
                'name' => $ingredient->ingredient_name,
                'amount' => $ingredient->amount,
                'unit' => $ingredient->unit,
                'notes' => $ingredient->notes,
            ];
        }

        return $ingredients;
    }

    /**
     * Масштабировать рецепт для количества порций
     */
    public function scaleForServings(int $newServings): array
    {
        if ($this->servings == 0) {
            return [];
        }

        $ratio = $newServings / $this->servings;
        $scaledIngredients = [];

        foreach ($this->ingredients as $ingredient) {
            $scaledIngredients[] = [
                'name' => $ingredient->ingredient_name,
                'amount' => round($ingredient->amount * $ratio, 2),
                'unit' => $ingredient->unit,
                'notes' => $ingredient->notes,
            ];
        }

        return $scaledIngredients;
    }

    /**
     * Получить похожие рецепты
     */
    public function getSimilarRecipes(int $limit = 5)
    {
        return static::published()
            ->where('id', '!=', $this->id)
            ->where(function ($query) {
                $query->where('category', $this->category)
                      ->orWhere('cuisine_type', $this->cuisine_type);
            })
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Проверка наличия видео
     */
    public function hasVideo(): bool
    {
        return !empty($this->video_url);
    }

    /**
     * Получить embed URL для видео
     */
    public function getVideoEmbedUrl(): ?string
    {
        if (!$this->hasVideo()) {
            return null;
        }

        // Преобразование YouTube URL в embed
        if (strpos($this->video_url, 'youtube.com') !== false || strpos($this->video_url, 'youtu.be') !== false) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $matches);
            if (isset($matches[1])) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
        }

        return $this->video_url;
    }
}
