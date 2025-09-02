<?php

namespace App\Policies;

use App\Models\Recipe;
use App\Models\User;

class RecipePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Все авторизованные пользователи могут просматривать список рецептов
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Recipe $recipe): bool
    {
        // Проверяем статус публикации
        if (!$recipe->is_published && !$user->hasPermissionTo('recipes.view')) {
            return false;
        }

        // Проверяем доступ в зависимости от типа рецепта
        if ($recipe->type === 'archive') {
            return $user->hasPermissionTo('content.access.archive');
        }

        if ($recipe->type === 'early_access') {
            return $user->hasPermissionTo('content.access.early');
        }

        if ($recipe->type === 'personal') {
            return $user->hasPermissionTo('content.access.personal');
        }

        // Для обычных рецептов проверяем базовый доступ
        return $user->hasPermissionTo('content.access.current');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('recipes.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Recipe $recipe): bool
    {
        return $user->hasPermissionTo('recipes.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Recipe $recipe): bool
    {
        return $user->hasPermissionTo('recipes.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Recipe $recipe): bool
    {
        return $user->hasPermissionTo('recipes.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Recipe $recipe): bool
    {
        return $user->hasPermissionTo('recipes.delete') && $user->hasRole('admin');
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Recipe $recipe): bool
    {
        return $user->hasPermissionTo('recipes.publish');
    }

    /**
     * Determine whether the user can add recipe to favorites.
     */
    public function favorite(User $user, Recipe $recipe): bool
    {
        // Сначала проверяем, может ли пользователь просматривать рецепт
        return $this->view($user, $recipe);
    }
}
