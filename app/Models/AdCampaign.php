<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdCampaign extends Model
{
    protected $fillable = [
        'advertiser_id',
        'name',
        'description',
        'budget',
        'daily_budget',
        'spent_budget',
        'type',
        'rate',
        'status',
        'impressions',
        'clicks',
        'target_audience',
        'starts_at',
        'ends_at',
        'paused_at',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'daily_budget' => 'decimal:2',
            'spent_budget' => 'decimal:2',
            'rate' => 'decimal:2',
            'impressions' => 'integer',
            'clicks' => 'integer',
            'target_audience' => 'array',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'paused_at' => 'datetime',
        ];
    }

    /**
     * Рекламодатель
     */
    public function advertiser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advertiser_id');
    }

    /**
     * Размещения кампании
     */
    public function placements(): BelongsToMany
    {
        return $this->belongsToMany(AdPlacement::class, 'ad_campaign_placement')
            ->withTimestamps();
    }

    /**
     * Креативы кампании
     */
    public function creatives(): HasMany
    {
        return $this->hasMany(AdCreative::class, 'campaign_id');
    }

    /**
     * Дневная статистика
     */
    public function dailyStats(): HasMany
    {
        return $this->hasMany(AdCampaignStat::class, 'campaign_id');
    }

    /**
     * Активные кампании
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * CTR кампании
     */
    public function getCtrAttribute(): float
    {
        if ($this->impressions > 0) {
            return round(($this->clicks / $this->impressions) * 100, 2);
        }
        return 0;
    }
}
