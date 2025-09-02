<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Day extends Model
{
    protected $fillable = [
        'menu_id',
        'day_number',
        'title',
        'description',
        'total_calories',
        'total_proteins',
        'total_fats',
        'total_carbs',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'total_calories' => 'decimal:1',
            'total_proteins' => 'decimal:1',
            'total_fats' => 'decimal:1',
            'total_carbs' => 'decimal:1',
        ];
    }

    /**
     * Меню, к которому относится день
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Приемы пищи в этот день
     */
    public function meals(): HasMany
    {
        return $this->hasMany(DayMeal::class)->orderBy('meal_type_order');
    }

    /**
     * Завтраки
     */
    public function breakfasts(): HasMany
    {
        return $this->hasMany(DayMeal::class)->where('meal_type', 'breakfast');
    }

    /**
     * Обеды
     */
    public function lunches(): HasMany
    {
        return $this->hasMany(DayMeal::class)->where('meal_type', 'lunch');
    }

    /**
     * Ужины
     */
    public function dinners(): HasMany
    {
        return $this->hasMany(DayMeal::class)->where('meal_type', 'dinner');
    }

    /**
     * Перекусы
     */
    public function snacks(): HasMany
    {
        return $this->hasMany(DayMeal::class)->where('meal_type', 'snack');
    }

    /**
     * Активные дни
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * По номеру дня
     */
    public function scopeByDayNumber($query, int $dayNumber)
    {
        return $query->where('day_number', $dayNumber);
    }

    /**
     * Получить все рецепты дня
     */
    public function recipes()
    {
        return Recipe::whereIn('id', $this->meals()->pluck('recipe_id'));
    }

    /**
     * Пересчитать БЖУ и калории
     */
    public function recalculateNutrition(): void
    {
        $totalCalories = 0;
        $totalProteins = 0;
        $totalFats = 0;
        $totalCarbs = 0;

        foreach ($this->meals as $meal) {
            if ($meal->recipe) {
                $totalCalories += $meal->recipe->calories;
                $totalProteins += $meal->recipe->proteins;
                $totalFats += $meal->recipe->fats;
                $totalCarbs += $meal->recipe->carbs;
            }
        }

        $this->update([
            'total_calories' => $totalCalories,
            'total_proteins' => $totalProteins,
            'total_fats' => $totalFats,
            'total_carbs' => $totalCarbs,
        ]);
    }

    /**
     * Получить название дня недели
     */
    public function getDayOfWeekName(): string
    {
        // Предполагаем, что день 1 - это понедельник
        $daysOfWeek = [
            1 => 'Понедельник', 2 => 'Вторник', 3 => 'Среда',
            4 => 'Четверг', 5 => 'Пятница', 6 => 'Суббота', 7 => 'Воскресенье'
        ];

        $dayOfWeek = (($this->day_number - 1) % 7) + 1;
        return $daysOfWeek[$dayOfWeek] ?? '';
    }

    /**
     * Полное название дня
     */
    public function getFullTitle(): string
    {
        $dayOfWeek = $this->getDayOfWeekName();
        return "День {$this->day_number} ({$dayOfWeek})";
    }

    /**
     * Проверка завершенности дня (все приемы пищи заполнены)
     */
    public function isComplete(): bool
    {
        $requiredMealTypes = ['breakfast', 'lunch', 'dinner'];
        
        foreach ($requiredMealTypes as $mealType) {
            if (!$this->meals()->where('meal_type', $mealType)->exists()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Получить приемы пищи по типу
     */
    public function getMealsByType(string $mealType): \Illuminate\Database\Eloquent\Collection
    {
        return $this->meals()->where('meal_type', $mealType)->get();
    }

    /**
     * Добавить прием пищи
     */
    public function addMeal(Recipe $recipe, string $mealType, int $order = 0): DayMeal
    {
        if ($order === 0) {
            $order = $this->meals()->where('meal_type', $mealType)->max('meal_type_order') + 1;
        }

        return $this->meals()->create([
            'recipe_id' => $recipe->id,
            'meal_type' => $mealType,
            'meal_type_order' => $order,
        ]);
    }

    /**
     * Получить список ингредиентов на день
     */
    public function getDayIngredients(): array
    {
        $ingredients = [];

        foreach ($this->meals as $meal) {
            if ($meal->recipe && $meal->recipe->ingredients) {
                foreach ($meal->recipe->ingredients as $ingredient) {
                    $key = $ingredient->ingredient_name . '_' . $ingredient->unit;
                    
                    if (!isset($ingredients[$key])) {
                        $ingredients[$key] = [
                            'name' => $ingredient->ingredient_name,
                            'unit' => $ingredient->unit,
                            'total_amount' => 0,
                            'category' => $ingredient->category ?? 'Прочее',
                            'meals' => [],
                        ];
                    }
                    
                    $ingredients[$key]['total_amount'] += $ingredient->amount;
                    $ingredients[$key]['meals'][] = $meal->getMealTypeName();
                }
            }
        }

        return array_values($ingredients);
    }

    /**
     * Проверка доступности для пользователя
     */
    public function isAvailableForUser(User $user): bool
    {
        return $this->menu->isAvailableForUser($user);
    }
}
