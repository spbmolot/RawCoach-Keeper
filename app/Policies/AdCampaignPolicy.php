<?php

namespace App\Policies;

use App\Models\AdCampaign;
use App\Models\User;

class AdCampaignPolicy
{
    /**
     * Просмотр списка кампаний
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['advertiser', 'admin']);
    }

    /**
     * Просмотр конкретной кампании
     */
    public function view(User $user, AdCampaign $adCampaign): bool
    {
        return $user->hasRole('admin') || $adCampaign->advertiser_id === $user->id;
    }

    /**
     * Создание кампании
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['advertiser', 'admin']);
    }

    /**
     * Обновление кампании
     */
    public function update(User $user, AdCampaign $adCampaign): bool
    {
        if (!$this->view($user, $adCampaign)) {
            return false;
        }

        return !in_array($adCampaign->status, ['active', 'completed']);
    }

    /**
     * Удаление кампании
     */
    public function delete(User $user, AdCampaign $adCampaign): bool
    {
        if (!$this->view($user, $adCampaign)) {
            return false;
        }

        return $adCampaign->status !== 'active';
    }

    /**
     * Приостановка кампании
     */
    public function pause(User $user, AdCampaign $adCampaign): bool
    {
        return $this->view($user, $adCampaign) && $adCampaign->status === 'active';
    }

    /**
     * Возобновление кампании
     */
    public function resume(User $user, AdCampaign $adCampaign): bool
    {
        return $this->view($user, $adCampaign) && $adCampaign->status === 'paused';
    }

    /**
     * Просмотр статистики
     */
    public function stats(User $user, AdCampaign $adCampaign): bool
    {
        return $this->view($user, $adCampaign);
    }

    /**
     * Управление креативами
     */
    public function manageCreatives(User $user, AdCampaign $adCampaign): bool
    {
        return $this->view($user, $adCampaign);
    }
}
