<?php

namespace App\Console\Commands;

use App\Mail\InactivityReminder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInactivityReminder extends Command
{
    protected $signature = 'emails:send-inactivity-reminder
        {--days=3 : Дней неактивности для срабатывания}
        {--limit=50 : Максимум писем за запуск}';

    protected $description = 'Отправляет письмо «вы пропустили N дней» подписчикам';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $limit = (int) $this->option('limit');

        // Найти активных подписчиков, которые не заходили N дней
        $users = User::where('is_active', true)
            ->where('email_notifications', true)
            ->whereNotNull('last_login_at')
            ->where('last_login_at', '<=', now()->subDays($days))
            ->where('last_login_at', '>=', now()->subDays($days + 1)) // только точный день, чтобы не слать повторно
            ->whereHas('activeSubscription')
            ->limit($limit)
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            $daysInactive = (int) $user->last_login_at->diffInDays(now());

            try {
                Mail::to($user->email)->send(new InactivityReminder($user, $daysInactive));
                $sent++;

                $this->line("  ✓ Напоминание отправлено: {$user->email} (неактивен {$daysInactive} дн.)");

                Log::info('Inactivity reminder sent', [
                    'user_id' => $user->id,
                    'days_inactive' => $daysInactive,
                    'email' => $user->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Inactivity reminder failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("  ✗ Ошибка для {$user->email}: {$e->getMessage()}");
            }
        }

        $this->info("Напоминания о неактивности: отправлено {$sent}");

        return Command::SUCCESS;
    }
}
