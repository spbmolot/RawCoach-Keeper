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
        'type', // monthly, yearly, individual, trial
        'duration_days', // соответствует миграции
        'price',
        'original_price', // добавлено из миграции
        'currency',
        'features',
        'limits', // добавлено из миграции
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'limits' => 'array', // добавлено из миграции
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'original_price' => 'decimal:2', // добавлено из миграции
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
     * Получить цену со скидкой (если есть original_price)
     */
    public function getDiscountedPrice(): float
    {
        return $this->price; // цена уже с учетом скидки
    }

    /**
     * Получить размер скидки в рублях
     */
    public function getDiscountAmount(): float
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return $this->original_price - $this->price;
        }
        return 0;
    }

    /**
     * Получить процент скидки
     */
    public function getDiscountPercentage(): float
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100, 2);
        }
        return 0;
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
        return $this->type === 'yearly';
    }

    /**
     * Проверка является ли план месячным
     */
    public function isMonthly(): bool
    {
        return $this->type === 'monthly';
    }

    /**
     * Проверка является ли план пробным
     */
    public function isTrial(): bool
    {
        return $this->type === 'trial';
    }

    /**
     * Получить период в днях
     */
    public function getPeriodDays(): int
    {
        return $this->duration_days ?? match($this->type) {
            'monthly' => 30,
            'yearly' => 365,
            'trial' => 7,
            'personal' => 30,
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
     * Экономия при покупке этого плана
     */
    public function getSavingsAmount(): float
    {
        return $this->getDiscountAmount();
    }

    /**
     * Получить лимиты плана
     */
    public function getLimit(string $key, $default = null)
    {
        return $this->limits[$key] ?? $default;
    }

    /**
     * Проверить доступность функции
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }
}
