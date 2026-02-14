<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralReward extends Model
{
    protected $fillable = [
        'user_id',
        'referral_id',
        'type',
        'days_added',
        'discount_percent',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'referrer_days' => '+' . $this->days_added . ' дн. за реферала',
            'referee_discount' => $this->discount_percent . '% скидка',
            'milestone_3' => 'Веха: 3 реферала',
            'milestone_5' => 'Веха: 5 рефералов',
            'milestone_10' => 'Веха: 10 рефералов',
            default => $this->type,
        };
    }
}
