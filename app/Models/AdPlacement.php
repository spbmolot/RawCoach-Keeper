<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdPlacement extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'size',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Кампании, использующие это размещение
     */
    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(AdCampaign::class, 'ad_campaign_placement')
            ->withTimestamps();
    }
}
