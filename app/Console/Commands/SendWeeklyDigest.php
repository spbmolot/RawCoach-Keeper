<?php

namespace App\Console\Commands;

use App\Mail\WeeklyDigest;
use App\Models\Day;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklyDigest extends Command
{
    protected $signature = 'emails:send-weekly-digest {--limit=100 : Максимум писем за запуск}';

    protected $description = 'Отправляет еженедельный дайджест с превью меню';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        // Получить текущее меню
        $currentMenu = Menu::where('type', 'current')
            ->where('is_published', true)
            ->first();

        if (!$currentMenu) {
            $this->warn('Нет активного опубликованного меню — дайджест не отправлен');
            return Command::SUCCESS;
        }

        // Определить дни текущей недели (по day_number)
        $currentDayOfMonth = now()->day;
        $weekStart = $currentDayOfMonth;
        $weekEnd = min($currentDayOfMonth + 6, 31);

        $weekDays = Day::where('menu_id', $currentMenu->id)
            ->whereBetween('day_number', [$weekStart, $weekEnd])
            ->where('is_active', true)
            ->with(['dayMeals.recipe' => function ($q) {
                $q->select('id', 'title', 'slug', 'calories', 'main_image', 'prep_time', 'cook_time');
            }])
            ->orderBy('day_number')
            ->get();

        // Новые рецепты за последнюю неделю
        $newRecipes = Recipe::where('is_published', true)
            ->where('created_at', '>=', now()->subWeek())
            ->select('id', 'title', 'slug', 'calories', 'main_image', 'description')
            ->limit(5)
            ->get();

        // Отправить подписчикам с активной подпиской
        $users = User::where('is_active', true)
            ->where('email_notifications', true)
            ->whereHas('activeSubscription')
            ->limit($limit)
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(
                    new WeeklyDigest($user, $weekDays, $newRecipes, $currentMenu->title)
                );
                $sent++;
            } catch (\Exception $e) {
                Log::error('Weekly digest email failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Также отправить бесплатную версию не-подписчикам (превью Дня 1)
        $freeUsers = User::where('is_active', true)
            ->where('email_notifications', true)
            ->whereDoesntHave('activeSubscription')
            ->where('created_at', '>=', now()->subMonths(3)) // только относительно свежие пользователи
            ->limit($limit)
            ->get();

        $freeDay = Day::where('menu_id', $currentMenu->id)
            ->where('day_number', 1)
            ->where('is_active', true)
            ->with(['dayMeals.recipe' => function ($q) {
                $q->select('id', 'title', 'slug', 'calories', 'main_image', 'prep_time', 'cook_time');
            }])
            ->get();

        foreach ($freeUsers as $user) {
            try {
                Mail::to($user->email)->send(
                    new WeeklyDigest($user, $freeDay, $newRecipes, $currentMenu->title)
                );
                $sent++;
            } catch (\Exception $e) {
                Log::error('Weekly digest (free) email failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Еженедельный дайджест: отправлено {$sent} писем");

        return Command::SUCCESS;
    }
}
