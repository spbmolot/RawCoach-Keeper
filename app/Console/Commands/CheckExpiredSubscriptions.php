<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use App\Events\SubscriptionStatusChanged;
use App\Mail\SubscriptionGracePeriod;
use App\Mail\SubscriptionExpired;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckExpiredSubscriptions extends Command
{
    /**
     * Длительность grace period в днях
     */
    private const GRACE_PERIOD_DAYS = 3;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired 
                            {--update : Обновить статус истекших подписок}
                            {--notify : Отправить уведомления пользователям}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка и обработка истекших подписок (с учётом grace period)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $shouldUpdate = $this->option('update');
        $shouldNotify = $this->option('notify');

        $this->info('Проверка истекших подписок...');

        try {
            // 1. Обработка активных подписок с истёкшим ends_at
            $this->processExpiredActiveSubscriptions($shouldUpdate, $shouldNotify);

            // 2. Обработка подписок в grace period с истёкшим grace_period_ends_at
            $this->processExpiredGracePeriodSubscriptions($shouldUpdate, $shouldNotify);

            if (!$shouldUpdate && !$shouldNotify) {
                $this->comment('Для обновления статуса используйте --update');
                $this->comment('Для отправки уведомлений используйте --notify');
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Ошибка при проверке истекших подписок: " . $e->getMessage());
            Log::error('Check expired subscriptions command failed', [
                'exception' => $e,
                'options' => $this->options(),
            ]);
            return self::FAILURE;
        }
    }

    /**
     * Обработка активных подписок с истёкшим ends_at
     */
    private function processExpiredActiveSubscriptions(bool $shouldUpdate, bool $shouldNotify): void
    {
        $expiredSubscriptions = UserSubscription::where('status', 'active')
            ->where('ends_at', '<', now())
            ->with(['user', 'plan'])
            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('Истекших активных подписок не найдено.');
            return;
        }

        $this->warn("Найдено {$expiredSubscriptions->count()} истекших активных подписок:");
        $this->displaySubscriptionsTable($expiredSubscriptions);

        if ($shouldUpdate) {
            $this->info('Обновление статуса...');
            
            $toGracePeriod = 0;
            $toExpired = 0;

            foreach ($expiredSubscriptions as $subscription) {
                // Если автопродление включено и это не trial - переводим в grace period
                if ($subscription->auto_renew && $subscription->plan->type !== 'trial') {
                    $subscription->update([
                        'status' => 'grace_period',
                        'grace_period_ends_at' => Carbon::now()->addDays(self::GRACE_PERIOD_DAYS),
                    ]);
                    event(new SubscriptionStatusChanged($subscription->fresh(), 'grace_period'));
                    $toGracePeriod++;
                    
                    Log::info('Subscription moved to grace period', [
                        'subscription_id' => $subscription->id,
                        'user_id' => $subscription->user_id,
                        'grace_period_ends_at' => $subscription->fresh()->grace_period_ends_at,
                    ]);
                } else {
                    // Автопродление выключено или trial - сразу expired
                    $subscription->update(['status' => 'expired']);
                    event(new SubscriptionStatusChanged($subscription->fresh(), 'expired'));
                    $toExpired++;
                    
                    Log::info('Subscription expired (no auto-renew or trial)', [
                        'subscription_id' => $subscription->id,
                        'user_id' => $subscription->user_id,
                    ]);
                }
            }

            $this->info("Переведено в grace period: {$toGracePeriod}");
            $this->info("Переведено в expired: {$toExpired}");
        }

        if ($shouldNotify) {
            $this->sendNotifications($expiredSubscriptions, 'expired_active');
        }
    }

    /**
     * Обработка подписок в grace period с истёкшим grace_period_ends_at
     */
    private function processExpiredGracePeriodSubscriptions(bool $shouldUpdate, bool $shouldNotify): void
    {
        $gracePeriodExpired = UserSubscription::where('status', 'grace_period')
            ->where('grace_period_ends_at', '<', now())
            ->with(['user', 'plan'])
            ->get();

        if ($gracePeriodExpired->isEmpty()) {
            $this->info('Подписок с истёкшим grace period не найдено.');
            return;
        }

        $this->warn("Найдено {$gracePeriodExpired->count()} подписок с истёкшим grace period:");
        $this->displaySubscriptionsTable($gracePeriodExpired);

        if ($shouldUpdate) {
            $this->info('Обновление статуса...');
            
            $expired = 0;
            foreach ($gracePeriodExpired as $subscription) {
                $subscription->update(['status' => 'expired']);
                event(new SubscriptionStatusChanged($subscription->fresh(), 'expired'));
                $expired++;
                
                Log::info('Subscription expired after grace period', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                ]);
            }

            $this->info("Переведено в expired: {$expired}");
        }

        if ($shouldNotify) {
            $this->sendNotifications($gracePeriodExpired, 'grace_period_expired');
        }
    }

    /**
     * Отображение таблицы подписок
     */
    private function displaySubscriptionsTable($subscriptions): void
    {
        $tableData = [];
        foreach ($subscriptions as $subscription) {
            $tableData[] = [
                $subscription->id,
                $subscription->user->name,
                $subscription->user->email,
                $subscription->plan->name,
                $subscription->status,
                $subscription->ends_at->format('d.m.Y H:i'),
                $subscription->auto_renew ? 'Да' : 'Нет',
            ];
        }

        $this->table(
            ['ID', 'Пользователь', 'Email', 'План', 'Статус', 'Истекла', 'Автопродление'],
            $tableData
        );
    }

    /**
     * Отправка уведомлений
     */
    private function sendNotifications($subscriptions, string $type): void
    {
        $this->info("Отправка уведомлений ({$type})...");
        
        $notified = 0;
        $errors = 0;

        foreach ($subscriptions as $subscription) {
            try {
                if ($type === 'expired_active' && $subscription->status === 'grace_period') {
                    // Уведомление о переходе в grace period
                    Mail::to($subscription->user)->send(new SubscriptionGracePeriod($subscription));
                    $notified++;
                } elseif ($type === 'grace_period_expired') {
                    // Уведомление об истечении подписки
                    Mail::to($subscription->user)->send(new SubscriptionExpired($subscription));
                    $notified++;
                }

                Log::info('Subscription notification sent', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'type' => $type,
                ]);
            } catch (\Exception $e) {
                $errors++;
                Log::error('Failed to send subscription notification', [
                    'subscription_id' => $subscription->id,
                    'type' => $type,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Отправлено уведомлений: {$notified}");
        if ($errors > 0) {
            $this->warn("Ошибок отправки: {$errors}");
        }
    }
}
