<?php

namespace App\Policies;

use App\Models\PersonalPlan;
use App\Models\User;

class PersonalPlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('personal-plans.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PersonalPlan $personalPlan): bool
    {
        // Пользователь может просматривать свои персональные планы или имеет разрешение
        return $user->id === $personalPlan->user_id || $user->hasPermissionTo('personal-plans.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('personal-plans.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PersonalPlan $personalPlan): bool
    {
        return $user->hasPermissionTo('personal-plans.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PersonalPlan $personalPlan): bool
    {
        return $user->hasPermissionTo('personal-plans.delete');
    }

    /**
     * Determine whether the user can approve the personal plan.
     */
    public function approve(User $user, PersonalPlan $personalPlan): bool
    {
        return $user->hasPermissionTo('personal-plans.approve');
    }

    /**
     * Determine whether the user can access their personal plan content.
     */
    public function access(User $user, PersonalPlan $personalPlan): bool
    {
        // Пользователь может получить доступ к своему плану, если он одобрен
        if ($user->id === $personalPlan->user_id) {
            return $personalPlan->status === 'approved' && $user->hasPermissionTo('content.access.personal');
        }

        // Или имеет административные права
        return $user->hasPermissionTo('personal-plans.view');
    }
}
