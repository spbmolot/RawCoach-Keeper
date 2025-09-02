<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPayments = Payment::count();
        $succeededPayments = Payment::where('status', 'succeeded')->count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $failedPayments = Payment::where('status', 'failed')->count();
        $refundedPayments = Payment::where('status', 'refunded')->count();

        // Общая сумма успешных платежей
        $totalRevenue = Payment::where('status', 'succeeded')->sum('amount');
        
        // Платежи за сегодня
        $todayPayments = Payment::whereDate('created_at', today())->count();
        $todayRevenue = Payment::whereDate('created_at', today())
            ->where('status', 'succeeded')
            ->sum('amount');

        // Платежи за месяц
        $monthPayments = Payment::where('created_at', '>=', now()->startOfMonth())->count();
        $monthRevenue = Payment::where('created_at', '>=', now()->startOfMonth())
            ->where('status', 'succeeded')
            ->sum('amount');

        // Конверсия платежей
        $conversionRate = $totalPayments > 0 ? round(($succeededPayments / $totalPayments) * 100, 1) : 0;

        // Средний чек
        $averagePayment = $succeededPayments > 0 ? round($totalRevenue / $succeededPayments, 0) : 0;

        return [
            Stat::make('Всего платежей', $totalPayments)
                ->description('Общее количество')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('primary'),

            Stat::make('Успешные', $succeededPayments)
                ->description('Завершенные платежи')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Ожидают', $pendingPayments)
                ->description('В процессе оплаты')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Неудачные', $failedPayments)
                ->description('Отклоненные платежи')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Общий доход', '₽' . number_format($totalRevenue, 0, ',', ' '))
                ->description('Все успешные платежи')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('За сегодня', '₽' . number_format($todayRevenue, 0, ',', ' '))
                ->description($todayPayments . ' платежей')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('За месяц', '₽' . number_format($monthRevenue, 0, ',', ' '))
                ->description($monthPayments . ' платежей')
                ->descriptionIcon('heroicon-m-chart-bar-square')
                ->color('primary'),

            Stat::make('Конверсия', $conversionRate . '%')
                ->description('Успешные / Всего')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($conversionRate > 80 ? 'success' : ($conversionRate > 60 ? 'warning' : 'danger')),

            Stat::make('Средний чек', '₽' . number_format($averagePayment, 0, ',', ' '))
                ->description('На успешный платеж')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('primary'),

            Stat::make('Возвраты', $refundedPayments)
                ->description('Возвращенные платежи')
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->color($refundedPayments > 0 ? 'warning' : 'success'),
        ];
    }
}
