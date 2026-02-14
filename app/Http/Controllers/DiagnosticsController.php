<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Plan;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\Day;
use App\Models\Coupon;
use App\Models\PersonalPlan;
use App\Models\Questionnaire;

class DiagnosticsController extends Controller
{
    private array $checks = [];
    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.diagnostics.key', '');
    }

    public function index(Request $request)
    {
        // Простая защита по ключу
        if (empty($this->secretKey) || $request->get('key') !== $this->secretKey) {
            abort(403, 'Доступ запрещён.');
        }

        $startTime = microtime(true);

        // Выполняем все проверки
        $this->checkEnvironment();
        $this->checkDatabase();
        $this->checkTables();
        $this->checkDataIntegrity();
        $this->checkStorage();
        $this->checkCache();
        $this->checkQueue();
        $this->checkPaymentServices();
        $this->checkCriticalRoutes();
        $this->checkPermissions();
        $this->checkLogs();
        $this->checkFilamentAdmin();
        $this->checkDashboardFeatures();
        $this->checkApiEndpoints();
        $this->checkViews();
        $this->checkAssets();

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        // Подсчёт статистики
        $stats = [
            'total' => count($this->checks),
            'passed' => count(array_filter($this->checks, fn($c) => $c['status'] === 'ok')),
            'warnings' => count(array_filter($this->checks, fn($c) => $c['status'] === 'warning')),
            'errors' => count(array_filter($this->checks, fn($c) => $c['status'] === 'error')),
        ];

        return view('diagnostics.index', [
            'checks' => $this->checks,
            'stats' => $stats,
            'executionTime' => $executionTime,
            'timestamp' => now()->format('d.m.Y H:i:s'),
            'environment' => app()->environment(),
        ]);
    }

    private function addCheck(string $category, string $name, string $status, string $message, ?string $details = null): void
    {
        $this->checks[] = [
            'category' => $category,
            'name' => $name,
            'status' => $status, // ok, warning, error
            'message' => $message,
            'details' => $details,
        ];
    }

    private function checkEnvironment(): void
    {
        $category = 'Окружение';

        // APP_ENV
        $env = app()->environment();
        if ($env === 'production') {
            $this->addCheck($category, 'APP_ENV', 'ok', "Окружение: {$env}");
        } else {
            $this->addCheck($category, 'APP_ENV', 'warning', "Окружение: {$env}", 'На продакшене должно быть production');
        }

        // APP_DEBUG
        if (config('app.debug') && $env === 'production') {
            $this->addCheck($category, 'APP_DEBUG', 'error', 'Debug режим ВКЛЮЧЁН на продакшене!', 'Установите APP_DEBUG=false');
        } else {
            $this->addCheck($category, 'APP_DEBUG', 'ok', 'Debug режим: ' . (config('app.debug') ? 'вкл' : 'выкл'));
        }

        // APP_KEY
        if (empty(config('app.key'))) {
            $this->addCheck($category, 'APP_KEY', 'error', 'APP_KEY не установлен!', 'Выполните: php artisan key:generate');
        } else {
            $this->addCheck($category, 'APP_KEY', 'ok', 'APP_KEY установлен');
        }

        // APP_URL
        $appUrl = config('app.url');
        if (str_contains($appUrl, 'localhost') && $env === 'production') {
            $this->addCheck($category, 'APP_URL', 'warning', "APP_URL: {$appUrl}", 'На продакшене должен быть реальный домен');
        } else {
            $this->addCheck($category, 'APP_URL', 'ok', "APP_URL: {$appUrl}");
        }

        // PHP Version
        $phpVersion = PHP_VERSION;
        if (version_compare($phpVersion, '8.2.0', '>=')) {
            $this->addCheck($category, 'PHP Version', 'ok', "PHP {$phpVersion}");
        } else {
            $this->addCheck($category, 'PHP Version', 'error', "PHP {$phpVersion}", 'Требуется PHP 8.2+');
        }

        // Расширения PHP
        $requiredExtensions = ['pdo', 'pdo_pgsql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'gd'];
        $missingExtensions = [];
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $missingExtensions[] = $ext;
            }
        }
        if (empty($missingExtensions)) {
            $this->addCheck($category, 'PHP Extensions', 'ok', 'Все необходимые расширения установлены');
        } else {
            $this->addCheck($category, 'PHP Extensions', 'error', 'Отсутствуют расширения', implode(', ', $missingExtensions));
        }

        // Память
        $memoryLimit = ini_get('memory_limit');
        $this->addCheck($category, 'Memory Limit', 'ok', "Memory limit: {$memoryLimit}");

        // Max execution time
        $maxExecTime = ini_get('max_execution_time');
        $this->addCheck($category, 'Max Execution Time', 'ok', "Max execution time: {$maxExecTime}s");
    }

    private function checkDatabase(): void
    {
        $category = 'База данных';

        try {
            $pdo = DB::connection()->getPdo();
            $this->addCheck($category, 'Подключение', 'ok', 'Подключение к БД успешно');

            // Версия PostgreSQL
            $version = DB::selectOne('SHOW server_version')->server_version;
            $this->addCheck($category, 'Версия PostgreSQL', 'ok', "PostgreSQL {$version}");

            // Имя БД
            $dbName = DB::connection()->getDatabaseName();
            $this->addCheck($category, 'База данных', 'ok', "Имя БД: {$dbName}");

        } catch (\Exception $e) {
            $this->addCheck($category, 'Подключение', 'error', 'Ошибка подключения к БД', $e->getMessage());
        }
    }

    private function checkTables(): void
    {
        $category = 'Таблицы БД';

        $requiredTables = [
            'users' => 'Пользователи',
            'plans' => 'Тарифы',
            'user_subscriptions' => 'Подписки',
            'payments' => 'Платежи',
            'menus' => 'Меню',
            'days' => 'Дни меню',
            'recipes' => 'Рецепты',
            'recipe_ingredients' => 'Ингредиенты',
            'day_meals' => 'Приёмы пищи',
            'coupons' => 'Купоны',
            'coupon_usages' => 'Использование купонов',
            'questionnaires' => 'Анкеты',
            'personal_plans' => 'Персональные планы',
            'personal_plan_days' => 'Дни персональных планов',
            'roles' => 'Роли (Spatie)',
            'permissions' => 'Разрешения (Spatie)',
            'model_has_roles' => 'Связь моделей с ролями',
        ];

        foreach ($requiredTables as $table => $description) {
            try {
                if (Schema::hasTable($table)) {
                    $count = DB::table($table)->count();
                    $this->addCheck($category, $description, 'ok', "Таблица {$table}: {$count} записей");
                } else {
                    $this->addCheck($category, $description, 'error', "Таблица {$table} не существует!", 'Выполните миграции');
                }
            } catch (\Exception $e) {
                $this->addCheck($category, $description, 'error', "Ошибка доступа к {$table}", $e->getMessage());
            }
        }
    }

    private function checkDataIntegrity(): void
    {
        $category = 'Целостность данных';

        try {
            // Проверка тарифов
            $plansCount = Plan::count();
            if ($plansCount >= 4) {
                $this->addCheck($category, 'Тарифы', 'ok', "Тарифов в системе: {$plansCount}");
            } else {
                $this->addCheck($category, 'Тарифы', 'warning', "Тарифов: {$plansCount}", 'Ожидается минимум 4 тарифа (standard, premium, personal, trial)');
            }

            // Проверка обязательных тарифов
            $requiredPlans = ['monthly', 'yearly', 'personal', 'trial'];
            $existingTypes = Plan::whereIn('type', $requiredPlans)->pluck('type')->toArray();
            $missingTypes = array_diff($requiredPlans, $existingTypes);
            if (empty($missingTypes)) {
                $this->addCheck($category, 'Типы тарифов', 'ok', 'Все типы тарифов присутствуют');
            } else {
                $this->addCheck($category, 'Типы тарифов', 'error', 'Отсутствуют типы тарифов', implode(', ', $missingTypes));
            }

            // Проверка пользователей
            $usersCount = User::count();
            $this->addCheck($category, 'Пользователи', 'ok', "Пользователей: {$usersCount}");

            // Проверка админов
            $adminsCount = User::role('admin')->count();
            if ($adminsCount > 0) {
                $this->addCheck($category, 'Администраторы', 'ok', "Администраторов: {$adminsCount}");
            } else {
                $this->addCheck($category, 'Администраторы', 'warning', 'Нет администраторов', 'Создайте хотя бы одного админа');
            }

            // Проверка активных подписок
            $activeSubscriptions = UserSubscription::where('status', 'active')->count();
            $this->addCheck($category, 'Активные подписки', 'ok', "Активных подписок: {$activeSubscriptions}");

            // Подписки без пользователей (orphaned)
            $orphanedSubscriptions = UserSubscription::whereNotIn('user_id', User::pluck('id'))->count();
            if ($orphanedSubscriptions === 0) {
                $this->addCheck($category, 'Orphaned подписки', 'ok', 'Нет подписок без пользователей');
            } else {
                $this->addCheck($category, 'Orphaned подписки', 'warning', "Подписок без пользователей: {$orphanedSubscriptions}", 'Рекомендуется очистить');
            }

            // Проверка меню
            $menusCount = Menu::count();
            $publishedMenus = Menu::where('is_published', true)->count();
            $this->addCheck($category, 'Меню', 'ok', "Всего меню: {$menusCount}, опубликованных: {$publishedMenus}");

            // Проверка рецептов
            $recipesCount = Recipe::count();
            if ($recipesCount > 0) {
                $this->addCheck($category, 'Рецепты', 'ok', "Рецептов: {$recipesCount}");
            } else {
                $this->addCheck($category, 'Рецепты', 'warning', 'Нет рецептов', 'Добавьте рецепты через админку');
            }

            // Рецепты без ингредиентов
            $recipesWithoutIngredients = Recipe::whereDoesntHave('ingredients')->count();
            if ($recipesWithoutIngredients === 0) {
                $this->addCheck($category, 'Рецепты без ингредиентов', 'ok', 'Все рецепты имеют ингредиенты');
            } else {
                $this->addCheck($category, 'Рецепты без ингредиентов', 'warning', "Рецептов без ингредиентов: {$recipesWithoutIngredients}");
            }

            // Платежи
            $paymentsCount = Payment::count();
            $successfulPayments = Payment::where('status', 'succeeded')->count();
            $pendingPayments = Payment::where('status', 'pending')->count();
            $this->addCheck($category, 'Платежи', 'ok', "Всего: {$paymentsCount}, успешных: {$successfulPayments}, ожидающих: {$pendingPayments}");

            // Купоны
            $couponsCount = Coupon::count();
            $activeCoupons = Coupon::where('is_active', true)->count();
            $this->addCheck($category, 'Купоны', 'ok', "Всего купонов: {$couponsCount}, активных: {$activeCoupons}");

        } catch (\Exception $e) {
            $this->addCheck($category, 'Ошибка проверки', 'error', 'Ошибка при проверке данных', $e->getMessage());
        }
    }

    private function checkStorage(): void
    {
        $category = 'Файловое хранилище';

        // Проверка директорий
        $directories = [
            'storage/app' => storage_path('app'),
            'storage/logs' => storage_path('logs'),
            'storage/framework/cache' => storage_path('framework/cache'),
            'storage/framework/sessions' => storage_path('framework/sessions'),
            'storage/framework/views' => storage_path('framework/views'),
            'public/storage' => public_path('storage'),
        ];

        foreach ($directories as $name => $path) {
            if (is_dir($path)) {
                // Пробуем записать тестовый файл для проверки прав
                $testFile = $path . DIRECTORY_SEPARATOR . '.write_test_' . time();
                $canWrite = @file_put_contents($testFile, 'test') !== false;
                if ($canWrite) {
                    @unlink($testFile);
                    $this->addCheck($category, $name, 'ok', "Директория существует и доступна для записи");
                } else {
                    // Проверяем стандартным методом как fallback
                    if (is_writable($path)) {
                        $this->addCheck($category, $name, 'ok', "Директория существует и доступна для записи");
                    } else {
                        $this->addCheck($category, $name, 'warning', "Директория может быть недоступна для записи", "Путь: {$path}\nРешение: chmod -R 775 storage или chown www-data:www-data storage");
                    }
                }
            } else {
                $this->addCheck($category, $name, 'error', "Директория не существует", "Путь: {$path}\nРешение: mkdir -p {$path} && chmod 775 {$path}");
            }
        }

        // Проверка симлинка storage
        $storageLinkPath = public_path('storage');
        if (is_link($storageLinkPath) || is_dir($storageLinkPath)) {
            $this->addCheck($category, 'Storage Link', 'ok', 'Симлинк public/storage существует');
        } else {
            $this->addCheck($category, 'Storage Link', 'error', 'Симлинк не создан', 'Выполните: php artisan storage:link');
        }

        // Свободное место на диске
        $freeSpace = disk_free_space(storage_path());
        $freeSpaceGB = round($freeSpace / 1024 / 1024 / 1024, 2);
        if ($freeSpaceGB > 1) {
            $this->addCheck($category, 'Свободное место', 'ok', "Свободно: {$freeSpaceGB} GB");
        } else {
            $this->addCheck($category, 'Свободное место', 'warning', "Мало места: {$freeSpaceGB} GB", 'Рекомендуется освободить место');
        }
    }

    private function checkCache(): void
    {
        $category = 'Кэш';

        try {
            $testKey = 'diagnostics_test_' . time();
            Cache::put($testKey, 'test_value', 60);
            $value = Cache::get($testKey);
            Cache::forget($testKey);

            if ($value === 'test_value') {
                $this->addCheck($category, 'Запись/чтение', 'ok', 'Кэш работает корректно');
            } else {
                $this->addCheck($category, 'Запись/чтение', 'error', 'Кэш не работает');
            }

            $cacheDriver = config('cache.default');
            $this->addCheck($category, 'Драйвер', 'ok', "Драйвер кэша: {$cacheDriver}");

        } catch (\Exception $e) {
            $this->addCheck($category, 'Ошибка', 'error', 'Ошибка кэша', $e->getMessage());
        }
    }

    private function checkQueue(): void
    {
        $category = 'Очереди';

        $queueDriver = config('queue.default');
        $this->addCheck($category, 'Драйвер', 'ok', "Драйвер очередей: {$queueDriver}");

        if ($queueDriver === 'sync') {
            $this->addCheck($category, 'Режим', 'warning', 'Очереди работают синхронно', 'Для продакшена рекомендуется database или redis');
        }

        // Проверка таблицы jobs если используется database
        if ($queueDriver === 'database') {
            if (Schema::hasTable('jobs')) {
                $pendingJobs = DB::table('jobs')->count();
                $this->addCheck($category, 'Pending Jobs', 'ok', "Задач в очереди: {$pendingJobs}");
            } else {
                $this->addCheck($category, 'Таблица jobs', 'error', 'Таблица jobs не существует', 'Выполните миграции');
            }

            if (Schema::hasTable('failed_jobs')) {
                $failedJobs = DB::table('failed_jobs')->count();
                if ($failedJobs === 0) {
                    $this->addCheck($category, 'Failed Jobs', 'ok', 'Нет упавших задач');
                } else {
                    $this->addCheck($category, 'Failed Jobs', 'warning', "Упавших задач: {$failedJobs}", 'Проверьте логи');
                }
            }
        }
    }

    private function checkPaymentServices(): void
    {
        $category = 'Платёжные системы';

        // YooKassa
        $yooKassaShopId = config('services.yookassa.shop_id');
        $yooKassaSecret = config('services.yookassa.secret_key');

        if (!empty($yooKassaShopId) && !empty($yooKassaSecret)) {
            $this->addCheck($category, 'YooKassa', 'ok', 'Настройки YooKassa заданы');
        } else {
            $missing = [];
            if (empty($yooKassaShopId)) $missing[] = 'YOOKASSA_SHOP_ID';
            if (empty($yooKassaSecret)) $missing[] = 'YOOKASSA_SECRET_KEY';
            $this->addCheck($category, 'YooKassa', 'warning', 'YooKassa не настроена', 'Отсутствуют: ' . implode(', ', $missing));
        }

        // CloudPayments
        $cpPublicId = config('services.cloudpayments.public_id');
        $cpApiSecret = config('services.cloudpayments.secret_key');

        if (!empty($cpPublicId) && !empty($cpApiSecret)) {
            $this->addCheck($category, 'CloudPayments', 'ok', 'Настройки CloudPayments заданы');
        } else {
            $missing = [];
            if (empty($cpPublicId)) $missing[] = 'CLOUDPAYMENTS_PUBLIC_ID';
            if (empty($cpApiSecret)) $missing[] = 'CLOUDPAYMENTS_API_SECRET';
            $this->addCheck($category, 'CloudPayments', 'warning', 'CloudPayments не настроена', 'Отсутствуют: ' . implode(', ', $missing));
        }
    }

    private function checkCriticalRoutes(): void
    {
        $category = 'Маршруты';

        $criticalRoutes = [
            'home' => '/',
            'login' => '/login',
            'register' => '/register',
            'dashboard' => '/dashboard',
            'plans.index' => '/plans',
            'webhook.yookassa' => '/webhook/yookassa',
            'webhook.cloudpayments' => '/webhook/cloudpayments',
        ];

        foreach ($criticalRoutes as $name => $path) {
            try {
                $route = app('router')->getRoutes()->getByName($name);
                if ($route) {
                    $this->addCheck($category, $name, 'ok', "Маршрут {$name} зарегистрирован");
                } else {
                    $this->addCheck($category, $name, 'error', "Маршрут {$name} не найден");
                }
            } catch (\Exception $e) {
                $this->addCheck($category, $name, 'warning', "Не удалось проверить маршрут {$name}");
            }
        }
    }

    private function checkPermissions(): void
    {
        $category = 'Роли и права';

        try {
            $roles = DB::table('roles')->pluck('name')->toArray();
            $expectedRoles = ['user', 'subscriber_standard', 'subscriber_premium', 'subscriber_personal', 'advertiser', 'editor', 'admin'];

            $missingRoles = array_diff($expectedRoles, $roles);
            if (empty($missingRoles)) {
                $this->addCheck($category, 'Роли', 'ok', 'Все роли созданы: ' . implode(', ', $roles));
            } else {
                $this->addCheck($category, 'Роли', 'warning', 'Отсутствуют роли', implode(', ', $missingRoles));
            }

        } catch (\Exception $e) {
            $this->addCheck($category, 'Ошибка', 'error', 'Ошибка проверки ролей', $e->getMessage());
        }
    }

    private function checkLogs(): void
    {
        $category = 'Логи';

        $logPath = storage_path('logs/laravel.log');
        
        if (file_exists($logPath)) {
            $logSize = filesize($logPath);
            $logSizeMB = round($logSize / 1024 / 1024, 2);
            
            if ($logSizeMB > 100) {
                $this->addCheck($category, 'Размер лога', 'warning', "Лог файл: {$logSizeMB} MB", 'Рекомендуется ротация логов');
            } else {
                $this->addCheck($category, 'Размер лога', 'ok', "Лог файл: {$logSizeMB} MB");
            }

            // Проверка последних ошибок
            $lastLines = $this->getLastLogErrors($logPath, 10);
            if (empty($lastLines)) {
                $this->addCheck($category, 'Последние ошибки', 'ok', 'Критических ошибок не найдено');
            } else {
                $this->addCheck($category, 'Последние ошибки', 'warning', 'Найдены ошибки в логах', implode("\n", array_slice($lastLines, 0, 5)));
            }
        } else {
            $this->addCheck($category, 'Лог файл', 'warning', 'Файл лога не существует', $logPath);
        }
    }

    private function getLastLogErrors(string $path, int $count): array
    {
        $errors = [];
        
        try {
            $file = new \SplFileObject($path, 'r');
            $file->seek(PHP_INT_MAX);
            $totalLines = $file->key();
            
            $startLine = max(0, $totalLines - 500);
            $file->seek($startLine);
            
            while (!$file->eof()) {
                $line = $file->fgets();
                if (preg_match('/\.(ERROR|CRITICAL|ALERT|EMERGENCY)/', $line)) {
                    $errors[] = trim(substr($line, 0, 200));
                }
            }
        } catch (\Exception $e) {
            // Ignore
        }
        
        return array_slice($errors, -$count);
    }

    private function checkFilamentAdmin(): void
    {
        $category = 'Админ-панель Filament';

        // Проверка наличия Filament
        if (!class_exists(\Filament\FilamentServiceProvider::class)) {
            $this->addCheck($category, 'Filament', 'error', 'Filament не установлен', 'composer require filament/filament');
            return;
        }

        $this->addCheck($category, 'Filament', 'ok', 'Filament установлен');

        // Проверка ресурсов Filament
        $filamentResources = [
            'UserResource' => \App\Filament\Resources\UserResource::class,
            'PlanResource' => \App\Filament\Resources\PlanResource::class,
            'MenuResource' => \App\Filament\Resources\MenuResource::class,
            'RecipeResource' => \App\Filament\Resources\RecipeResource::class,
            'PaymentResource' => \App\Filament\Resources\PaymentResource::class,
            'UserSubscriptionResource' => \App\Filament\Resources\UserSubscriptionResource::class,
        ];

        $missingResources = [];
        foreach ($filamentResources as $name => $class) {
            if (!class_exists($class)) {
                $missingResources[] = $name;
            }
        }

        if (empty($missingResources)) {
            $this->addCheck($category, 'Ресурсы', 'ok', 'Все Filament ресурсы доступны: ' . count($filamentResources));
        } else {
            $this->addCheck($category, 'Ресурсы', 'error', 'Отсутствуют ресурсы', implode(', ', $missingResources));
        }

        // Проверка доступа к админке
        try {
            $adminRoute = route('filament.admin.pages.dashboard');
            $this->addCheck($category, 'Маршрут админки', 'ok', "Админ-панель: {$adminRoute}");
        } catch (\Exception $e) {
            $this->addCheck($category, 'Маршрут админки', 'error', 'Маршрут админки не найден', $e->getMessage());
        }

        // Проверка таблиц для Filament
        $filamentTables = ['notifications', 'activity_log'];
        foreach ($filamentTables as $table) {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                $this->addCheck($category, "Таблица {$table}", 'ok', "Записей: {$count}");
            } else {
                $this->addCheck($category, "Таблица {$table}", 'warning', "Таблица {$table} не существует", 'Опционально для Filament');
            }
        }
    }

    private function checkDashboardFeatures(): void
    {
        $category = 'Личный кабинет';

        // Проверка контроллеров ЛК
        $dashboardControllers = [
            'DashboardController' => \App\Http\Controllers\DashboardController::class,
            'MenuController' => \App\Http\Controllers\MenuController::class,
            'RecipeController' => \App\Http\Controllers\RecipeController::class,
            'SubscriptionController' => \App\Http\Controllers\SubscriptionController::class,
            'PaymentController' => \App\Http\Controllers\PaymentController::class,
            'PersonalPlanController' => \App\Http\Controllers\PersonalPlanController::class,
            'ShoppingListController' => \App\Http\Controllers\ShoppingListController::class,
        ];

        $missingControllers = [];
        foreach ($dashboardControllers as $name => $class) {
            if (!class_exists($class)) {
                $missingControllers[] = $name;
            }
        }

        if (empty($missingControllers)) {
            $this->addCheck($category, 'Контроллеры', 'ok', 'Все контроллеры ЛК доступны');
        } else {
            $this->addCheck($category, 'Контроллеры', 'error', 'Отсутствуют контроллеры', implode(', ', $missingControllers));
        }

        // Проверка маршрутов ЛК
        $dashboardRoutes = [
            'dashboard' => 'Главная ЛК',
            'dashboard.today' => 'Сегодня',
            'dashboard.week' => 'Неделя',
            'dashboard.calendar' => 'Календарь',
            'dashboard.shopping-list' => 'Список покупок',
            'menus.index' => 'Меню',
            'recipes.index' => 'Рецепты',
            'personal-plans.index' => 'Персональные планы',
            'payment.history' => 'История платежей',
        ];

        $missingRoutes = [];
        foreach ($dashboardRoutes as $routeName => $description) {
            try {
                $route = app('router')->getRoutes()->getByName($routeName);
                if (!$route) {
                    $missingRoutes[] = "{$routeName} ({$description})";
                }
            } catch (\Exception $e) {
                $missingRoutes[] = "{$routeName} ({$description})";
            }
        }

        if (empty($missingRoutes)) {
            $this->addCheck($category, 'Маршруты ЛК', 'ok', 'Все маршруты ЛК зарегистрированы');
        } else {
            $this->addCheck($category, 'Маршруты ЛК', 'error', 'Отсутствуют маршруты', implode("\n", $missingRoutes));
        }

        // Проверка сервисов
        $services = [
            'PaymentService' => \App\Services\PaymentService::class ?? null,
        ];

        foreach ($services as $name => $class) {
            if ($class && class_exists($class)) {
                $this->addCheck($category, $name, 'ok', "Сервис {$name} доступен");
            } else {
                $this->addCheck($category, $name, 'warning', "Сервис {$name} не найден");
            }
        }

        // Проверка данных для ЛК
        $menusWithDays = Menu::has('days')->count();
        $menusWithoutDays = Menu::doesntHave('days')->count();
        if ($menusWithoutDays > 0) {
            $this->addCheck($category, 'Меню без дней', 'warning', "Меню без дней: {$menusWithoutDays}", 'Эти меню не будут отображаться корректно');
        } else {
            $this->addCheck($category, 'Меню с днями', 'ok', "Все меню имеют дни: {$menusWithDays}");
        }

        // Дни без рецептов
        $daysWithoutMeals = Day::doesntHave('meals')->count();
        if ($daysWithoutMeals > 0) {
            $this->addCheck($category, 'Дни без приёмов пищи', 'warning', "Дней без приёмов пищи: {$daysWithoutMeals}");
        } else {
            $this->addCheck($category, 'Дни с приёмами пищи', 'ok', 'Все дни имеют приёмы пищи');
        }
    }

    private function checkApiEndpoints(): void
    {
        $category = 'API';

        // Проверка API контроллеров
        $apiControllers = [
            'AuthController' => 'App\\Http\\Controllers\\Api\\AuthController',
            'MenuController' => 'App\\Http\\Controllers\\Api\\MenuController',
            'RecipeController' => 'App\\Http\\Controllers\\Api\\RecipeController',
            'SubscriptionController' => 'App\\Http\\Controllers\\Api\\SubscriptionController',
            'DashboardController' => 'App\\Http\\Controllers\\Api\\DashboardController',
            'PersonalPlanController' => 'App\\Http\\Controllers\\Api\\PersonalPlanController',
        ];

        $missingApiControllers = [];
        foreach ($apiControllers as $name => $class) {
            if (!class_exists($class)) {
                $missingApiControllers[] = $name;
            }
        }

        if (empty($missingApiControllers)) {
            $this->addCheck($category, 'API Контроллеры', 'ok', 'Все API контроллеры доступны');
        } else {
            $this->addCheck($category, 'API Контроллеры', 'error', 'Отсутствуют API контроллеры', implode(', ', $missingApiControllers));
        }

        // Проверка Sanctum
        if (class_exists(\Laravel\Sanctum\SanctumServiceProvider::class)) {
            $this->addCheck($category, 'Sanctum', 'ok', 'Laravel Sanctum установлен');
            
            // Проверка таблицы токенов
            if (Schema::hasTable('personal_access_tokens')) {
                $tokensCount = DB::table('personal_access_tokens')->count();
                $this->addCheck($category, 'API Токены', 'ok', "Токенов в системе: {$tokensCount}");
            } else {
                $this->addCheck($category, 'API Токены', 'error', 'Таблица personal_access_tokens не существует');
            }
        } else {
            $this->addCheck($category, 'Sanctum', 'error', 'Laravel Sanctum не установлен');
        }

        // Проверка CORS
        $corsConfig = config('cors.allowed_origins', []);
        if (!empty($corsConfig) && $corsConfig !== ['*']) {
            $this->addCheck($category, 'CORS', 'ok', 'CORS настроен: ' . implode(', ', array_slice($corsConfig, 0, 3)));
        } else {
            $this->addCheck($category, 'CORS', 'warning', 'CORS разрешает все домены (*)', 'Рекомендуется ограничить для продакшена');
        }
    }

    private function checkViews(): void
    {
        $category = 'Шаблоны (Views)';

        // Критические шаблоны
        $criticalViews = [
            'layouts/app' => resource_path('views/layouts/app.blade.php'),
            'layouts/guest' => resource_path('views/layouts/guest.blade.php'),
            'dashboard/index' => resource_path('views/dashboard/index.blade.php'),
            'auth/login' => resource_path('views/auth/login.blade.php'),
            'auth/register' => resource_path('views/auth/register.blade.php'),
            'home/index' => resource_path('views/home/index.blade.php'),
            'plans/index' => resource_path('views/plans/index.blade.php'),
            'menus/index' => resource_path('views/menus/index.blade.php'),
            'menus/show' => resource_path('views/menus/show.blade.php'),
            'recipes/index' => resource_path('views/recipes/index.blade.php'),
            'recipes/show' => resource_path('views/recipes/show.blade.php'),
        ];

        $missingViews = [];
        foreach ($criticalViews as $name => $path) {
            if (!file_exists($path)) {
                $missingViews[] = $name;
            }
        }

        if (empty($missingViews)) {
            $this->addCheck($category, 'Критические шаблоны', 'ok', 'Все критические шаблоны существуют');
        } else {
            $this->addCheck($category, 'Критические шаблоны', 'error', 'Отсутствуют шаблоны', implode(', ', $missingViews));
        }

        // Проверка скомпилированных views
        $compiledViewsPath = storage_path('framework/views');
        if (is_dir($compiledViewsPath)) {
            $compiledCount = count(glob($compiledViewsPath . '/*.php'));
            $this->addCheck($category, 'Скомпилированные views', 'ok', "Скомпилировано: {$compiledCount} шаблонов");
        }

        // Проверка компонентов
        $componentsPath = resource_path('views/components');
        if (is_dir($componentsPath)) {
            $componentsCount = count(glob($componentsPath . '/*.blade.php')) + count(glob($componentsPath . '/**/*.blade.php'));
            $this->addCheck($category, 'Blade компоненты', 'ok', "Компонентов: {$componentsCount}");
        } else {
            $this->addCheck($category, 'Blade компоненты', 'warning', 'Директория компонентов не найдена');
        }
    }

    private function checkAssets(): void
    {
        $category = 'Ассеты (CSS/JS)';

        // Проверка Vite manifest
        $viteManifestPath = public_path('build/manifest.json');
        if (file_exists($viteManifestPath)) {
            $manifest = json_decode(file_get_contents($viteManifestPath), true);
            if ($manifest) {
                $this->addCheck($category, 'Vite Manifest', 'ok', 'Manifest существует, файлов: ' . count($manifest));
                
                // Проверка основных ассетов
                $requiredAssets = ['resources/css/app.css', 'resources/js/app.js'];
                $missingAssets = [];
                foreach ($requiredAssets as $asset) {
                    if (!isset($manifest[$asset])) {
                        $missingAssets[] = $asset;
                    }
                }
                
                if (empty($missingAssets)) {
                    $this->addCheck($category, 'Основные ассеты', 'ok', 'CSS и JS скомпилированы');
                } else {
                    $this->addCheck($category, 'Основные ассеты', 'error', 'Отсутствуют в manifest', implode(', ', $missingAssets));
                }
            } else {
                $this->addCheck($category, 'Vite Manifest', 'error', 'Manifest повреждён');
            }
        } else {
            $this->addCheck($category, 'Vite Manifest', 'error', 'Manifest не найден', 'Выполните: npm run build');
        }

        // Проверка директории build
        $buildPath = public_path('build');
        if (is_dir($buildPath)) {
            $buildFiles = count(glob($buildPath . '/assets/*'));
            $this->addCheck($category, 'Build директория', 'ok', "Файлов в build/assets: {$buildFiles}");
        } else {
            $this->addCheck($category, 'Build директория', 'error', 'Директория build не существует', 'Выполните: npm run build');
        }

        // Проверка Tailwind
        $tailwindConfig = base_path('tailwind.config.js');
        if (file_exists($tailwindConfig)) {
            $this->addCheck($category, 'Tailwind Config', 'ok', 'tailwind.config.js существует');
        } else {
            $this->addCheck($category, 'Tailwind Config', 'warning', 'tailwind.config.js не найден');
        }

        // Проверка package.json
        $packageJson = base_path('package.json');
        if (file_exists($packageJson)) {
            $package = json_decode(file_get_contents($packageJson), true);
            $deps = array_merge($package['dependencies'] ?? [], $package['devDependencies'] ?? []);
            
            $requiredPackages = ['vite', 'tailwindcss', 'laravel-vite-plugin'];
            $missingPackages = [];
            foreach ($requiredPackages as $pkg) {
                if (!isset($deps[$pkg])) {
                    $missingPackages[] = $pkg;
                }
            }
            
            if (empty($missingPackages)) {
                $this->addCheck($category, 'NPM пакеты', 'ok', 'Все необходимые пакеты в package.json');
            } else {
                $this->addCheck($category, 'NPM пакеты', 'warning', 'Отсутствуют пакеты', implode(', ', $missingPackages));
            }
        }

        // Проверка node_modules
        $nodeModulesPath = base_path('node_modules');
        if (is_dir($nodeModulesPath)) {
            $this->addCheck($category, 'node_modules', 'ok', 'node_modules существует');
        } else {
            $this->addCheck($category, 'node_modules', 'warning', 'node_modules не найден', 'Выполните: npm install');
        }

        // Проверка изображений
        $imagesPath = public_path('images');
        if (is_dir($imagesPath)) {
            $imagesCount = count(glob($imagesPath . '/*.*'));
            $this->addCheck($category, 'Изображения', 'ok', "Изображений в public/images: {$imagesCount}");
        } else {
            $this->addCheck($category, 'Изображения', 'warning', 'Директория public/images не найдена');
        }
    }
}
