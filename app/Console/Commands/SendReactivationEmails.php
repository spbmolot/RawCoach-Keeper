<?php

namespace App\Console\Commands;

use App\Mail\ReactivationEmail;
use App\Models\EmailSequence;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReactivationEmails extends Command
{
    protected $signature = 'emails:send-reactivation
        {--days=7 : Дней после истечения подписки для начала серии}
        {--limit=50 : Максимум писем за запуск}';

    protected $description = 'Отправляет реактивационные письма пользователям с истёкшей подпиской';

    public function handle(): int
    {
        $daysThreshold = (int) $this->option('days');
        $limit = (int) $this->option('limit');

        // 1. Найти пользователей с недавно истёкшими подписками, у которых ещё нет реактивационной серии
        $this->startNewSequences($daysThreshold);

        // 2. Обработать активные серии
        $sequences = EmailSequence::where('sequence_type', 'reactivation')
            ->whereNull('completed_at')
            ->whereNull('unsubscribed_at')
            ->where(function ($q) {
                $q->whereNull('next_send_at')
                  ->orWhere('next_send_at', '<=', now());
            })
            ->where('current_step', '<', 3)
            ->with('user')
            ->limit($limit)
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($sequences as $sequence) {
            $user = $sequence->user;

            if (!$user || !$user->is_active || !$user->email_notifications) {
                $skipped++;
                continue;
            }

            // Если пользователь снова оформил подписку — завершаем
            if ($user->hasActiveSubscription()) {
                $sequence->update(['completed_at' => now()]);
                $this->line("  Пользователь #{$user->id} вернулся — серия завершена");
                $skipped++;
                continue;
            }

            $nextStep = $sequence->current_step + 1;
            $lastSub = $user->subscriptions()->latest('ends_at')->first();

            try {
                Mail::to($user->email)->send(new ReactivationEmail($user, $nextStep, $lastSub));

                $delayHours = ReactivationEmail::getDelayHours($nextStep);
                $sequence->advanceStep($delayHours);

                $sent++;
                $this->line("  ✓ Реактивация #{$nextStep} отправлена: {$user->email}");

                Log::info('Reactivation email sent', [
                    'user_id' => $user->id,
                    'step' => $nextStep,
                    'email' => $user->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Reactivation email failed', [
                    'user_id' => $user->id,
                    'step' => $nextStep,
                    'error' => $e->getMessage(),
                ]);
                $this->error("  ✗ Ошибка для {$user->email}: {$e->getMessage()}");
            }
        }

        $this->info("Реактивация: отправлено {$sent}, пропущено {$skipped}");

        return Command::SUCCESS;
    }

    /**
     * Найти пользователей с истёкшими подписками и создать для них серию
     */
    private function startNewSequences(int $daysThreshold): void
    {
        $expiredUsers = User::where('is_active', true)
            ->where('email_notifications', true)
            ->whereDoesntHave('activeSubscription')
            ->whereHas('subscriptions', function ($q) use ($daysThreshold) {
                $q->where('status', 'expired')
                  ->where('ends_at', '>=', now()->subDays($daysThreshold + 3))
                  ->where('ends_at', '<=', now()->subDays($daysThreshold));
            })
            ->whereDoesntHave('subscriptions', function ($q) {
                // Исключить тех, у кого есть активная подписка
                $q->where('status', 'active');
            })
            ->get();

        foreach ($expiredUsers as $user) {
            // Не создавать повторную серию если уже есть незавершённая
            $existing = EmailSequence::where('user_id', $user->id)
                ->where('sequence_type', 'reactivation')
                ->whereNull('completed_at')
                ->first();

            if (!$existing) {
                EmailSequence::startReactivation($user);
                $this->line("  Новая реактивационная серия для #{$user->id} ({$user->email})");
            }
        }
    }
}
