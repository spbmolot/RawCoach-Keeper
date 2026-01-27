<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Events\SubscriptionStatusChanged;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Создание новой подписки
     */
    public function create(Request $request, Plan $plan)
    {
        $request->validate([
            'coupon_code' => 'nullable|string|exists:coupons,code',
        ]);

        if (!$plan->is_active) {
            return back()->with('error', 'Выбранный план недоступен');
        }

        $user = auth()->user();
        
        // Проверяем, нет ли уже активной подписки
        $activeSubscription = $user->activeSubscription()->first();
        if ($activeSubscription) {
            return redirect()->route('dashboard')
                ->with('error', 'У вас уже есть активная подписка');
        }

        $price = $plan->price;
        $coupon = null;

        // Обработка купона
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            
            if ($coupon && $this->canUseCoupon($user, $coupon, $plan)) {
                $price = $this->calculateDiscountedPrice($price, $coupon);
            } else {
                return back()->with('error', 'Купон недействителен или не может быть применен');
            }
        }

        try {
            DB::beginTransaction();

            // Создаем подписку
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'pending',
                'started_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addDays($plan->duration_days),
                'auto_renew' => true,
                'trial_ends_at' => $plan->type === 'trial' ? Carbon::now()->addDays(7) : null,
            ]);

            // Создаем платеж
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
                'coupon_id' => $coupon?->id,
                'amount' => $price,
                'original_amount' => $plan->price,
                'currency' => $plan->currency,
                'status' => 'pending',
                'provider' => 'yookassa', // по умолчанию
                'description' => "Подписка на план: {$plan->name}",
            ]);

            // Если это бесплатный план (trial)
            if ($price == 0) {
                $payment->update(['status' => 'paid']);
                $subscription->update(['status' => 'active']);
                
                DB::commit();
                event(new SubscriptionStatusChanged($subscription->fresh(), 'active'));
                
                return redirect()->route('dashboard')
                    ->with('success', 'Пробная подписка успешно активирована!');
            }

            DB::commit();

            // Перенаправляем на страницу оплаты
            return redirect()->route('payment.process', $payment);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Произошла ошибка при создании подписки');
        }
    }

    /**
     * Отмена подписки
     */
    public function cancel(UserSubscription $subscription)
    {
        $this->authorize('cancel', $subscription);

        if ($subscription->status === 'cancelled') {
            return back()->with('error', 'Подписка уже отменена');
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now(),
            'auto_renew' => false,
        ]);
        event(new SubscriptionStatusChanged($subscription->fresh(), 'cancelled'));

        return back()->with('success', 'Подписка успешно отменена');
    }

    /**
     * Приостановка подписки
     */
    public function pause(UserSubscription $subscription)
    {
        $this->authorize('pause', $subscription);

        if (!$subscription->isActive()) {
            return back()->with('error', 'Нельзя приостановить неактивную подписку');
        }

        $subscription->update([
            'status' => 'paused',
            'paused_at' => Carbon::now(),
        ]);
        event(new SubscriptionStatusChanged($subscription->fresh(), 'paused'));

        return back()->with('success', 'Подписка приостановлена');
    }

    /**
     * Возобновление подписки
     */
    public function resume(UserSubscription $subscription)
    {
        $this->authorize('resume', $subscription);

        if ($subscription->status !== 'paused') {
            return back()->with('error', 'Можно возобновить только приостановленную подписку');
        }

        $subscription->update([
            'status' => 'active',
            'paused_at' => null,
        ]);
        event(new SubscriptionStatusChanged($subscription->fresh(), 'active'));

        return back()->with('success', 'Подписка возобновлена');
    }

    /**
     * Переключение автопродления подписки
     */
    public function toggleRenewal(UserSubscription $subscription)
    {
        $this->authorize('manage', $subscription);

        $subscription->update([
            'auto_renew' => !$subscription->auto_renew,
        ]);

        $message = $subscription->auto_renew 
            ? 'Автопродление включено' 
            : 'Автопродление отключено';

        return back()->with('success', $message);
    }

    /**
     * Обновление подписки до другого плана
     */
    public function upgrade(Request $request, Plan $newPlan)
    {
        $user = auth()->user();
        $currentSubscription = $user->activeSubscription()->first();

        if (!$currentSubscription) {
            return redirect()->route('plans.index')
                ->with('error', 'У вас нет активной подписки для обновления');
        }

        if ($currentSubscription->plan_id === $newPlan->id) {
            return back()->with('error', 'Вы уже подписаны на этот план');
        }

        // Логика пропорционального возврата и доплаты
        $prorationAmount = $this->calculateProration($currentSubscription, $newPlan);

        try {
            DB::beginTransaction();

            // Отменяем текущую подписку
            $currentSubscription->update([
                'status' => 'upgraded',
                'cancelled_at' => Carbon::now(),
            ]);
            event(new SubscriptionStatusChanged($currentSubscription->fresh(), 'upgraded'));

            // Создаем новую подписку
            $newSubscription = UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $newPlan->id,
                'status' => 'active',
                'started_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addDays($newPlan->duration_days),
                'auto_renew' => true,
                'upgraded_from_id' => $currentSubscription->id,
            ]);
            event(new SubscriptionStatusChanged($newSubscription->fresh(), 'active'));

            // Если нужна доплата
            if ($prorationAmount > 0) {
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'subscription_id' => $newSubscription->id,
                    'plan_id' => $newPlan->id,
                    'amount' => $prorationAmount,
                    'currency' => $newPlan->currency,
                    'status' => 'pending',
                    'provider' => 'yookassa',
                    'description' => "Доплата при обновлении до плана: {$newPlan->name}",
                ]);

                DB::commit();
                return redirect()->route('payment.process', $payment);
            }

            DB::commit();
            return redirect()->route('dashboard')
                ->with('success', 'Подписка успешно обновлена!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Произошла ошибка при обновлении подписки');
        }
    }

    /**
     * Проверка возможности использования купона
     */
    private function canUseCoupon($user, $coupon, $plan): bool
    {
        if (!$coupon->is_active) return false;
        if ($coupon->expires_at && $coupon->expires_at->isPast()) return false;
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) return false;
        
        if ($coupon->usage_limit_per_user) {
            $userUsageCount = $coupon->couponUsages()->where('user_id', $user->id)->count();
            if ($userUsageCount >= $coupon->usage_limit_per_user) return false;
        }

        if ($coupon->applicable_plans && !in_array($plan->slug, $coupon->applicable_plans)) {
            return false;
        }

        return true;
    }

    /**
     * Расчет цены со скидкой
     */
    private function calculateDiscountedPrice($originalPrice, $coupon): float
    {
        if ($coupon->type === 'percentage') {
            return $originalPrice * (1 - $coupon->value / 100);
        } else {
            return max(0, $originalPrice - $coupon->value);
        }
    }

    /**
     * Расчет пропорциональной доплаты при обновлении
     */
    private function calculateProration($currentSubscription, $newPlan): float
    {
        // Упрощенная логика - в реальности может быть сложнее
        $remainingDays = $currentSubscription->ends_at->diffInDays(Carbon::now());
        $currentDailyRate = $currentSubscription->plan->price / $currentSubscription->plan->duration_days;
        $newDailyRate = $newPlan->price / $newPlan->duration_days;
        
        $refund = $remainingDays * $currentDailyRate;
        $newCharge = $remainingDays * $newDailyRate;
        
        return max(0, $newCharge - $refund);
    }
}
