<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdCampaignStat extends Model
{
    protected $table = 'ad_campaign_stats';

    protected $fillable = [
        'campaign_id',
        'date',
        'impressions',
        'clicks',
        'spent',
        'ctr',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'impressions' => 'integer',
            'clicks' => 'integer',
            'spent' => 'decimal:2',
            'ctr' => 'decimal:2',
        ];
    }

    /**
     * Кампания
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }
}
