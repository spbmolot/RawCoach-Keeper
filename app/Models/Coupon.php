<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type', // percentage, fixed_amount
        'value',
        'currency',
        'minimum_amount',
        'usage_limit',
        'usage_count',
        'user_usage_limit',
        'starts_at',
        'expires_at',
        'is_active',
        'applicable_plans', // JSON array of plan IDs
        'first_time_only',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
            'first_time_only' => 'boolean',
            'applicable_plans' => 'array',
            'value' => 'decimal:2',
            'minimum_amount' => 'decimal:2',
        ];
    }

    /**
     * Платежи с этим купоном
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Использования купона пользователями
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Активные купоны
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Поиск по коду
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', strtoupper($code));
    }

    /**
     * Проверка активности купона
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at > now()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at <= now()) {
            return false;
        }

        return true;
    }

    /**
     * Проверка лимита использований
     */
    public function hasUsageLimit(): bool
    {
        return $this->usage_limit && $this->usage_count >= $this->usage_limit;
    }

    /**
     * Проверка возможности использования пользователем
     */
    public function canBeUsedByUser(User $user, Plan $plan = null): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->hasUsageLimit()) {
            return false;
        }

        // Проверка лимита на пользователя
        if ($this->user_usage_limit) {
            $userUsageCount = $this->usages()
                ->where('user_id', $user->id)
                ->count();
            
            if ($userUsageCount >= $this->user_usage_limit) {
                return false;
            }
        }

        // Проверка только для новых пользователей
        if ($this->first_time_only && $user->payments()->successful()->exists()) {
            return false;
        }

        // Проверка применимости к плану
        if ($plan && $this->applicable_plans && !in_array($plan->id, $this->applicable_plans)) {
            return false;
        }

        return true;
    }

    /**
     * Рассчитать скидку для суммы
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->minimum_amount && $amount < $this->minimum_amount) {
            return 0;
        }

        if ($this->type === 'percentage') {
            return $amount * ($this->value / 100);
        }

        if ($this->type === 'fixed_amount') {
            return min($this->value, $amount);
        }

        return 0;
    }

    /**
     * Применить купон к платежу
     */
    public function applyToPayment(User $user, float $amount, Plan $plan = null): array
    {
        if (!$this->canBeUsedByUser($user, $plan)) {
            return [
                'success' => false,
                'message' => 'Купон недоступен для использования',
                'discount' => 0,
                'final_amount' => $amount,
            ];
        }

        $discount = $this->calculateDiscount($amount);
        $finalAmount = $amount - $discount;

        return [
            'success' => true,
            'message' => 'Купон применен успешно',
            'discount' => $discount,
            'final_amount' => max(0, $finalAmount),
        ];
    }

    /**
     * Отметить использование купона
     */
    public function markAsUsed(User $user): void
    {
        $this->increment('usage_count');
        
        CouponUsage::create([
            'coupon_id' => $this->id,
            'user_id' => $user->id,
            'used_at' => now(),
        ]);
    }

    /**
     * Получить описание скидки
     */
    public function getDiscountDescription(): string
    {
        if ($this->type === 'percentage') {
            return $this->value . '% скидка';
        }

        if ($this->type === 'fixed_amount') {
            return number_format($this->value, 0, ',', ' ') . ' ₽ скидка';
        }

        return 'Скидка';
    }

    /**
     * Проверка истечения срока
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at <= now();
    }

    /**
     * Дни до истечения
     */
    public function getDaysUntilExpiry(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        return max(0, now()->diffInDays($this->expires_at, false));
    }
}
