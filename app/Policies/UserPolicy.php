<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('users.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Пользователь может просматривать свой профиль или имеет разрешение
        return $user->id === $model->id || $user->hasPermissionTo('users.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('users.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Пользователь может редактировать свой профиль или имеет разрешение
        return $user->id === $model->id || $user->hasPermissionTo('users.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Нельзя удалить самого себя, только с разрешением и не себя
        return $user->id !== $model->id && $user->hasPermissionTo('users.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasPermissionTo('users.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('users.delete') && $user->hasRole('admin');
    }

    /**
     * Determine whether the user can impersonate another user.
     */
    public function impersonate(User $user, User $model): bool
    {
        // Нельзя имперсонировать самого себя или админа
        return $user->id !== $model->id && 
               !$model->hasRole('admin') && 
               $user->hasPermissionTo('users.impersonate');
    }
}
