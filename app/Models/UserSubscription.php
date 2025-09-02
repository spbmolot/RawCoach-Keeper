<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'status', // active, cancelled, expired, grace_period, pending
        'started_at',
        'ends_at',
        'cancelled_at',
        'auto_renew',
        'provider', // yookassa, cloudpayments, tinkoff
        'external_id',
        'trial_ends_at',
        'grace_period_ends_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'grace_period_ends_at' => 'datetime',
            'auto_renew' => 'boolean',
            'metadata' => 'array',
        ];
    }

    /**
     * Пользователь подписки
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * План подписки
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Платежи по подписке
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'subscription_id');
    }

    /**
     * Активные подписки
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('ends_at', '>', now());
    }

    /**
     * Подписки в grace period
     */
    public function scopeInGracePeriod($query)
    {
        return $query->where('status', 'grace_period')
                    ->where('grace_period_ends_at', '>', now());
    }

    /**
     * Подписки для автопродления
     */
    public function scopeForRenewal($query)
    {
        return $query->where('auto_renew', true)
                    ->where('status', 'active')
                    ->whereDate('ends_at', '<=', now()->addDay());
    }

    /**
     * Проверка активности подписки
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at > now();
    }

    /**
     * Проверка trial периода
     */
    public function isInTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at > now();
    }

    /**
     * Проверка grace period
     */
    public function isInGracePeriod(): bool
    {
        return $this->status === 'grace_period' && 
               $this->grace_period_ends_at && 
               $this->grace_period_ends_at > now();
    }

    /**
     * Отмена подписки
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false,
        ]);
    }

    /**
     * Продление подписки
     */
    public function renew(int $days = null): void
    {
        $days = $days ?? $this->plan->getPeriodDays();
        
        $this->update([
            'status' => 'active',
            'ends_at' => $this->ends_at->addDays($days),
            'grace_period_ends_at' => null,
        ]);
    }

    /**
     * Перевод в grace period
     */
    public function enterGracePeriod(int $graceDays = 3): void
    {
        $this->update([
            'status' => 'grace_period',
            'grace_period_ends_at' => now()->addDays($graceDays),
        ]);
    }

    /**
     * Дни до окончания подписки
     */
    public function getDaysUntilExpiry(): int
    {
        return max(0, now()->diffInDays($this->ends_at, false));
    }

    /**
     * Получить статус для отображения
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'active' => 'Активна',
            'cancelled' => 'Отменена',
            'expired' => 'Истекла',
            'grace_period' => 'Льготный период',
            'pending' => 'Ожидает оплаты',
            default => 'Неизвестно',
        };
    }

    /**
     * Можно ли продлить подписку
     */
    public function canBeRenewed(): bool
    {
        return in_array($this->status, ['active', 'grace_period', 'expired']);
    }
}
