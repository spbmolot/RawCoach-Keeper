<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;

class MenuPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Все авторизованные пользователи могут просматривать список меню
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Menu $menu): bool
    {
        // Проверяем доступ в зависимости от типа меню
        if ($menu->type === 'archive') {
            return $user->hasPermissionTo('content.access.archive');
        }

        if ($menu->type === 'early_access') {
            return $user->hasPermissionTo('content.access.early');
        }

        // Для текущих меню проверяем базовый доступ
        return $user->hasPermissionTo('content.access.current');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('menus.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Menu $menu): bool
    {
        return $user->hasPermissionTo('menus.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Menu $menu): bool
    {
        return $user->hasPermissionTo('menus.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Menu $menu): bool
    {
        return $user->hasPermissionTo('menus.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Menu $menu): bool
    {
        return $user->hasPermissionTo('menus.delete') && $user->hasRole('admin');
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Menu $menu): bool
    {
        return $user->hasPermissionTo('menus.publish');
    }

    /**
     * Determine whether the user can download files from the menu.
     */
    public function download(User $user, Menu $menu): bool
    {
        // Сначала проверяем, может ли пользователь просматривать меню
        if (!$this->view($user, $menu)) {
            return false;
        }

        return $user->hasPermissionTo('files.download');
    }

    /**
     * Determine whether the user can export the menu.
     */
    public function export(User $user, Menu $menu): bool
    {
        // Сначала проверяем, может ли пользователь просматривать меню
        if (!$this->view($user, $menu)) {
            return false;
        }

        return $user->hasPermissionTo('files.export.pdf') || 
               $user->hasPermissionTo('files.export.excel');
    }
}
