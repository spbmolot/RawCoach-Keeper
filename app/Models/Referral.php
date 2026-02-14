<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_id',
        'status',
        'referrer_ip',
        'referred_ip',
        'registered_at',
        'subscribed_at',
        'rewarded_at',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
            'subscribed_at' => 'datetime',
            'rewarded_at' => 'datetime',
        ];
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(ReferralReward::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'registered');
    }

    public function scopeRewarded($query)
    {
        return $query->where('status', 'rewarded');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'registered' => 'Зарегистрирован',
            'subscribed' => 'Оплатил',
            'rewarded' => 'Награда начислена',
            'expired' => 'Истёк',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'registered' => 'yellow',
            'subscribed' => 'blue',
            'rewarded' => 'green',
            'expired' => 'gray',
            default => 'gray',
        };
    }
}
