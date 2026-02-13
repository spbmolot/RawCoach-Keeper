<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\UserSubscription;
use App\Services\PaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetryFailedRenewals extends Command
{
    /**
     * Максимальное количество попыток
     */
    private const MAX_RETRY_ATTEMPTS = 3;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'subscriptions:retry-failed 
                            {--dry-run : Показать неудачные платежи без повторной попытки}';

    /**
     * The console command description.
     */
    protected $description = 'Повторная попытка списания для неудачных автопродлений в grace period';

    /**
     * Execute the console command.
     */
    public function handle(PaymentService $paymentService): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('Поиск подписок в grace period для повторной попытки списания...');

        try {
            // Находим подписки в grace period
            $gracePeriodSubscriptions = UserSubscription::where('status', 'grace_period')
                ->where('grace_period_ends_at', '>', now())
                ->with(['user', 'plan'])
                ->get();

            if ($gracePeriodSubscriptions->isEmpty()) {
                $this->info('Подписок в grace period не найдено.');
                return self::SUCCESS;
            }

            $this->warn("Найдено {$gracePeriodSubscriptions->count()} подписок в grace period:");

            $retried = 0;
            $skipped = 0;
            $maxAttemptsReached = 0;

            foreach ($gracePeriodSubscriptions as $subscription) {
                // Проверяем количество неудачных попыток
                $failedAttempts = Payment::where('subscription_id', $subscription->id)
                    ->where('status', 'failed')
                    ->where('created_at', '>=', $subscription->ends_at)
                    ->count();

                $this->line("Подписка #{$subscription->id} ({$subscription->user->email}): {$failedAttempts} неудачных попыток");

                if ($failedAttempts >= self::MAX_RETRY_ATTEMPTS) {
                    $this->warn("  → Достигнут лимит попыток ({$failedAttempts}/" . self::MAX_RETRY_ATTEMPTS . ")");
                    $maxAttemptsReached++;
                    continue;
                }

                // Проверяем, нет ли уже pending платежа
                $pendingPayment = Payment::where('subscription_id', $subscription->id)
                    ->where('status', 'pending')
                    ->where('created_at', '>=', $subscription->ends_at)
                    ->first();

                if ($pendingPayment) {
                    $this->comment("  → Есть ожидающий платеж #{$pendingPayment->id}");
                    $skipped++;
                    continue;
                }

                if ($dryRun) {
                    $this->info("  → [DRY-RUN] Будет создан повторный платеж");
                    $skipped++;
                    continue;
                }

                // Создаём новый платёж для повторной попытки
                try {
                    $payment = $paymentService->createSubscriptionPayment($subscription, [
                        'description' => "Повторная попытка продления (попытка " . ($failedAttempts + 1) . ")",
                        'is_retry' => true,
                    ]);

                    $this->info("  → Создан платёж #{$payment->id}");
                    $retried++;

                    Log::info('Retry payment created for grace period subscription', [
                        'subscription_id' => $subscription->id,
                        'payment_id' => $payment->id,
                        'attempt' => $failedAttempts + 1,
                    ]);

                } catch (\Exception $e) {
                    $this->error("  → Ошибка: " . $e->getMessage());
                    Log::error('Failed to create retry payment', [
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->newLine();
            $this->info("Результаты:");
            $this->table(
                ['Метрика', 'Значение'],
                [
                    ['Всего в grace period', $gracePeriodSubscriptions->count()],
                    ['Создано повторных платежей', $retried],
                    ['Пропущено', $skipped],
                    ['Достигнут лимит попыток', $maxAttemptsReached],
                ]
            );

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Ошибка: " . $e->getMessage());
            Log::error('Retry failed renewals command failed', [
                'exception' => $e,
            ]);
            return self::FAILURE;
        }
    }
}
