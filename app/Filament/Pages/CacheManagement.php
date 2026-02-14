<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CacheManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $title = 'Управление кешем';

    protected static ?string $navigationLabel = 'Кеш';

    protected static ?string $navigationGroup = 'Система';

    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.cache-management';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearAllCache')
                ->label('Сбросить весь кеш')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Сбросить весь кеш?')
                ->modalDescription('Будут очищены все закешированные данные: планы, рецепты, меню, статистика. Данные перезагрузятся из БД при следующем запросе.')
                ->modalSubmitActionLabel('Да, сбросить')
                ->action(function () {
                    Cache::flush();
                    Notification::make()
                        ->title('Весь кеш успешно очищен')
                        ->success()
                        ->send();
                }),

            Action::make('clearConfigCache')
                ->label('Сбросить кеш конфигурации')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    Artisan::call('config:clear');
                    Artisan::call('route:clear');
                    Artisan::call('view:clear');
                    Notification::make()
                        ->title('Кеш конфигурации, маршрутов и шаблонов очищен')
                        ->success()
                        ->send();
                }),

            Action::make('clearContentCache')
                ->label('Сбросить кеш контента')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->action(function () {
                    $keys = [
                        'active_plans',
                        'featured_recipes_6',
                        'landing_current_menu',
                        'landing_stats',
                        'demo_recipes_3',
                        'recent_recipes_5',
                        'sitemap_xml',
                    ];

                    // Сброс по месяцам (free day preview)
                    for ($m = 1; $m <= 12; $m++) {
                        $keys[] = "free_day_preview_{$m}_" . now()->year;
                    }

                    foreach ($keys as $key) {
                        Cache::forget($key);
                    }

                    Notification::make()
                        ->title('Кеш контента очищен (' . count($keys) . ' ключей)')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getRedisStats(): array
    {
        try {
            $redis = app('redis')->connection();
            $info = $redis->info();

            $stats = $info['Stats'] ?? $info;
            $memory = $info['Memory'] ?? $info;
            $keyspace = $info['Keyspace'] ?? $info;

            $hits = (int) ($stats['keyspace_hits'] ?? 0);
            $misses = (int) ($stats['keyspace_misses'] ?? 0);
            $total = $hits + $misses;
            $hitRate = $total > 0 ? round(($hits / $total) * 100, 1) : 0;

            // Считаем ключи во всех db
            $totalKeys = 0;
            foreach ($keyspace as $key => $value) {
                if (str_starts_with($key, 'db') && is_string($value)) {
                    preg_match('/keys=(\d+)/', $value, $matches);
                    $totalKeys += (int) ($matches[1] ?? 0);
                }
            }

            return [
                'connected' => true,
                'used_memory' => $memory['used_memory_human'] ?? 'N/A',
                'peak_memory' => $memory['used_memory_peak_human'] ?? 'N/A',
                'hits' => number_format($hits),
                'misses' => number_format($misses),
                'hit_rate' => $hitRate . '%',
                'total_keys' => $totalKeys,
                'uptime_days' => round(($info['Server']['uptime_in_seconds'] ?? $info['uptime_in_seconds'] ?? 0) / 86400, 1),
                'ops_per_sec' => $stats['instantaneous_ops_per_sec'] ?? 0,
            ];
        } catch (\Exception $e) {
            return [
                'connected' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getCacheKeys(): array
    {
        return [
            ['key' => 'active_plans', 'ttl' => '1 час', 'description' => 'Активные тарифы'],
            ['key' => 'featured_recipes_6', 'ttl' => '30 мин', 'description' => 'Рецепты на лендинге'],
            ['key' => 'landing_current_menu', 'ttl' => '30 мин', 'description' => 'Меню на лендинге'],
            ['key' => 'landing_stats', 'ttl' => '1 час', 'description' => 'Счётчики соцдоказательств'],
            ['key' => 'demo_recipes_3', 'ttl' => '30 мин', 'description' => 'Демо-рецепты'],
            ['key' => 'recent_recipes_5', 'ttl' => '10 мин', 'description' => 'Последние рецепты (ЛК)'],
            ['key' => 'free_day_preview_{m}_{y}', 'ttl' => '30 мин', 'description' => 'Бесплатный день (Freemium)'],
            ['key' => 'recipes_{md5(url)}', 'ttl' => '5 мин', 'description' => 'Каталог рецептов'],
            ['key' => 'similar_recipes_{id}', 'ttl' => '10 мин', 'description' => 'Похожие рецепты'],
            ['key' => 'sitemap_xml', 'ttl' => '1 час', 'description' => 'XML карта сайта'],
        ];
    }
}
