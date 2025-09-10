<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Navigation\NavigationGroup;
use App\Filament\Widgets\DashboardStatsOverview;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('RawPlan Admin')
            ->authGuard('web')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->homeUrl('/admin')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                DashboardStatsOverview::class,
                Widgets\AccountWidget::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('Пользователи')
                    ->icon('heroicon-o-users')
                    ->collapsed(),
                NavigationGroup::make('Подписки и планы')
                    ->icon('heroicon-o-document-text')
                    ->collapsed(),
                NavigationGroup::make('Платежи')
                    ->icon('heroicon-o-credit-card')
                    ->collapsed(),
                NavigationGroup::make('Контент')
                    ->icon('heroicon-o-book-open')
                    ->collapsed(),
                NavigationGroup::make('Маркетинг')
                    ->icon('heroicon-o-megaphone')
                    ->collapsed(),
                NavigationGroup::make('Система')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->requiresEmailVerification(false);
    }
}
