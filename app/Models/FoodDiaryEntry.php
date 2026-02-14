<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodDiaryEntry extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'meal_type',
        'recipe_id',
        'custom_name',
        'calories',
        'proteins',
        'fats',
        'carbs',
        'portion_size',
        'notes',
        'from_menu',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'calories' => 'decimal:1',
            'proteins' => 'decimal:1',
            'fats' => 'decimal:1',
            'carbs' => 'decimal:1',
            'portion_size' => 'decimal:2',
            'from_menu' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->recipe) {
            return $this->recipe->title;
        }
        return $this->custom_name ?? 'Без названия';
    }

    public function getMealTypeNameAttribute(): string
    {
        return match ($this->meal_type) {
            'breakfast' => 'Завтрак',
            'lunch' => 'Обед',
            'dinner' => 'Ужин',
            'snack' => 'Перекус',
            default => $this->meal_type,
        };
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
