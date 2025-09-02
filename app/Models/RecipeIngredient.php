<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeIngredient extends Model
{
    protected $fillable = [
        'recipe_id',
        'ingredient_name',
        'amount',
        'unit',
        'category',
        'notes',
        'is_optional',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_optional' => 'boolean',
        ];
    }

    /**
     * Рецепт, к которому относится ингредиент
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * По категории
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Обязательные ингредиенты
     */
    public function scopeRequired($query)
    {
        return $query->where('is_optional', false);
    }

    /**
     * Опциональные ингредиенты
     */
    public function scopeOptional($query)
    {
        return $query->where('is_optional', true);
    }

    /**
     * Получить форматированное количество с единицей измерения
     */
    public function getFormattedAmount(): string
    {
        $amount = $this->amount;
        
        // Убираем лишние нули после запятой
        if ($amount == floor($amount)) {
            $amount = (int) $amount;
        }
        
        return $amount . ' ' . $this->unit;
    }

    /**
     * Получить полное описание ингредиента
     */
    public function getFullDescription(): string
    {
        $description = $this->getFormattedAmount() . ' ' . $this->ingredient_name;
        
        if ($this->notes) {
            $description .= ' (' . $this->notes . ')';
        }
        
        if ($this->is_optional) {
            $description .= ' (по желанию)';
        }
        
        return $description;
    }

    /**
     * Получить название категории
     */
    public function getCategoryName(): string
    {
        $categories = [
            'meat' => 'Мясо и птица',
            'fish' => 'Рыба и морепродукты',
            'dairy' => 'Молочные продукты',
            'vegetables' => 'Овощи',
            'fruits' => 'Фрукты',
            'grains' => 'Крупы и злаки',
            'spices' => 'Специи и приправы',
            'oils' => 'Масла и жиры',
            'nuts' => 'Орехи и семена',
            'herbs' => 'Зелень',
            'other' => 'Прочее',
        ];

        return $categories[$this->category] ?? 'Прочее';
    }

    /**
     * Масштабировать количество
     */
    public function scaleAmount(float $ratio): float
    {
        return round($this->amount * $ratio, 2);
    }
}
