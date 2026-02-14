<?php

namespace App\Listeners;

use App\Events\SubscriptionStatusChanged;
use App\Services\ReferralService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessReferralReward implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private ReferralService $referralService
    ) {}

    public function handle(SubscriptionStatusChanged $event): void
    {
        // Начисляем только при активации
        if ($event->status !== 'active') {
            return;
        }

        $subscription = $event->subscription;
        $user = $subscription->user;

        // Только если пользователь был приглашён
        if (!$user->referred_by) {
            return;
        }

        // Только первая подписка (не продления)
        $previousActive = $user->subscriptions()
            ->where('status', 'active')
            ->where('id', '!=', $subscription->id)
            ->exists();

        if ($previousActive) {
            return;
        }

        try {
            $this->referralService->processReward($subscription);
        } catch (\Exception $e) {
            Log::error('ProcessReferralReward failed', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
