<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\Widget;
use App\Filament\Widgets\DashboardStatsOverview;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $title = 'Панель управления';
    
    protected static ?string $navigationLabel = 'Главная';
    
    protected ?string $heading = 'Панель управления RawPlan';
    
    protected ?string $subheading = 'Добро пожаловать в административную панель';

    /**
     * @return array<class-string<Widget>>
     */
    protected function getWidgets(): array
    {
        return [
            AccountWidget::class,
            DashboardStatsOverview::class,
        ];
    }

    /**
     * @return int | string | array<string, int | string | null>
     */
    protected function getColumns(): int|string|array
    {
        return 2;
    }
}
