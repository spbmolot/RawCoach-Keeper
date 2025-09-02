<?php

namespace App\Filament\Resources\UserSubscriptionResource\Widgets;

use App\Models\UserSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSubscriptions = UserSubscription::count();
        $activeSubscriptions = UserSubscription::where('status', 'active')->count();
        $pendingSubscriptions = UserSubscription::where('status', 'pending')->count();
        $cancelledSubscriptions = UserSubscription::where('status', 'cancelled')->count();
        $expiredSubscriptions = UserSubscription::where('status', 'expired')->count();
        
        // Подписки, истекающие в ближайшие 7 дней
        $expiringSoon = UserSubscription::where('status', 'active')
            ->where('ends_at', '<=', now()->addDays(7))
            ->where('ends_at', '>', now())
            ->count();

        // Новые подписки за последние 30 дней
        $newSubscriptionsThisMonth = UserSubscription::where('created_at', '>=', now()->subDays(30))->count();
        
        // Подписки с автопродлением
        $autoRenewalSubscriptions = UserSubscription::where('status', 'active')
            ->where('auto_renewal', true)
            ->count();

        // Общий доход от активных подписок
        $totalRevenue = UserSubscription::where('status', 'active')->sum('price');

        return [
            Stat::make('Всего подписок', $totalSubscriptions)
                ->description('Общее количество подписок')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            Stat::make('Активные', $activeSubscriptions)
                ->description('Активные подписки')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Ожидающие', $pendingSubscriptions)
                ->description('Ожидают оплаты')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Истекают скоро', $expiringSoon)
                ->description('В ближайшие 7 дней')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($expiringSoon > 0 ? 'danger' : 'success'),

            Stat::make('Новые за месяц', $newSubscriptionsThisMonth)
                ->description('За последние 30 дней')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('info'),

            Stat::make('Автопродление', $autoRenewalSubscriptions)
                ->description('Включено автопродление')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('primary'),

            Stat::make('Общий доход', '₽' . number_format($totalRevenue, 0, ',', ' '))
                ->description('От активных подписок')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Конверсия', $totalSubscriptions > 0 ? round(($activeSubscriptions / $totalSubscriptions) * 100, 1) . '%' : '0%')
                ->description('Активные / Всего')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($totalSubscriptions > 0 && ($activeSubscriptions / $totalSubscriptions) > 0.7 ? 'success' : 'warning'),
        ];
    }
}
