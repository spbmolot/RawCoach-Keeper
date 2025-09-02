<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;

class CouponPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('coupons.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('coupons.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('coupons.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('coupons.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('coupons.delete');
    }

    /**
     * Determine whether the user can use the coupon.
     */
    public function use(User $user, Coupon $coupon): bool
    {
        // Проверяем, активен ли купон
        if (!$coupon->is_active) {
            return false;
        }

        // Проверяем срок действия
        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return false;
        }

        // Проверяем лимит использований
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return false;
        }

        // Проверяем лимит на пользователя
        if ($coupon->usage_limit_per_user) {
            $userUsageCount = $coupon->couponUsages()->where('user_id', $user->id)->count();
            if ($userUsageCount >= $coupon->usage_limit_per_user) {
                return false;
            }
        }

        return true;
    }
}
