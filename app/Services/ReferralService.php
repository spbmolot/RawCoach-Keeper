<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\ReferralReward;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReferralService
{
    // Награды
    const REFERRER_DAYS = 7;          // дней за каждого реферала
    const REFEREE_DISCOUNT = 15;       // % скидка приглашённому
    const MAX_REFERRALS_PER_MONTH = 50;

    // Вехи: кол-во рефералов → бонусные дни
    const MILESTONES = [
        3  => ['days' => 7,  'type' => 'milestone_3'],
        5  => ['days' => 14, 'type' => 'milestone_5'],
        10 => ['days' => 30, 'type' => 'milestone_10'],
    ];

    /**
     * Сгенерировать уникальный реферальный код для пользователя
     */
    public function generateCode(User $user): string
    {
        if ($user->referral_code) {
            return $user->referral_code;
        }

        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());

        $user->update(['referral_code' => $code]);

        return $code;
    }

    /**
     * Получить или создать код пользователя
     */
    public function getOrCreateCode(User $user): string
    {
        return $user->referral_code ?? $this->generateCode($user);
    }

    /**
     * Реферальная ссылка пользователя
     */
    public function getReferralUrl(User $user): string
    {
        $code = $this->getOrCreateCode($user);
        return route('referral.landing', $code);
    }

    /**
     * Зарегистрировать реферал (при регистрации приглашённого)
     */
    public function registerReferral(User $referrer, User $referred, ?string $ip = null): ?Referral
    {
        // Антифрод: нельзя пригласить самого себя
        if ($referrer->id === $referred->id) {
            Log::warning('Referral: self-referral attempt', [
                'user_id' => $referrer->id,
            ]);
            return null;
        }

        // Антифрод: уже приглашён кем-то
        if ($referred->referred_by !== null) {
            return null;
        }

        // Антифрод: лимит рефералов в месяц
        $monthCount = Referral::where('referrer_id', $referrer->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        if ($monthCount >= self::MAX_REFERRALS_PER_MONTH) {
            Log::warning('Referral: monthly limit reached', [
                'referrer_id' => $referrer->id,
                'count' => $monthCount,
            ]);
            return null;
        }

        // Антифрод: проверка IP — не более 3 рефералов с одного IP в день
        if ($ip) {
            $ipCount = Referral::where('referred_ip', $ip)
                ->where('created_at', '>=', now()->startOfDay())
                ->count();

            if ($ipCount >= 3) {
                Log::warning('Referral: IP limit reached', [
                    'ip' => $ip,
                    'count' => $ipCount,
                ]);
                return null;
            }
        }

        try {
            $referral = Referral::create([
                'referrer_id' => $referrer->id,
                'referred_id' => $referred->id,
                'status' => 'registered',
                'referrer_ip' => null,
                'referred_ip' => $ip,
                'registered_at' => now(),
            ]);

            $referred->update(['referred_by' => $referrer->id]);

            Log::info('Referral registered', [
                'referrer_id' => $referrer->id,
                'referred_id' => $referred->id,
                'referral_id' => $referral->id,
            ]);

            return $referral;

        } catch (\Exception $e) {
            Log::error('Referral registration failed', [
                'referrer_id' => $referrer->id,
                'referred_id' => $referred->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Начислить награды при первой оплате реферала
     * Вызывается из Listener при SubscriptionStatusChanged → active
     */
    public function processReward(UserSubscription $subscription): void
    {
        $referred = $subscription->user;

        if (!$referred->referred_by) {
            return;
        }

        $referral = Referral::where('referrer_id', $referred->referred_by)
            ->where('referred_id', $referred->id)
            ->first();

        if (!$referral) {
            return;
        }

        // Награда уже начислена
        if ($referral->status === 'rewarded') {
            return;
        }

        $referrer = User::find($referred->referred_by);
        if (!$referrer) {
            return;
        }

        try {
            DB::beginTransaction();

            // 1. Обновляем статус реферала
            $referral->update([
                'status' => 'rewarded',
                'subscribed_at' => $subscription->started_at ?? now(),
                'rewarded_at' => now(),
            ]);

            // 2. Награда пригласившему: +7 дней к подписке
            $this->addDaysToSubscription($referrer, self::REFERRER_DAYS, $referral, 'referrer_days');

            // 3. Проверяем вехи
            $this->checkMilestones($referrer);

            DB::commit();

            Log::info('Referral reward processed', [
                'referrer_id' => $referrer->id,
                'referred_id' => $referred->id,
                'referral_id' => $referral->id,
                'days_added' => self::REFERRER_DAYS,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Referral reward processing failed', [
                'referral_id' => $referral->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Добавить дни к подписке пользователя
     */
    private function addDaysToSubscription(User $user, int $days, Referral $referral, string $type): void
    {
        $subscription = $user->activeSubscription()->first();

        if ($subscription) {
            $subscription->update([
                'ends_at' => $subscription->ends_at->addDays($days),
            ]);
        }

        // Логируем награду даже если нет активной подписки (будет видно в истории)
        ReferralReward::create([
            'user_id' => $user->id,
            'referral_id' => $referral->id,
            'type' => $type,
            'days_added' => $days,
            'description' => $this->getRewardDescription($type, $days, $referral),
        ]);
    }

    /**
     * Проверить и начислить бонусы за вехи
     */
    private function checkMilestones(User $referrer): void
    {
        $totalRewarded = Referral::where('referrer_id', $referrer->id)
            ->where('status', 'rewarded')
            ->count();

        foreach (self::MILESTONES as $threshold => $bonus) {
            if ($totalRewarded < $threshold) {
                continue;
            }

            // Проверяем, не была ли уже начислена эта веха
            $alreadyAwarded = ReferralReward::where('user_id', $referrer->id)
                ->where('type', $bonus['type'])
                ->exists();

            if ($alreadyAwarded) {
                continue;
            }

            // Начисляем бонус за веху
            $subscription = $referrer->activeSubscription()->first();
            if ($subscription) {
                $subscription->update([
                    'ends_at' => $subscription->ends_at->addDays($bonus['days']),
                ]);
            }

            ReferralReward::create([
                'user_id' => $referrer->id,
                'referral_id' => null,
                'type' => $bonus['type'],
                'days_added' => $bonus['days'],
                'description' => "Бонус за {$threshold} успешных приглашений: +{$bonus['days']} дней",
            ]);

            Log::info('Referral milestone reached', [
                'referrer_id' => $referrer->id,
                'milestone' => $threshold,
                'days_added' => $bonus['days'],
            ]);
        }
    }

    /**
     * Скидка для приглашённого (% от цены подписки)
     */
    public function getRefereeDiscount(User $user): int
    {
        if (!$user->referred_by) {
            return 0;
        }

        // Скидка только на первую подписку
        $hasSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->exists();

        if ($hasSubscription) {
            return 0;
        }

        return self::REFEREE_DISCOUNT;
    }

    /**
     * Статистика рефералов пользователя
     */
    public function getStats(User $user): array
    {
        $referrals = Referral::where('referrer_id', $user->id)->get();

        $totalDaysEarned = ReferralReward::where('user_id', $user->id)
            ->sum('days_added');

        return [
            'total_invited' => $referrals->count(),
            'registered' => $referrals->where('status', 'registered')->count(),
            'rewarded' => $referrals->where('status', 'rewarded')->count(),
            'total_days_earned' => (int) $totalDaysEarned,
            'referral_url' => $this->getReferralUrl($user),
            'referral_code' => $this->getOrCreateCode($user),
        ];
    }

    /**
     * Описание награды
     */
    private function getRewardDescription(string $type, int $days, Referral $referral): string
    {
        $referredName = $referral->referred->name ?? 'Пользователь';

        return match ($type) {
            'referrer_days' => "+{$days} дней за приглашение: {$referredName}",
            default => "+{$days} дней (бонус)",
        };
    }
}
