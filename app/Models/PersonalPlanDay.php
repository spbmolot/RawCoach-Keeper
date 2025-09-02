<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class PersonalPlanDay extends Model
{
    protected $fillable = [
        'personal_plan_id',
        'day_number',
        'title',
        'description',
        'total_calories',
        'total_proteins',
        'total_fats',
        'total_carbs',
        'notes',
        'is_completed',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
            'total_calories' => 'decimal:1',
            'total_proteins' => 'decimal:1',
            'total_fats' => 'decimal:1',
            'total_carbs' => 'decimal:1',
        ];
    }

    /**
     * Персональный план, к которому относится день
     */
    public function personalPlan(): BelongsTo
    {
        return $this->belongsTo(PersonalPlan::class);
    }

    /**
     * Рецепты дня с типами приемов пищи
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'personal_plan_day_recipes')
                    ->withPivot(['meal_type', 'order', 'portion_size'])
                    ->orderByPivot('meal_type')
                    ->orderByPivot('order');
    }

    /**
     * Завершенные дни
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * По номеру дня
     */
    public function scopeByDayNumber($query, int $dayNumber)
    {
        return $query->where('day_number', $dayNumber);
    }

    /**
     * Получить рецепты по типу приема пищи
     */
    public function getRecipesByMealType(string $mealType)
    {
        return $this->recipes()->wherePivot('meal_type', $mealType)->get();
    }

    /**
     * Завтраки
     */
    public function getBreakfasts()
    {
        return $this->getRecipesByMealType('breakfast');
    }

    /**
     * Обеды
     */
    public function getLunches()
    {
        return $this->getRecipesByMealType('lunch');
    }

    /**
     * Ужины
     */
    public function getDinners()
    {
        return $this->getRecipesByMealType('dinner');
    }

    /**
     * Перекусы
     */
    public function getSnacks()
    {
        return $this->getRecipesByMealType('snack');
    }

    /**
     * Отметить день как завершенный
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }

    /**
     * Отменить завершение дня
     */
    public function markAsIncomplete(): void
    {
        $this->update([
            'is_completed' => false,
            'completed_at' => null,
        ]);
    }

    /**
     * Добавить рецепт к дню
     */
    public function addRecipe(Recipe $recipe, string $mealType, int $order = 1, float $portionSize = null): void
    {
        $this->recipes()->attach($recipe->id, [
            'meal_type' => $mealType,
            'order' => $order,
            'portion_size' => $portionSize,
        ]);

        $this->recalculateNutrition();
    }

    /**
     * Удалить рецепт из дня
     */
    public function removeRecipe(Recipe $recipe): void
    {
        $this->recipes()->detach($recipe->id);
        $this->recalculateNutrition();
    }

    /**
     * Заменить рецепт
     */
    public function replaceRecipe(Recipe $oldRecipe, Recipe $newRecipe, string $mealType = null, int $order = null): void
    {
        $pivot = $this->recipes()->where('recipe_id', $oldRecipe->id)->first()?->pivot;
        
        if ($pivot) {
            $this->removeRecipe($oldRecipe);
            $this->addRecipe($newRecipe, $mealType ?? $pivot->meal_type, $order ?? $pivot->order, $pivot->portion_size);
        }
    }

    /**
     * Пересчитать БЖУ и калории дня
     */
    public function recalculateNutrition(): void
    {
        $totalCalories = 0;
        $totalProteins = 0;
        $totalFats = 0;
        $totalCarbs = 0;

        foreach ($this->recipes as $recipe) {
            $portionMultiplier = 1;
            
            if ($recipe->pivot->portion_size && $recipe->servings > 0) {
                $portionMultiplier = $recipe->pivot->portion_size / $recipe->servings;
            }

            $totalCalories += $recipe->calories * $portionMultiplier;
            $totalProteins += $recipe->proteins * $portionMultiplier;
            $totalFats += $recipe->fats * $portionMultiplier;
            $totalCarbs += $recipe->carbs * $portionMultiplier;
        }

        $this->update([
            'total_calories' => round($totalCalories, 1),
            'total_proteins' => round($totalProteins, 1),
            'total_fats' => round($totalFats, 1),
            'total_carbs' => round($totalCarbs, 1),
        ]);
    }

    /**
     * Получить полное название дня
     */
    public function getFullTitle(): string
    {
        $dayOfWeek = $this->getDayOfWeekName();
        $title = "День {$this->day_number}";
        
        if ($dayOfWeek) {
            $title .= " ({$dayOfWeek})";
        }
        
        if ($this->title) {
            $title .= " - {$this->title}";
        }

        return $title;
    }

    /**
     * Получить название дня недели
     */
    public function getDayOfWeekName(): ?string
    {
        if (!$this->personalPlan->start_date) {
            return null;
        }

        $daysOfWeek = [
            0 => 'Воскресенье', 1 => 'Понедельник', 2 => 'Вторник',
            3 => 'Среда', 4 => 'Четверг', 5 => 'Пятница', 6 => 'Суббота'
        ];

        $startDate = $this->personalPlan->start_date;
        $dayDate = $startDate->addDays($this->day_number - 1);
        
        return $daysOfWeek[$dayDate->dayOfWeek] ?? null;
    }

    /**
     * Получить дату дня
     */
    public function getDate(): ?\Carbon\Carbon
    {
        if (!$this->personalPlan->start_date) {
            return null;
        }

        return $this->personalPlan->start_date->addDays($this->day_number - 1);
    }

    /**
     * Проверить, является ли день текущим
     */
    public function isToday(): bool
    {
        $date = $this->getDate();
        return $date && $date->isToday();
    }

    /**
     * Проверить, прошел ли день
     */
    public function isPast(): bool
    {
        $date = $this->getDate();
        return $date && $date->isPast();
    }

    /**
     * Проверить, будущий ли день
     */
    public function isFuture(): bool
    {
        $date = $this->getDate();
        return $date && $date->isFuture();
    }

    /**
     * Получить список ингредиентов на день
     */
    public function getDayIngredients(): array
    {
        $ingredients = [];

        foreach ($this->recipes as $recipe) {
            $portionMultiplier = 1;
            
            if ($recipe->pivot->portion_size && $recipe->servings > 0) {
                $portionMultiplier = $recipe->pivot->portion_size / $recipe->servings;
            }

            if ($recipe->ingredients) {
                foreach ($recipe->ingredients as $ingredient) {
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
                    
                    $ingredients[$key]['total_amount'] += $ingredient->amount * $portionMultiplier;
                    
                    $mealTypeName = $this->getMealTypeName($recipe->pivot->meal_type);
                    if (!in_array($mealTypeName, $ingredients[$key]['meals'])) {
                        $ingredients[$key]['meals'][] = $mealTypeName;
                    }
                }
            }
        }

        return array_values($ingredients);
    }

    /**
     * Получить название типа приема пищи
     */
    private function getMealTypeName(string $mealType): string
    {
        $mealTypes = [
            'breakfast' => 'Завтрак',
            'lunch' => 'Обед',
            'dinner' => 'Ужин',
            'snack' => 'Перекус',
        ];

        return $mealTypes[$mealType] ?? 'Неизвестно';
    }

    /**
     * Проверить соответствие целевым показателям плана
     */
    public function checkTargetCompliance(): array
    {
        $plan = $this->personalPlan;
        
        return [
            'calories' => [
                'target' => $plan->target_calories,
                'actual' => $this->total_calories,
                'difference' => $this->total_calories - $plan->target_calories,
                'percentage' => $plan->target_calories > 0 ? round(($this->total_calories / $plan->target_calories) * 100, 1) : 0,
            ],
            'proteins' => [
                'target' => $plan->target_proteins,
                'actual' => $this->total_proteins,
                'difference' => $this->total_proteins - $plan->target_proteins,
                'percentage' => $plan->target_proteins > 0 ? round(($this->total_proteins / $plan->target_proteins) * 100, 1) : 0,
            ],
            'fats' => [
                'target' => $plan->target_fats,
                'actual' => $this->total_fats,
                'difference' => $this->total_fats - $plan->target_fats,
                'percentage' => $plan->target_fats > 0 ? round(($this->total_fats / $plan->target_fats) * 100, 1) : 0,
            ],
            'carbs' => [
                'target' => $plan->target_carbs,
                'actual' => $this->total_carbs,
                'difference' => $this->total_carbs - $plan->target_carbs,
                'percentage' => $plan->target_carbs > 0 ? round(($this->total_carbs / $plan->target_carbs) * 100, 1) : 0,
            ],
        ];
    }
}
