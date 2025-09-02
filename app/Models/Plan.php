<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'type', // month, year, personal
        'period_days',
        'price',
        'currency',
        'features',
        'is_popular',
        'is_active',
        'sort_order',
        'discount_percentage',
        'trial_days',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
        ];
    }

    /**
     * Подписки по этому плану
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Платежи по этому плану
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Активные планы
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Планы по типу
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Получить цену со скидкой
     */
    public function getDiscountedPrice(): float
    {
        if ($this->discount_percentage > 0) {
            return $this->price * (1 - $this->discount_percentage / 100);
        }
        return $this->price;
    }

    /**
     * Проверка является ли план персональным
     */
    public function isPersonal(): bool
    {
        return $this->type === 'personal';
    }

    /**
     * Проверка является ли план годовым
     */
    public function isYearly(): bool
    {
        return $this->type === 'year';
    }

    /**
     * Получить период в днях
     */
    public function getPeriodDays(): int
    {
        return match($this->type) {
            'month' => 30,
            'year' => 365,
            'personal' => $this->period_days ?? 30,
            default => 30,
        };
    }

    /**
     * Форматированная цена
     */
    public function getFormattedPrice(): string
    {
        $price = $this->getDiscountedPrice();
        return number_format($price, 0, ',', ' ') . ' ₽';
    }

    /**
     * Экономия при годовой подписке
     */
    public function getSavingsAmount(): float
    {
        if ($this->discount_percentage > 0) {
            return $this->price * ($this->discount_percentage / 100);
        }
        return 0;
    }
}
