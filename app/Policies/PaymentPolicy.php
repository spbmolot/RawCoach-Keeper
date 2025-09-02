<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('payments.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Payment $payment): bool
    {
        // Пользователь может просматривать свои платежи или имеет разрешение
        return $user->id === $payment->user_id || $user->hasPermissionTo('payments.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('payments.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('payments.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo('payments.delete');
    }

    /**
     * Determine whether the user can refund the payment.
     */
    public function refund(User $user, Payment $payment): bool
    {
        // Можно возвращать только успешные платежи
        if ($payment->status !== 'completed') {
            return false;
        }

        return $user->hasPermissionTo('payments.refund');
    }

    /**
     * Determine whether the user can view payment details.
     */
    public function viewDetails(User $user, Payment $payment): bool
    {
        return $this->view($user, $payment);
    }
}
