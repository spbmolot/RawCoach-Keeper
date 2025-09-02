<?php

namespace App\Policies;

use App\Models\UserSubscription;
use App\Models\User;

class UserSubscriptionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('subscriptions.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserSubscription $subscription): bool
    {
        // Пользователь может просматривать свои подписки или имеет разрешение
        return $user->id === $subscription->user_id || $user->hasPermissionTo('subscriptions.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('subscriptions.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserSubscription $subscription): bool
    {
        return $user->hasPermissionTo('subscriptions.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserSubscription $subscription): bool
    {
        return $user->hasPermissionTo('subscriptions.delete');
    }

    /**
     * Determine whether the user can manage subscription (cancel, pause, etc.).
     */
    public function manage(User $user, UserSubscription $subscription): bool
    {
        // Пользователь может управлять своими подписками или имеет разрешение
        return $user->id === $subscription->user_id || $user->hasPermissionTo('subscriptions.manage');
    }

    /**
     * Determine whether the user can cancel the subscription.
     */
    public function cancel(User $user, UserSubscription $subscription): bool
    {
        return $this->manage($user, $subscription);
    }

    /**
     * Determine whether the user can pause the subscription.
     */
    public function pause(User $user, UserSubscription $subscription): bool
    {
        return $this->manage($user, $subscription);
    }

    /**
     * Determine whether the user can resume the subscription.
     */
    public function resume(User $user, UserSubscription $subscription): bool
    {
        return $this->manage($user, $subscription);
    }
}
