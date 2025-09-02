<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponUsage extends Model
{
    protected $fillable = [
        'coupon_id',
        'user_id',
        'payment_id',
        'used_at',
        'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'used_at' => 'datetime',
            'discount_amount' => 'decimal:2',
        ];
    }

    /**
     * Купон
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Пользователь
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Платеж
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
