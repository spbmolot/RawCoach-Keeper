<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Events\SubscriptionStatusChanged;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Шаг 1: Welcome-экран с объяснением ценности
     */
    public function welcome()
    {
        $user = auth()->user();

        // Если онбординг уже пройден — на дашборд
        if ($user->hasCompletedOnboarding()) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.welcome', compact('user'));
    }

    /**
     * Шаг 2: Мини-анкета (цель, ограничения)
     */
    public function survey()
    {
        $user = auth()->user();

        if ($user->hasCompletedOnboarding()) {
            return redirect()->route('dashboard');
        }

        $goals = [
            'lose_weight' => 'Похудеть',
            'gain_weight' => 'Набрать вес',
            'maintain' => 'Поддержать форму',
            'eat_healthy' => 'Питаться правильно',
            'save_time' => 'Экономить время на готовке',
        ];

        $dietaryOptions = [
            'vegetarian' => 'Вегетарианство',
            'vegan' => 'Веганство',
            'gluten_free' => 'Без глютена',
            'lactose_free' => 'Без лактозы',
            'no_restrictions' => 'Нет ограничений',
        ];

        $allergyOptions = [
            'nuts' => 'Орехи',
            'dairy' => 'Молочные продукты',
            'eggs' => 'Яйца',
            'fish' => 'Рыба / Морепродукты',
            'soy' => 'Соя',
            'wheat' => 'Пшеница / Глютен',
            'none' => 'Нет аллергий',
        ];

        return view('onboarding.survey', compact('user', 'goals', 'dietaryOptions', 'allergyOptions'));
    }

    /**
     * Сохранение анкеты + автоактивация Trial
     */
    public function storeSurvey(Request $request)
    {
        $request->validate([
            'goal' => 'required|string|in:lose_weight,gain_weight,maintain,eat_healthy,save_time',
            'dietary_preferences' => 'nullable|array',
            'dietary_preferences.*' => 'string',
            'allergies' => 'nullable|array',
            'allergies.*' => 'string',
        ]);

        $user = auth()->user();

        if ($user->hasCompletedOnboarding()) {
            return redirect()->route('dashboard');
        }

        // Убираем "нет ограничений" и "нет аллергий" из массивов
        $dietary = collect($request->dietary_preferences ?? [])
            ->reject(fn($v) => $v === 'no_restrictions')
            ->values()
            ->toArray();

        $allergies = collect($request->allergies ?? [])
            ->reject(fn($v) => $v === 'none')
            ->values()
            ->toArray();

        // Сохраняем данные анкеты в профиль
        $user->update([
            'onboarding_goal' => $request->goal,
            'dietary_preferences' => !empty($dietary) ? $dietary : null,
            'allergies' => !empty($allergies) ? $allergies : null,
        ]);

        Log::channel('user-actions')->info('Onboarding survey completed', [
            'user_id' => $user->id,
            'goal' => $request->goal,
            'dietary' => $dietary,
            'allergies' => $allergies,
            'ip' => request()->ip(),
        ]);

        // Автоактивация Trial (если ещё не использован)
        $trialActivated = false;
        if (!$user->hasUsedTrial() && !$user->hasActiveSubscription()) {
            $trialActivated = $this->activateTrial($user);
        }

        // Отмечаем онбординг как завершённый
        $user->completeOnboarding();

        // Перенаправляем на финальный шаг
        return redirect()->route('onboarding.complete')
            ->with('trial_activated', $trialActivated);
    }

    /**
     * Шаг 3: Финальный экран — подтверждение + демо-меню
     */
    public function complete()
    {
        $user = auth()->user();
        $trialActivated = session('trial_activated', false);
        $subscription = $user->getCachedSubscription();

        // Получаем демо-день для показа
        $demoDay = $this->getDemoDay($user);

        return view('onboarding.complete', compact('user', 'trialActivated', 'subscription', 'demoDay'));
    }

    /**
     * Активация пробного периода
     */
    private function activateTrial($user): bool
    {
        $trialPlan = Plan::where('slug', 'trial')->where('is_active', true)->first();

        if (!$trialPlan) {
            return false;
        }

        try {
            DB::beginTransaction();

            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $trialPlan->id,
                'status' => 'active',
                'started_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addDays($trialPlan->duration_days),
                'auto_renew' => false,
            ]);

            Payment::create([
                'user_id' => $user->id,
                'plan_id' => $trialPlan->id,
                'amount' => 0,
                'original_amount' => 0,
                'currency' => 'RUB',
                'status' => 'paid',
                'provider' => 'system',
                'paid_at' => Carbon::now(),
                'description' => 'Активация пробного периода при онбординге',
            ]);

            // Назначаем роль trial подписчика
            if ($trialPlan->role && !$user->hasRole($trialPlan->role)) {
                $user->assignRole($trialPlan->role);
            }

            DB::commit();

            Log::channel('subscriptions')->info('Trial activated via onboarding', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'ends_at' => $subscription->ends_at->toDateString(),
            ]);

            event(new SubscriptionStatusChanged($subscription->fresh(), 'active'));

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('subscriptions')->error('Trial activation failed in onboarding', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            report($e);
            return false;
        }
    }

    /**
     * Получить демо-день для показа на финальном экране
     */
    private function getDemoDay($user)
    {
        $today = Carbon::today();

        // Пытаемся найти день из текущего меню
        $day = \App\Models\Day::whereHas('menu', function ($query) use ($today) {
            $query->where('month', $today->month)
                ->where('year', $today->year)
                ->where('is_published', true);
        })
            ->where('day_number', $today->day)
            ->with(['meals.recipe'])
            ->first();

        // Если нет — берём первый доступный день из любого опубликованного меню
        if (!$day) {
            $day = \App\Models\Day::whereHas('menu', function ($query) {
                $query->where('is_published', true);
            })
                ->with(['meals.recipe'])
                ->orderBy('day_number')
                ->first();
        }

        return $day;
    }
}
