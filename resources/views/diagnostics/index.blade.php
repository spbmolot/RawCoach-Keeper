<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Диагностика системы - RawPlan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .status-ok { background-color: #10b981; }
        .status-warning { background-color: #f59e0b; }
        .status-error { background-color: #ef4444; }
        .category-header { background-color: #1f2937; }
        pre { white-space: pre-wrap; word-wrap: break-word; }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">🔍 Диагностика системы RawPlan</h1>
            <div class="flex flex-wrap gap-4 text-sm text-gray-400">
                <span>📅 {{ $timestamp }}</span>
                <span>⚙️ Окружение: <span class="text-yellow-400">{{ $environment }}</span></span>
                <span>⏱️ Время выполнения: {{ $executionTime }} мс</span>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gray-800 rounded-lg p-4 text-center">
                <div class="text-3xl font-bold text-white">{{ $stats['total'] }}</div>
                <div class="text-gray-400 text-sm">Всего проверок</div>
            </div>
            <div class="bg-green-900/50 border border-green-700 rounded-lg p-4 text-center">
                <div class="text-3xl font-bold text-green-400">{{ $stats['passed'] }}</div>
                <div class="text-green-300 text-sm">✓ Успешно</div>
            </div>
            <div class="bg-yellow-900/50 border border-yellow-700 rounded-lg p-4 text-center">
                <div class="text-3xl font-bold text-yellow-400">{{ $stats['warnings'] }}</div>
                <div class="text-yellow-300 text-sm">⚠ Предупреждения</div>
            </div>
            <div class="bg-red-900/50 border border-red-700 rounded-lg p-4 text-center">
                <div class="text-3xl font-bold text-red-400">{{ $stats['errors'] }}</div>
                <div class="text-red-300 text-sm">✗ Ошибки</div>
            </div>
        </div>

        <!-- Overall Status -->
        @if($stats['errors'] > 0)
            <div class="bg-red-900/30 border-l-4 border-red-500 p-4 mb-8 rounded-r-lg">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🚨</span>
                    <div>
                        <h3 class="text-red-400 font-bold">Обнаружены критические ошибки!</h3>
                        <p class="text-red-300 text-sm">Система требует немедленного внимания. Исправьте ошибки, отмеченные красным.</p>
                    </div>
                </div>
            </div>
        @elseif($stats['warnings'] > 0)
            <div class="bg-yellow-900/30 border-l-4 border-yellow-500 p-4 mb-8 rounded-r-lg">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">⚠️</span>
                    <div>
                        <h3 class="text-yellow-400 font-bold">Есть предупреждения</h3>
                        <p class="text-yellow-300 text-sm">Система работает, но рекомендуется обратить внимание на предупреждения.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-green-900/30 border-l-4 border-green-500 p-4 mb-8 rounded-r-lg">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div>
                        <h3 class="text-green-400 font-bold">Система работает нормально</h3>
                        <p class="text-green-300 text-sm">Все проверки пройдены успешно.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Fix Recommendations -->
        @if($stats['errors'] > 0 || $stats['warnings'] > 0)
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-white mb-4">📋 Рекомендации по исправлению</h2>
            <div class="space-y-3 text-sm">
                @php
                    $hasDebugError = collect($checks)->where('name', 'APP_DEBUG')->where('status', 'error')->isNotEmpty();
                    $hasStorageWarnings = collect($checks)->where('category', 'Файловое хранилище')->whereIn('status', ['error', 'warning'])->isNotEmpty();
                    $hasPlanErrors = collect($checks)->where('name', 'Типы тарифов')->where('status', 'error')->isNotEmpty();
                    $hasExtensionErrors = collect($checks)->where('name', 'PHP Extensions')->where('status', 'error')->isNotEmpty();
                @endphp

                @if($hasDebugError)
                <div class="bg-red-900/30 border border-red-700 rounded p-3">
                    <h3 class="text-red-400 font-semibold mb-1">🔴 APP_DEBUG включён на продакшене</h3>
                    <p class="text-gray-300 mb-2">Это серьёзная уязвимость безопасности! Отключите debug режим.</p>
                    <code class="block bg-gray-900 p-2 rounded text-green-400 text-xs">
                        # В файле .env на сервере:<br>
                        APP_DEBUG=false<br>
                        APP_ENV=production
                    </code>
                </div>
                @endif

                @if($hasExtensionErrors)
                <div class="bg-red-900/30 border border-red-700 rounded p-3">
                    <h3 class="text-red-400 font-semibold mb-1">🔴 Отсутствуют PHP расширения</h3>
                    <p class="text-gray-300 mb-2">Установите недостающие расширения PHP.</p>
                    <code class="block bg-gray-900 p-2 rounded text-green-400 text-xs">
                        # Ubuntu/Debian:<br>
                        sudo apt-get install php8.2-gd php8.2-mbstring php8.2-xml<br><br>
                        # CentOS/RHEL:<br>
                        sudo yum install php-gd php-mbstring php-xml<br><br>
                        # После установки перезапустите PHP-FPM:<br>
                        sudo systemctl restart php8.2-fpm
                    </code>
                </div>
                @endif

                @if($hasStorageWarnings)
                <div class="bg-yellow-900/30 border border-yellow-700 rounded p-3">
                    <h3 class="text-yellow-400 font-semibold mb-1">🟡 Проблемы с правами на директории</h3>
                    <p class="text-gray-300 mb-2">Установите правильные права на директории storage и bootstrap/cache.</p>
                    <code class="block bg-gray-900 p-2 rounded text-green-400 text-xs">
                        # Выполните на сервере:<br>
                        cd /path/to/project<br>
                        sudo chown -R www-data:www-data storage bootstrap/cache<br>
                        sudo chmod -R 775 storage bootstrap/cache<br><br>
                        # Создайте симлинк если нужно:<br>
                        php artisan storage:link
                    </code>
                </div>
                @endif

                @if($hasPlanErrors)
                <div class="bg-red-900/30 border border-red-700 rounded p-3">
                    <h3 class="text-red-400 font-semibold mb-1">🔴 Отсутствуют типы тарифов</h3>
                    <p class="text-gray-300 mb-2">Запустите сидер для создания тарифов.</p>
                    <code class="block bg-gray-900 p-2 rounded text-green-400 text-xs">
                        php artisan db:seed --class=PlanSeeder
                    </code>
                </div>
                @endif

                @php
                    $hasViewErrors = collect($checks)->where('category', 'Шаблоны (Views)')->where('status', 'error')->isNotEmpty();
                    $hasAssetErrors = collect($checks)->where('category', 'Ассеты (CSS/JS)')->where('status', 'error')->isNotEmpty();
                    $hasApiErrors = collect($checks)->where('category', 'API')->where('status', 'error')->isNotEmpty();
                    $hasFilamentErrors = collect($checks)->where('category', 'Админ-панель Filament')->where('status', 'error')->isNotEmpty();
                    $hasDashboardErrors = collect($checks)->where('category', 'Личный кабинет')->where('status', 'error')->isNotEmpty();
                @endphp

                @if($hasAssetErrors)
                <div class="bg-red-900/30 border border-red-700 rounded p-3">
                    <h3 class="text-red-400 font-semibold mb-1">🔴 Проблемы с ассетами (CSS/JS)</h3>
                    <p class="text-gray-300 mb-2">Скомпилируйте фронтенд ассеты.</p>
                    <code class="block bg-gray-900 p-2 rounded text-green-400 text-xs">
                        # Установите зависимости и соберите:<br>
                        npm install<br>
                        npm run build<br><br>
                        # Или для продакшена:<br>
                        npm ci --production=false<br>
                        npm run build
                    </code>
                </div>
                @endif

                @if($hasViewErrors)
                <div class="bg-red-900/30 border border-red-700 rounded p-3">
                    <h3 class="text-red-400 font-semibold mb-1">🔴 Отсутствуют шаблоны</h3>
                    <p class="text-gray-300 mb-2">Некоторые Blade-шаблоны не найдены. Проверьте деплой.</p>
                    <code class="block bg-gray-900 p-2 rounded text-green-400 text-xs">
                        # Очистите кэш views:<br>
                        php artisan view:clear<br>
                        php artisan view:cache
                    </code>
                </div>
                @endif

                @if($hasFilamentErrors)
                <div class="bg-red-900/30 border border-red-700 rounded p-3">
                    <h3 class="text-red-400 font-semibold mb-1">🔴 Проблемы с админ-панелью Filament</h3>
                    <p class="text-gray-300 mb-2">Проверьте установку и настройку Filament.</p>
                    <code class="block bg-gray-900 p-2 rounded text-green-400 text-xs">
                        # Опубликуйте ассеты Filament:<br>
                        php artisan filament:assets<br><br>
                        # Очистите кэш:<br>
                        php artisan optimize:clear
                    </code>
                </div>
                @endif

                @if($hasDashboardErrors)
                <div class="bg-yellow-900/30 border border-yellow-700 rounded p-3">
                    <h3 class="text-yellow-400 font-semibold mb-1">🟡 Проблемы в личном кабинете</h3>
                    <p class="text-gray-300 mb-2">Некоторые компоненты ЛК могут работать некорректно.</p>
                    <code class="block bg-gray-900 p-2 rounded text-green-400 text-xs">
                        # Проверьте маршруты:<br>
                        php artisan route:list --name=dashboard<br><br>
                        # Очистите кэш маршрутов:<br>
                        php artisan route:clear<br>
                        php artisan route:cache
                    </code>
                </div>
                @endif

                @if($hasApiErrors)
                <div class="bg-yellow-900/30 border border-yellow-700 rounded p-3">
                    <h3 class="text-yellow-400 font-semibold mb-1">🟡 Проблемы с API</h3>
                    <p class="text-gray-300 mb-2">API может работать некорректно.</p>
                    <code class="block bg-gray-900 p-2 rounded text-green-400 text-xs">
                        # Проверьте Sanctum миграции:<br>
                        php artisan migrate --path=vendor/laravel/sanctum/database/migrations<br><br>
                        # Проверьте API маршруты:<br>
                        php artisan route:list --path=api
                    </code>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Filter Buttons -->
        <div class="flex flex-wrap gap-2 mb-6">
            <button onclick="filterChecks('all')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm transition">
                Все
            </button>
            <button onclick="filterChecks('error')" class="px-4 py-2 bg-red-900/50 hover:bg-red-800 rounded-lg text-sm transition">
                🔴 Только ошибки
            </button>
            <button onclick="filterChecks('warning')" class="px-4 py-2 bg-yellow-900/50 hover:bg-yellow-800 rounded-lg text-sm transition">
                🟡 Только предупреждения
            </button>
            <button onclick="filterChecks('ok')" class="px-4 py-2 bg-green-900/50 hover:bg-green-800 rounded-lg text-sm transition">
                🟢 Только успешные
            </button>
        </div>

        <!-- Checks by Category -->
        @php
            $groupedChecks = collect($checks)->groupBy('category');
        @endphp

        @foreach($groupedChecks as $category => $categoryChecks)
            @php
                $categoryErrors = $categoryChecks->where('status', 'error')->count();
                $categoryWarnings = $categoryChecks->where('status', 'warning')->count();
            @endphp
            <div class="mb-6 check-category">
                <div class="category-header rounded-t-lg px-4 py-3 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        @if($categoryErrors > 0)
                            <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                        @elseif($categoryWarnings > 0)
                            <span class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>
                        @else
                            <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                        @endif
                        {{ $category }}
                    </h2>
                    <span class="text-gray-400 text-sm">{{ $categoryChecks->count() }} проверок</span>
                </div>
                <div class="bg-gray-800 rounded-b-lg overflow-hidden">
                    @foreach($categoryChecks as $check)
                        <div class="check-item border-b border-gray-700 last:border-b-0 p-4 hover:bg-gray-750 transition" data-status="{{ $check['status'] }}">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">
                                    @if($check['status'] === 'ok')
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-500 text-white text-xs">✓</span>
                                    @elseif($check['status'] === 'warning')
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-500 text-white text-xs">!</span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-500 text-white text-xs">✗</span>
                                    @endif
                                </div>
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-medium text-white">{{ $check['name'] }}</h3>
                                        <span class="text-xs px-2 py-1 rounded 
                                            @if($check['status'] === 'ok') bg-green-900 text-green-300
                                            @elseif($check['status'] === 'warning') bg-yellow-900 text-yellow-300
                                            @else bg-red-900 text-red-300
                                            @endif">
                                            {{ $check['status'] === 'ok' ? 'OK' : ($check['status'] === 'warning' ? 'WARNING' : 'ERROR') }}
                                        </span>
                                    </div>
                                    <p class="text-gray-300 text-sm mt-1">{{ $check['message'] }}</p>
                                    @if($check['details'])
                                        <div class="mt-2 p-2 bg-gray-900 rounded text-xs">
                                            <pre class="text-gray-400">{{ $check['details'] }}</pre>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-700 text-center text-gray-500 text-sm">
            <p>RawPlan Diagnostics v1.0 | Для обновления данных перезагрузите страницу</p>
            <p class="mt-2">
                <a href="{{ url('/') }}" class="text-blue-400 hover:text-blue-300">← Вернуться на сайт</a>
            </p>
        </div>
    </div>

    <script>
        function filterChecks(status) {
            const items = document.querySelectorAll('.check-item');
            items.forEach(item => {
                if (status === 'all' || item.dataset.status === status) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            // Hide empty categories
            document.querySelectorAll('.check-category').forEach(category => {
                const visibleItems = category.querySelectorAll('.check-item[style="display: block"], .check-item:not([style])');
                let hasVisible = false;
                category.querySelectorAll('.check-item').forEach(item => {
                    if (item.style.display !== 'none') hasVisible = true;
                });
                category.style.display = hasVisible ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>
