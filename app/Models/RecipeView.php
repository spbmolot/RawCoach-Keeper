<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeView extends Model
{
    protected $fillable = [
        'user_id',
        'recipe_id',
        'ip_address',
        'user_agent',
    ];

    /**
     * Рецепт
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Пользователь
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
