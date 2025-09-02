<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DayMeal extends Model
{
    protected $fillable = [
        'day_id',
        'recipe_id',
        'meal_type', // breakfast, lunch, dinner, snack
        'meal_type_order', // порядок в рамках типа приема пищи
        'portion_size', // размер порции (если отличается от стандартного)
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'portion_size' => 'decimal:2',
        ];
    }

    /**
     * День, к которому относится прием пищи
     */
    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }

    /**
     * Рецепт для приема пищи
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * По типу приема пищи
     */
    public function scopeByMealType($query, string $mealType)
    {
        return $query->where('meal_type', $mealType);
    }

    /**
     * Завтраки
     */
    public function scopeBreakfast($query)
    {
        return $query->where('meal_type', 'breakfast');
    }

    /**
     * Обеды
     */
    public function scopeLunch($query)
    {
        return $query->where('meal_type', 'lunch');
    }

    /**
     * Ужины
     */
    public function scopeDinner($query)
    {
        return $query->where('meal_type', 'dinner');
    }

    /**
     * Перекусы
     */
    public function scopeSnack($query)
    {
        return $query->where('meal_type', 'snack');
    }

    /**
     * Получить название типа приема пищи
     */
    public function getMealTypeName(): string
    {
        $mealTypes = [
            'breakfast' => 'Завтрак',
            'lunch' => 'Обед',
            'dinner' => 'Ужин',
            'snack' => 'Перекус',
        ];

        return $mealTypes[$this->meal_type] ?? 'Неизвестно';
    }

    /**
     * Получить порядок типа приема пищи для сортировки
     */
    public function getMealTypeOrder(): int
    {
        $orders = [
            'breakfast' => 1,
            'snack' => 2,
            'lunch' => 3,
            'dinner' => 4,
        ];

        return $orders[$this->meal_type] ?? 999;
    }

    /**
     * Получить калории с учетом размера порции
     */
    public function getAdjustedCalories(): float
    {
        if (!$this->recipe) {
            return 0;
        }

        $multiplier = $this->portion_size ? ($this->portion_size / $this->recipe->servings) : 1;
        return round($this->recipe->calories * $multiplier, 1);
    }

    /**
     * Получить БЖУ с учетом размера порции
     */
    public function getAdjustedNutrition(): array
    {
        if (!$this->recipe) {
            return [
                'calories' => 0,
                'proteins' => 0,
                'fats' => 0,
                'carbs' => 0,
            ];
        }

        $multiplier = $this->portion_size ? ($this->portion_size / $this->recipe->servings) : 1;

        return [
            'calories' => round($this->recipe->calories * $multiplier, 1),
            'proteins' => round($this->recipe->proteins * $multiplier, 1),
            'fats' => round($this->recipe->fats * $multiplier, 1),
            'carbs' => round($this->recipe->carbs * $multiplier, 1),
        ];
    }

    /**
     * Получить ингредиенты с учетом размера порции
     */
    public function getAdjustedIngredients(): array
    {
        if (!$this->recipe || !$this->recipe->ingredients) {
            return [];
        }

        $multiplier = $this->portion_size ? ($this->portion_size / $this->recipe->servings) : 1;
        $adjustedIngredients = [];

        foreach ($this->recipe->ingredients as $ingredient) {
            $adjustedIngredients[] = [
                'name' => $ingredient->ingredient_name,
                'amount' => round($ingredient->amount * $multiplier, 2),
                'unit' => $ingredient->unit,
                'category' => $ingredient->category,
                'notes' => $ingredient->notes,
                'is_optional' => $ingredient->is_optional,
            ];
        }

        return $adjustedIngredients;
    }
}
