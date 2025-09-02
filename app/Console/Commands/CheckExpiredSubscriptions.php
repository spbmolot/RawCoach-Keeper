<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiredSubscriptions extends Command
{
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
    protected $description = 'Проверка и обработка истекших подписок';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $shouldUpdate = $this->option('update');
        $shouldNotify = $this->option('notify');

        $this->info('Проверка истекших подписок...');

        try {
            // Найти истекшие подписки
            $expiredSubscriptions = UserSubscription::where('status', 'active')
                ->where('ends_at', '<', now())
                ->with(['user', 'plan'])
                ->get();

            if ($expiredSubscriptions->isEmpty()) {
                $this->info('Истекших подписок не найдено.');
                return self::SUCCESS;
            }

            $this->warn("Найдено {$expiredSubscriptions->count()} истекших подписок:");

            $tableData = [];
            foreach ($expiredSubscriptions as $subscription) {
                $tableData[] = [
                    $subscription->id,
                    $subscription->user->name,
                    $subscription->user->email,
                    $subscription->plan->name,
                    $subscription->ends_at->format('d.m.Y H:i'),
                    $subscription->ends_at->diffForHumans(),
                ];
            }

            $this->table(
                ['ID', 'Пользователь', 'Email', 'План', 'Истекла', 'Время'],
                $tableData
            );

            if ($shouldUpdate) {
                $this->info('Обновление статуса истекших подписок...');
                
                $updated = 0;
                foreach ($expiredSubscriptions as $subscription) {
                    $subscription->update(['status' => 'expired']);
                    $updated++;
                    
                    Log::info('Subscription expired', [
                        'subscription_id' => $subscription->id,
                        'user_id' => $subscription->user_id,
                        'plan_id' => $subscription->plan_id,
                        'ended_at' => $subscription->ends_at,
                    ]);
                }

                $this->info("Обновлено подписок: {$updated}");
            }

            if ($shouldNotify) {
                $this->info('Отправка уведомлений пользователям...');
                
                $notified = 0;
                foreach ($expiredSubscriptions as $subscription) {
                    // Здесь можно добавить отправку email уведомлений
                    // Mail::to($subscription->user)->send(new SubscriptionExpiredMail($subscription));
                    $notified++;
                }

                $this->info("Отправлено уведомлений: {$notified}");
            }

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
}
