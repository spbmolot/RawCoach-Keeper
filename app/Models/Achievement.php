<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'category',
        'condition_type',
        'condition_value',
        'sort_order',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    public function getCategoryNameAttribute(): string
    {
        return match ($this->category) {
            'nutrition' => 'Питание',
            'weight' => 'Вес',
            'streak' => 'Серии',
            'social' => 'Социальное',
            'exploration' => 'Исследование',
            default => $this->category,
        };
    }
}
