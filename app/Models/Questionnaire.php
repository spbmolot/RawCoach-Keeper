<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Questionnaire extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'current_weight',
        'target_weight',
        'height',
        'age',
        'gender',
        'activity_level',
        'goal', // lose_weight, maintain_weight, gain_muscle
        'dietary_preferences', // JSON array
        'allergens', // JSON array
        'dislikes', // JSON array
        'health_conditions', // JSON array
        'medications', // JSON array
        'meal_preferences', // JSON: breakfast_time, lunch_time, dinner_time, snacks_count
        'cooking_skills', // beginner, intermediate, advanced
        'cooking_time_available', // minutes per day
        'budget_range', // low, medium, high
        'kitchen_equipment', // JSON array
        'additional_notes',
        'completed_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'dietary_preferences' => 'array',
            'allergens' => 'array',
            'dislikes' => 'array',
            'health_conditions' => 'array',
            'medications' => 'array',
            'meal_preferences' => 'array',
            'kitchen_equipment' => 'array',
            'completed_at' => 'datetime',
            'is_active' => 'boolean',
            'current_weight' => 'decimal:1',
            'target_weight' => 'decimal:1',
            'height' => 'integer',
            'age' => 'integer',
            'cooking_time_available' => 'integer',
        ];
    }

    /**
     * Пользователь, заполнивший анкету
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Персональные планы, созданные на основе анкеты
     */
    public function personalPlans(): HasMany
    {
        return $this->hasMany(PersonalPlan::class);
    }

    /**
     * Завершенные анкеты
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    /**
     * Активные анкеты
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * По цели
     */
    public function scopeByGoal($query, string $goal)
    {
        return $query->where('goal', $goal);
    }

    /**
     * Проверка завершенности анкеты
     */
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    /**
     * Отметить анкету как завершенную
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'completed_at' => now(),
            'is_active' => true,
        ]);
    }

    /**
     * Рассчитать базовый метаболизм (BMR)
     */
    public function calculateBMR(): float
    {
        if (!$this->current_weight || !$this->height || !$this->age) {
            return 0;
        }

        // Формула Миффлина-Сан Жеора
        if ($this->gender === 'male') {
            return 10 * $this->current_weight + 6.25 * $this->height - 5 * $this->age + 5;
        } else {
            return 10 * $this->current_weight + 6.25 * $this->height - 5 * $this->age - 161;
        }
    }

    /**
     * Рассчитать суточную норму калорий
     */
    public function calculateDailyCalories(): float
    {
        $bmr = $this->calculateBMR();
        
        if ($bmr === 0) {
            return 0;
        }

        // Коэффициенты активности
        $activityMultipliers = [
            'sedentary' => 1.2,     // Малоподвижный
            'light' => 1.375,       // Легкая активность
            'moderate' => 1.55,     // Умеренная активность
            'active' => 1.725,      // Высокая активность
            'very_active' => 1.9,   // Очень высокая активность
        ];

        $multiplier = $activityMultipliers[$this->activity_level] ?? 1.2;
        $maintenanceCalories = $bmr * $multiplier;

        // Корректировка в зависимости от цели
        switch ($this->goal) {
            case 'lose_weight':
                return $maintenanceCalories - 500; // Дефицит 500 ккал для потери 0.5 кг в неделю
            case 'gain_muscle':
                return $maintenanceCalories + 300; // Профицит 300 ккал
            case 'maintain_weight':
            default:
                return $maintenanceCalories;
        }
    }

    /**
     * Рассчитать распределение БЖУ
     */
    public function calculateMacroDistribution(): array
    {
        $totalCalories = $this->calculateDailyCalories();
        
        if ($totalCalories === 0) {
            return [
                'proteins' => 0,
                'fats' => 0,
                'carbs' => 0,
            ];
        }

        // Стандартное распределение БЖУ в зависимости от цели
        switch ($this->goal) {
            case 'lose_weight':
                $proteinPercent = 0.35;
                $fatPercent = 0.25;
                $carbPercent = 0.40;
                break;
            case 'gain_muscle':
                $proteinPercent = 0.30;
                $fatPercent = 0.25;
                $carbPercent = 0.45;
                break;
            case 'maintain_weight':
            default:
                $proteinPercent = 0.25;
                $fatPercent = 0.30;
                $carbPercent = 0.45;
                break;
        }

        return [
            'proteins' => round(($totalCalories * $proteinPercent) / 4, 1), // 1г белка = 4 ккал
            'fats' => round(($totalCalories * $fatPercent) / 9, 1),         // 1г жира = 9 ккал
            'carbs' => round(($totalCalories * $carbPercent) / 4, 1),       // 1г углеводов = 4 ккал
        ];
    }

    /**
     * Получить рекомендации по времени приготовления
     */
    public function getCookingTimeRecommendation(): string
    {
        if (!$this->cooking_time_available) {
            return 'Не указано';
        }

        if ($this->cooking_time_available <= 30) {
            return 'Быстрые рецепты (до 30 минут)';
        } elseif ($this->cooking_time_available <= 60) {
            return 'Средние по времени рецепты (30-60 минут)';
        } else {
            return 'Сложные рецепты (более 60 минут)';
        }
    }

    /**
     * Получить название цели
     */
    public function getGoalName(): string
    {
        $goals = [
            'lose_weight' => 'Похудение',
            'maintain_weight' => 'Поддержание веса',
            'gain_muscle' => 'Набор мышечной массы',
        ];

        return $goals[$this->goal] ?? 'Не указано';
    }

    /**
     * Получить название уровня активности
     */
    public function getActivityLevelName(): string
    {
        $levels = [
            'sedentary' => 'Малоподвижный',
            'light' => 'Легкая активность',
            'moderate' => 'Умеренная активность',
            'active' => 'Высокая активность',
            'very_active' => 'Очень высокая активность',
        ];

        return $levels[$this->activity_level] ?? 'Не указано';
    }

    /**
     * Получить название уровня кулинарных навыков
     */
    public function getCookingSkillsName(): string
    {
        $skills = [
            'beginner' => 'Начинающий',
            'intermediate' => 'Средний',
            'advanced' => 'Продвинутый',
        ];

        return $skills[$this->cooking_skills] ?? 'Не указано';
    }

    /**
     * Получить название бюджетного диапазона
     */
    public function getBudgetRangeName(): string
    {
        $ranges = [
            'low' => 'Экономный',
            'medium' => 'Средний',
            'high' => 'Высокий',
        ];

        return $ranges[$this->budget_range] ?? 'Не указано';
    }

    /**
     * Проверить совместимость рецепта с анкетой
     */
    public function isRecipeCompatible(Recipe $recipe): bool
    {
        // Проверка аллергенов
        if ($this->allergens && $recipe->allergens) {
            if (array_intersect($this->allergens, $recipe->allergens)) {
                return false;
            }
        }

        // Проверка нелюбимых продуктов
        if ($this->dislikes && $recipe->ingredients()->count() > 0) {
            foreach ($recipe->ingredients as $ingredient) {
                if (in_array($ingredient->ingredient_name, $this->dislikes)) {
                    return false;
                }
            }
        }

        // Проверка времени приготовления
        if ($this->cooking_time_available && $recipe->total_time > $this->cooking_time_available) {
            return false;
        }

        // Проверка диетических предпочтений
        if ($this->dietary_preferences) {
            foreach ($this->dietary_preferences as $preference) {
                if (!in_array($preference, $recipe->dietary_tags ?? [])) {
                    // Если у пользователя есть строгие диетические требования, рецепт должен им соответствовать
                    if (in_array($preference, ['vegan', 'vegetarian', 'gluten_free', 'dairy_free'])) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Получить прогресс заполнения анкеты (в процентах)
     */
    public function getCompletionProgress(): int
    {
        $requiredFields = [
            'current_weight', 'target_weight', 'height', 'age', 'gender',
            'activity_level', 'goal', 'dietary_preferences', 'cooking_skills'
        ];

        $filledFields = 0;
        foreach ($requiredFields as $field) {
            if (!empty($this->$field)) {
                $filledFields++;
            }
        }

        return round(($filledFields / count($requiredFields)) * 100);
    }
}
