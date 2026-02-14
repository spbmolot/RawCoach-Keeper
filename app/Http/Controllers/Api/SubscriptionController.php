<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Список подписок пользователя
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $subscriptions = UserSubscription::with(['plan'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $subscriptions
        ]);
    }

    /**
     * Создание новой подписки
     */
    public function create(Request $request, Plan $plan): JsonResponse
    {
        $user = auth()->user();

        // Проверяем, нет ли уже активной подписки
        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->first();

        if ($activeSubscription) {
            return response()->json([
                'success' => false,
                'message' => 'У вас уже есть активная подписка'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'billing_cycle' => ['required', 'in:monthly,yearly'],
            'coupon_code' => ['nullable', 'string', 'exists:coupons,code'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        // Рассчитываем стоимость
        $billingCycle = $request->get('billing_cycle');
        $amount = $billingCycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price;
        
        // Применяем купон если есть
        $coupon = null;
        if ($request->has('coupon_code')) {
            $coupon = \App\Models\Coupon::where('code', $request->get('coupon_code'))
                ->where('is_active', true)
                ->where('expires_at', '>', now())
                ->first();
                
            if ($coupon && $this->canUseCoupon($coupon, $user)) {
                $amount = $this->applyCouponDiscount($amount, $coupon);
            }
        }

        // Создаем подписку
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
            'billing_cycle' => $billingCycle,
            'amount' => $amount,
            'original_amount' => $billingCycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price,
            'starts_at' => now(),
            'ends_at' => $billingCycle === 'yearly' ? now()->addYear() : now()->addMonth(),
            'coupon_id' => $coupon?->id,
        ]);

        Log::channel('subscriptions')->info('API: Subscription created', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $billingCycle,
            'amount' => $amount,
            'coupon' => $coupon?->code,
            'ip' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Подписка создана, необходимо произвести оплату',
            'data' => $subscription->load('plan')
        ], 201);
    }

    /**
     * Отмена подписки
     */
    public function cancel(Request $request, UserSubscription $subscription): JsonResponse
    {
        $user = auth()->user();

        if ($subscription->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно прав'
            ], 403);
        }

        if (!in_array($subscription->status, ['active', 'paused'])) {
            return response()->json([
                'success' => false,
                'message' => 'Подписку нельзя отменить в текущем статусе'
            ], 400);
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        Log::channel('subscriptions')->info('API: Subscription cancelled', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'ip' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Подписка отменена'
        ]);
    }

    /**
     * Приостановка подписки
     */
    public function pause(Request $request, UserSubscription $subscription): JsonResponse
    {
        $user = auth()->user();

        if ($subscription->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно прав'
            ], 403);
        }

        if ($subscription->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Можно приостановить только активную подписку'
            ], 400);
        }

        $subscription->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);

        Log::channel('subscriptions')->info('API: Subscription paused', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'ip' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Подписка приостановлена'
        ]);
    }

    /**
     * Возобновление подписки
     */
    public function resume(Request $request, UserSubscription $subscription): JsonResponse
    {
        $user = auth()->user();

        if ($subscription->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно прав'
            ], 403);
        }

        if ($subscription->status !== 'paused') {
            return response()->json([
                'success' => false,
                'message' => 'Можно возобновить только приостановленную подписку'
            ], 400);
        }

        // Продлеваем подписку на время паузы
        $pausedDuration = now()->diffInDays($subscription->paused_at);
        $newEndDate = Carbon::parse($subscription->ends_at)->addDays($pausedDuration);

        $subscription->update([
            'status' => 'active',
            'ends_at' => $newEndDate,
            'paused_at' => null,
        ]);

        Log::channel('subscriptions')->info('API: Subscription resumed', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'new_ends_at' => $newEndDate->toDateString(),
            'ip' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Подписка возобновлена'
        ]);
    }

    /**
     * Проверка возможности использования купона
     */
    private function canUseCoupon($coupon, $user): bool
    {
        // Проверяем лимит использований на пользователя
        if ($coupon->usage_limit_per_user > 0) {
            $userUsageCount = UserSubscription::where('user_id', $user->id)
                ->where('coupon_id', $coupon->id)
                ->count();
                
            if ($userUsageCount >= $coupon->usage_limit_per_user) {
                return false;
            }
        }

        // Проверяем общий лимит использований
        if ($coupon->usage_limit > 0) {
            $totalUsageCount = UserSubscription::where('coupon_id', $coupon->id)->count();
            if ($totalUsageCount >= $coupon->usage_limit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Применение скидки купона
     */
    private function applyCouponDiscount($amount, $coupon): float
    {
        if ($coupon->discount_type === 'percentage') {
            return $amount * (1 - $coupon->discount_value / 100);
        } else {
            return max(0, $amount - $coupon->discount_value);
        }
    }
}
