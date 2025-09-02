<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'month',
        'year',
        'calories_per_day',
        'is_published',
        'is_featured',
        'published_at',
        'cover_image',
        'pdf_file',
        'excel_file',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'calories_per_day' => 'integer',
        ];
    }

    /**
     * Дни меню
     */
    public function days(): HasMany
    {
        return $this->hasMany(Day::class)->orderBy('day_number');
    }

    /**
     * Планы, которым доступно это меню
     */
    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'menu_plan');
    }

    /**
     * Пользователи, добавившие в избранное
     */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorite_menus');
    }

    /**
     * Опубликованные меню
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where('published_at', '<=', now());
    }

    /**
     * Рекомендуемые меню
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Меню по месяцу и году
     */
    public function scopeByPeriod($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Текущий месяц
     */
    public function scopeCurrentMonth($query)
    {
        return $query->where('month', now()->month)
                    ->where('year', now()->year);
    }

    /**
     * Доступные для плана
     */
    public function scopeAvailableForPlan($query, Plan $plan)
    {
        return $query->whereHas('plans', function ($q) use ($plan) {
            $q->where('plan_id', $plan->id);
        });
    }

    /**
     * Проверка публикации
     */
    public function isPublished(): bool
    {
        return $this->is_published && 
               $this->published_at && 
               $this->published_at <= now();
    }

    /**
     * Проверка доступности для пользователя
     */
    public function isAvailableForUser(User $user): bool
    {
        if (!$this->isPublished()) {
            return false;
        }

        // Проверяем активную подписку пользователя
        $activeSubscription = $user->activeSubscription();
        if (!$activeSubscription) {
            return false;
        }

        // Проверяем доступность меню для плана пользователя
        return $this->plans()->where('plan_id', $activeSubscription->plan_id)->exists();
    }

    /**
     * Получить название месяца
     */
    public function getMonthName(): string
    {
        $months = [
            1 => 'Январь', 2 => 'Февраль', 3 => 'Март',
            4 => 'Апрель', 5 => 'Май', 6 => 'Июнь',
            7 => 'Июль', 8 => 'Август', 9 => 'Сентябрь',
            10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
        ];

        return $months[$this->month] ?? '';
    }

    /**
     * Полное название периода
     */
    public function getPeriodName(): string
    {
        return $this->getMonthName() . ' ' . $this->year;
    }

    /**
     * Количество дней в меню
     */
    public function getDaysCount(): int
    {
        return $this->days()->count();
    }

    /**
     * Общее количество рецептов
     */
    public function getRecipesCount(): int
    {
        return $this->days()
                   ->withCount('recipes')
                   ->get()
                   ->sum('recipes_count');
    }

    /**
     * Средняя калорийность
     */
    public function getAverageCalories(): float
    {
        $totalCalories = $this->days()
                             ->join('day_meals', 'days.id', '=', 'day_meals.day_id')
                             ->join('recipes', 'day_meals.recipe_id', '=', 'recipes.id')
                             ->sum('recipes.calories');

        $daysCount = $this->getDaysCount();
        
        return $daysCount > 0 ? round($totalCalories / $daysCount, 0) : 0;
    }

    /**
     * Проверка наличия файлов
     */
    public function hasPdfFile(): bool
    {
        return !empty($this->pdf_file) && file_exists(storage_path('app/public/' . $this->pdf_file));
    }

    public function hasExcelFile(): bool
    {
        return !empty($this->excel_file) && file_exists(storage_path('app/public/' . $this->excel_file));
    }

    /**
     * URL для скачивания PDF
     */
    public function getPdfDownloadUrl(): ?string
    {
        return $this->hasPdfFile() ? route('menu.download.pdf', $this) : null;
    }

    /**
     * URL для скачивания Excel
     */
    public function getExcelDownloadUrl(): ?string
    {
        return $this->hasExcelFile() ? route('menu.download.excel', $this) : null;
    }

    /**
     * Проверка избранного для пользователя
     */
    public function isFavoritedByUser(User $user): bool
    {
        return $this->favoritedByUsers()->where('user_id', $user->id)->exists();
    }

    /**
     * Получить список покупок для меню
     */
    public function getShoppingList(): array
    {
        $ingredients = [];

        foreach ($this->days as $day) {
            foreach ($day->meals as $meal) {
                if ($meal->recipe && $meal->recipe->ingredients) {
                    foreach ($meal->recipe->ingredients as $ingredient) {
                        $key = $ingredient->ingredient_name;
                        
                        if (!isset($ingredients[$key])) {
                            $ingredients[$key] = [
                                'name' => $ingredient->ingredient_name,
                                'unit' => $ingredient->unit,
                                'total_amount' => 0,
                                'category' => $ingredient->category ?? 'Прочее',
                            ];
                        }
                        
                        $ingredients[$key]['total_amount'] += $ingredient->amount;
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
}
