<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\Plan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Пользователи
        $totalUsers = User::count();
        $newUsersThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();
        
        // Подписки
        $activeSubscriptions = UserSubscription::where('status', 'active')->count();
        $totalSubscriptions = UserSubscription::count();
        
        // Платежи
        $monthRevenue = Payment::where('created_at', '>=', now()->startOfMonth())
            ->where('status', 'paid')
            ->sum('amount');
        $todayRevenue = Payment::whereDate('created_at', today())
            ->where('status', 'paid')
            ->sum('amount');
        
        // Планы
        $totalPlans = Plan::where('is_active', true)->count();
        
        // Конверсия
        $conversionRate = $totalUsers > 0 ? round(($activeSubscriptions / $totalUsers) * 100, 1) : 0;

        return [
            Stat::make('Всего пользователей', $totalUsers)
                ->description($newUsersThisMonth . ' новых за месяц')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Активные подписки', $activeSubscriptions)
                ->description('из ' . $totalSubscriptions . ' всего')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Доход за месяц', '₽' . number_format($monthRevenue, 0, ',', ' '))
                ->description('Текущий месяц')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Доход сегодня', '₽' . number_format($todayRevenue, 0, ',', ' '))
                ->description('За сегодня')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Конверсия', $conversionRate . '%')
                ->description('Подписчики / Пользователи')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($conversionRate > 20 ? 'success' : ($conversionRate > 10 ? 'warning' : 'danger')),

            Stat::make('Активные планы', $totalPlans)
                ->description('Доступно для покупки')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
        ];
    }
}
