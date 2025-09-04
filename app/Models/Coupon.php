<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'description',
        'type', // percentage, fixed
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'used_count', // соответствует миграции
        'usage_limit_per_user', // соответствует миграции
        'valid_from', // соответствует миграции
        'valid_until', // соответствует миграции
        'is_active',
        'applicable_plans', // JSON array of plan IDs
        'created_by_type',
        'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'datetime', // соответствует миграции
            'valid_until' => 'datetime', // соответствует миграции
            'is_active' => 'boolean',
            'applicable_plans' => 'array',
            'value' => 'decimal:2',
            'minimum_amount' => 'decimal:2',
            'maximum_discount' => 'decimal:2',
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
                        $q->whereNull('valid_from')
                          ->orWhere('valid_from', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('valid_until')
                          ->orWhere('valid_until', '>', now());
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

        if ($this->valid_from && $this->valid_from > now()) {
            return false;
        }

        if ($this->valid_until && $this->valid_until <= now()) {
            return false;
        }

        return true;
    }

    /**
     * Проверка лимита использований
     */
    public function hasUsageLimit(): bool
    {
        return $this->usage_limit && $this->used_count >= $this->usage_limit;
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
        if ($this->usage_limit_per_user) {
            $userUsageCount = $this->usages()
                ->where('user_id', $user->id)
                ->count();
            
            if ($userUsageCount >= $this->usage_limit_per_user) {
                return false;
            }
        }

        // Проверка только для новых пользователей (можно добавить логику позже)
        // if ($this->first_time_only && $user->payments()->successful()->exists()) {
        //     return false;
        // }

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

        if ($this->type === 'fixed') {
            $discount = min($this->value, $amount);
            // Применяем максимальную скидку если она установлена
            if ($this->maximum_discount) {
                $discount = min($discount, $this->maximum_discount);
            }
            return $discount;
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
    public function markAsUsed(User $user, Payment $payment): void
    {
        $this->increment('used_count');
        
        CouponUsage::create([
            'coupon_id' => $this->id,
            'user_id' => $user->id,
            'payment_id' => $payment->id,
            'discount_amount' => $this->calculateDiscount($payment->original_amount ?? $payment->amount),
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

        if ($this->type === 'fixed') {
            return number_format($this->value, 0, ',', ' ') . ' ₽ скидка';
        }

        return 'Скидка';
    }

    /**
     * Проверка истечения срока
     */
    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until <= now();
    }

    /**
     * Дни до истечения
     */
    public function getDaysUntilExpiry(): ?int
    {
        if (!$this->valid_until) {
            return null;
        }

        return max(0, now()->diffInDays($this->valid_until, false));
    }

    /**
     * Получить количество использований
     */
    public function getUsageCount(): int
    {
        return $this->used_count ?? 0;
    }

    /**
     * Получить оставшиеся использования
     */
    public function getRemainingUsages(): ?int
    {
        if (!$this->usage_limit) {
            return null;
        }
        
        return max(0, $this->usage_limit - $this->getUsageCount());
    }
}
