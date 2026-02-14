<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMealSwap extends Model
{
    protected $fillable = [
        'user_id',
        'day_meal_id',
        'original_recipe_id',
        'replacement_recipe_id',
        'reason',
    ];

    /**
     * Пользователь, сделавший замену
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Приём пищи, в котором произведена замена
     */
    public function dayMeal(): BelongsTo
    {
        return $this->belongsTo(DayMeal::class);
    }

    /**
     * Оригинальный рецепт (который заменили)
     */
    public function originalRecipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class, 'original_recipe_id');
    }

    /**
     * Рецепт-замена
     */
    public function replacementRecipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class, 'replacement_recipe_id');
    }

    /**
     * Замены конкретного пользователя
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Замены для конкретного дня
     */
    public function scopeForDay($query, int $dayId)
    {
        return $query->whereHas('dayMeal', fn($q) => $q->where('day_id', $dayId));
    }
}
