<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use App\Mail\SubscriptionExpiring;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyExpiringSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'subscriptions:notify-expiring 
                            {--days=3 : За сколько дней до истечения уведомлять}
                            {--dry-run : Показать подписки без отправки уведомлений}';

    /**
     * The console command description.
     */
    protected $description = 'Отправка уведомлений о скором истечении подписки';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info("Поиск подписок, истекающих в течение {$days} дней...");

        try {
            // Находим подписки, которые истекают в ближайшие N дней
            // и у которых отключено автопродление (иначе они продлятся автоматически)
            $expiringSubscriptions = UserSubscription::where('status', 'active')
                ->where('auto_renew', false)
                ->where('ends_at', '>', now())
                ->where('ends_at', '<=', now()->addDays($days))
                ->whereNull('cancelled_at') // Ещё не отменена
                ->with(['user', 'plan'])
                ->get();

            // Также уведомляем тех, у кого автопродление включено, но это первое уведомление
            $autoRenewExpiring = UserSubscription::where('status', 'active')
                ->where('auto_renew', true)
                ->where('ends_at', '>', now())
                ->where('ends_at', '<=', now()->addDays($days))
                ->whereNotNull('cancelled_at') // Отменена (auto_renew выключится)
                ->with(['user', 'plan'])
                ->get();

            $allExpiring = $expiringSubscriptions->merge($autoRenewExpiring);

            if ($allExpiring->isEmpty()) {
                $this->info('Подписок для уведомления не найдено.');
                return self::SUCCESS;
            }

            $this->warn("Найдено {$allExpiring->count()} подписок для уведомления:");

            $tableData = [];
            foreach ($allExpiring as $subscription) {
                $tableData[] = [
                    $subscription->id,
                    $subscription->user->email,
                    $subscription->plan->name,
                    $subscription->ends_at->format('d.m.Y'),
                    $subscription->ends_at->diffForHumans(),
                    $subscription->auto_renew ? 'Да' : 'Нет',
                ];
            }

            $this->table(
                ['ID', 'Email', 'План', 'Истекает', 'Через', 'Автопродление'],
                $tableData
            );

            if ($dryRun) {
                $this->comment('Режим тестирования - уведомления не отправлены');
                return self::SUCCESS;
            }

            $sent = 0;
            $errors = 0;

            foreach ($allExpiring as $subscription) {
                try {
                    Mail::to($subscription->user)->send(new SubscriptionExpiring($subscription));
                    $sent++;

                    Log::info('Expiring subscription notification sent', [
                        'subscription_id' => $subscription->id,
                        'user_id' => $subscription->user_id,
                        'ends_at' => $subscription->ends_at,
                    ]);
                } catch (\Exception $e) {
                    $errors++;
                    Log::error('Failed to send expiring notification', [
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->info("Отправлено уведомлений: {$sent}");
            if ($errors > 0) {
                $this->warn("Ошибок: {$errors}");
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Ошибка: " . $e->getMessage());
            Log::error('Notify expiring subscriptions command failed', [
                'exception' => $e,
            ]);
            return self::FAILURE;
        }
    }
}
