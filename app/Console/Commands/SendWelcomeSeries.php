<?php

namespace App\Console\Commands;

use App\Mail\WelcomeSeries;
use App\Models\EmailSequence;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeSeries extends Command
{
    protected $signature = 'emails:send-welcome-series {--limit=50 : Максимум писем за запуск}';

    protected $description = 'Отправляет следующее письмо welcome-серии пользователям';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $sequences = EmailSequence::where('sequence_type', 'welcome')
            ->whereNull('completed_at')
            ->whereNull('unsubscribed_at')
            ->where(function ($q) {
                $q->whereNull('next_send_at')
                  ->orWhere('next_send_at', '<=', now());
            })
            ->where('current_step', '<', 5)
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

            // Если пользователь уже оформил подписку — завершаем серию досрочно
            if ($user->hasActiveSubscription() && $sequence->current_step >= 2) {
                $sequence->update(['completed_at' => now()]);
                $this->line("  Пользователь #{$user->id} уже подписчик — серия завершена");
                $skipped++;
                continue;
            }

            $nextStep = $sequence->current_step + 1;

            try {
                Mail::to($user->email)->send(new WelcomeSeries($user, $nextStep));

                $delayHours = WelcomeSeries::getDelayHours($nextStep);
                $sequence->advanceStep($delayHours);

                $sent++;
                $this->line("  ✓ Письмо #{$nextStep} отправлено: {$user->email}");

                Log::info('Welcome series email sent', [
                    'user_id' => $user->id,
                    'step' => $nextStep,
                    'email' => $user->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Welcome series email failed', [
                    'user_id' => $user->id,
                    'step' => $nextStep,
                    'error' => $e->getMessage(),
                ]);
                $this->error("  ✗ Ошибка для {$user->email}: {$e->getMessage()}");
            }
        }

        $this->info("Welcome-серия: отправлено {$sent}, пропущено {$skipped}");

        return Command::SUCCESS;
    }
}
