<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'plan_id',
        'coupon_id',
        'provider',
        'external_id',
        'amount',
        'original_amount',
        'discount_amount',
        'currency',
        'status',
        'type', // subscription, renewal, one_time
        'payload',
        'metadata',
        'payment_url',
        'webhook_payload',
        'paid_at',
        'failed_at',
        'refunded_at',
        'processed_at',
        'refund_amount',
        'failure_reason',
        'description',
    ];

    /**
     * Пользователь, которому принадлежит платёж.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Подписка, связанная с платёжом.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class);
    }

    /**
     * Тарифный план, связанный с платёжом.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Купон, использованный в платеже
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Атрибуты, которые должны быть приведены к определённым типам.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'metadata' => 'array',
            'webhook_payload' => 'array',
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
            'refunded_at' => 'datetime',
            'processed_at' => 'datetime',
            'amount' => 'decimal:2',
            'original_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
        ];
    }

    /**
     * Успешные платежи
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Неуспешные платежи
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Платежи по провайдеру
     */
    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Проверка успешности платежа
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Проверка возврата
     */
    public function isRefunded(): bool
    {
        return $this->refunded_at !== null;
    }

    /**
     * Получить статус для отображения
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Ожидает оплаты',
            'paid' => 'Оплачен',
            'failed' => 'Неуспешный',
            'cancelled' => 'Отменен',
            'refunded' => 'Возвращен',
            default => 'Неизвестно',
        };
    }

    /**
     * Форматированная сумма
     */
    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 2, ',', ' ') . ' ' . strtoupper($this->currency);
    }

    /**
     * Получить экономию от купона
     */
    public function getSavingsAmount(): float
    {
        return $this->discount_amount ?? 0;
    }

    /**
     * Обновить статус платежа
     */
    public function updateStatus(string $status, array $webhookData = []): void
    {
        $updateData = [
            'status' => $status,
            'webhook_payload' => $webhookData,
        ];

        if ($status === 'paid' && !$this->paid_at) {
            $updateData['paid_at'] = now();
        } elseif ($status === 'failed' && !$this->failed_at) {
            $updateData['failed_at'] = now();
        }

        $this->update($updateData);
    }

    /**
     * Создать возврат
     */
    public function refund(float $amount = null): void
    {
        $refundAmount = $amount ?? $this->amount;
        
        $this->update([
            'status' => 'refunded',
            'refunded_at' => now(),
            'refund_amount' => $refundAmount,
        ]);
    }
}
