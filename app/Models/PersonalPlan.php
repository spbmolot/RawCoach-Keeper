<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonalPlan extends Model
{
    protected $fillable = [
        'user_id',
        'questionnaire_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'target_calories',
        'target_proteins',
        'target_fats',
        'target_carbs',
        'duration_days',
        'status', // draft, active, completed, paused
        'generated_by', // ai, nutritionist
        'approved_by', // ID нутрициолога
        'approved_at',
        'notes',
        'is_public', // можно ли показывать другим пользователям как пример
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'approved_at' => 'datetime',
            'is_public' => 'boolean',
            'target_calories' => 'decimal:1',
            'target_proteins' => 'decimal:1',
            'target_fats' => 'decimal:1',
            'target_carbs' => 'decimal:1',
        ];
    }

    /**
     * Пользователь, которому принадлежит план
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Анкета, на основе которой создан план
     */
    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    /**
     * Дни персонального плана
     */
    public function days(): HasMany
    {
        return $this->hasMany(PersonalPlanDay::class)->orderBy('day_number');
    }

    /**
     * Нутрициолог, одобривший план
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Активные планы
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * По статусу
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Одобренные планы
     */
    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    /**
     * Публичные планы
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Проверка активности плана
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->start_date <= now()->toDateString() && 
               $this->end_date >= now()->toDateString();
    }

    /**
     * Проверка завершенности плана
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed' || $this->end_date < now()->toDateString();
    }

    /**
     * Проверка одобрения
     */
    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    /**
     * Получить текущий день плана
     */
    public function getCurrentDay(): ?PersonalPlanDay
    {
        if (!$this->isActive()) {
            return null;
        }

        $daysSinceStart = now()->diffInDays($this->start_date) + 1;
        
        return $this->days()->where('day_number', $daysSinceStart)->first();
    }

    /**
     * Получить день по номеру
     */
    public function getDayByNumber(int $dayNumber): ?PersonalPlanDay
    {
        return $this->days()->where('day_number', $dayNumber)->first();
    }

    /**
     * Получить прогресс выполнения плана (в процентах)
     */
    public function getProgress(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $totalDays = $this->start_date->diffInDays($this->end_date) + 1;
        $daysPassed = max(0, now()->diffInDays($this->start_date));
        
        return min(100, round(($daysPassed / $totalDays) * 100));
    }

    /**
     * Получить оставшиеся дни
     */
    public function getRemainingDays(): int
    {
        if (!$this->end_date || $this->isCompleted()) {
            return 0;
        }

        return max(0, now()->diffInDays($this->end_date, false));
    }

    /**
     * Получить название статуса
     */
    public function getStatusName(): string
    {
        $statuses = [
            'draft' => 'Черновик',
            'active' => 'Активный',
            'completed' => 'Завершен',
            'paused' => 'Приостановлен',
        ];

        return $statuses[$this->status] ?? 'Неизвестно';
    }

    /**
     * Одобрить план
     */
    public function approve(User $nutritionist, string $notes = null): void
    {
        $this->update([
            'approved_by' => $nutritionist->id,
            'approved_at' => now(),
            'notes' => $notes,
        ]);
    }

    /**
     * Активировать план
     */
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'start_date' => $this->start_date ?? now()->toDateString(),
        ]);
    }

    /**
     * Завершить план
     */
    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'end_date' => $this->end_date ?? now()->toDateString(),
        ]);
    }

    /**
     * Приостановить план
     */
    public function pause(): void
    {
        $this->update(['status' => 'paused']);
    }

    /**
     * Возобновить план
     */
    public function resume(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Рассчитать средние показатели БЖУ по плану
     */
    public function getAverageNutrition(): array
    {
        $totalCalories = 0;
        $totalProteins = 0;
        $totalFats = 0;
        $totalCarbs = 0;
        $daysCount = $this->days()->count();

        if ($daysCount === 0) {
            return [
                'calories' => 0,
                'proteins' => 0,
                'fats' => 0,
                'carbs' => 0,
            ];
        }

        foreach ($this->days as $day) {
            $totalCalories += $day->total_calories ?? 0;
            $totalProteins += $day->total_proteins ?? 0;
            $totalFats += $day->total_fats ?? 0;
            $totalCarbs += $day->total_carbs ?? 0;
        }

        return [
            'calories' => round($totalCalories / $daysCount, 1),
            'proteins' => round($totalProteins / $daysCount, 1),
            'fats' => round($totalFats / $daysCount, 1),
            'carbs' => round($totalCarbs / $daysCount, 1),
        ];
    }

    /**
     * Проверить соответствие целевым показателям
     */
    public function checkTargetCompliance(): array
    {
        $average = $this->getAverageNutrition();
        
        return [
            'calories' => [
                'target' => $this->target_calories,
                'actual' => $average['calories'],
                'difference' => $average['calories'] - $this->target_calories,
                'percentage' => $this->target_calories > 0 ? round(($average['calories'] / $this->target_calories) * 100, 1) : 0,
            ],
            'proteins' => [
                'target' => $this->target_proteins,
                'actual' => $average['proteins'],
                'difference' => $average['proteins'] - $this->target_proteins,
                'percentage' => $this->target_proteins > 0 ? round(($average['proteins'] / $this->target_proteins) * 100, 1) : 0,
            ],
            'fats' => [
                'target' => $this->target_fats,
                'actual' => $average['fats'],
                'difference' => $average['fats'] - $this->target_fats,
                'percentage' => $this->target_fats > 0 ? round(($average['fats'] / $this->target_fats) * 100, 1) : 0,
            ],
            'carbs' => [
                'target' => $this->target_carbs,
                'actual' => $average['carbs'],
                'difference' => $average['carbs'] - $this->target_carbs,
                'percentage' => $this->target_carbs > 0 ? round(($average['carbs'] / $this->target_carbs) * 100, 1) : 0,
            ],
        ];
    }

    /**
     * Получить список покупок для плана
     */
    public function getShoppingList(int $fromDay = 1, int $toDay = null): array
    {
        $toDay = $toDay ?? $this->duration_days;
        $ingredients = [];

        $days = $this->days()
                    ->where('day_number', '>=', $fromDay)
                    ->where('day_number', '<=', $toDay)
                    ->with(['recipes.ingredients'])
                    ->get();

        foreach ($days as $day) {
            foreach ($day->recipes as $recipe) {
                if ($recipe->ingredients) {
                    foreach ($recipe->ingredients as $ingredient) {
                        $key = $ingredient->ingredient_name . '_' . $ingredient->unit;
                        
                        if (!isset($ingredients[$key])) {
                            $ingredients[$key] = [
                                'name' => $ingredient->ingredient_name,
                                'unit' => $ingredient->unit,
                                'total_amount' => 0,
                                'category' => $ingredient->category ?? 'Прочее',
                                'days' => [],
                            ];
                        }
                        
                        $ingredients[$key]['total_amount'] += $ingredient->amount;
                        if (!in_array($day->day_number, $ingredients[$key]['days'])) {
                            $ingredients[$key]['days'][] = $day->day_number;
                        }
                    }
                }
            }
        }

        // Группировка по категориям
        $grouped = [];
        foreach ($ingredients as $ingredient) {
            $category = $ingredient['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $ingredient;
        }

        return $grouped;
    }

    /**
     * Клонировать план для другого пользователя
     */
    public function cloneForUser(User $user, Questionnaire $questionnaire = null): PersonalPlan
    {
        $newPlan = $this->replicate();
        $newPlan->user_id = $user->id;
        $newPlan->questionnaire_id = $questionnaire?->id;
        $newPlan->status = 'draft';
        $newPlan->approved_by = null;
        $newPlan->approved_at = null;
        $newPlan->start_date = null;
        $newPlan->end_date = null;
        $newPlan->save();

        // Клонируем дни плана
        foreach ($this->days as $day) {
            $newDay = $day->replicate();
            $newDay->personal_plan_id = $newPlan->id;
            $newDay->save();

            // Клонируем рецепты дня
            foreach ($day->recipes as $recipe) {
                $newPlan->days()->find($newDay->id)->recipes()->attach($recipe->id, [
                    'meal_type' => $recipe->pivot->meal_type ?? 'lunch',
                    'order' => $recipe->pivot->order ?? 1,
                ]);
            }
        }

        return $newPlan;
    }
}
