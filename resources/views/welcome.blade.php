<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RawPlan — Планы питания для похудения</title>
    <meta name="description" content="Готовые планы питания 1200–1400 ккал. Меню на месяц, рецепты с КБЖУ и списки покупок. Худейте вкусно!" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --color-primary: #22c55e;
            --color-primary-dark: #16a34a;
            --gradient-primary: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            --gradient-hero: linear-gradient(135deg, #065f46 0%, #047857 50%, #10b981 100%);
        }
        body { font-family: 'Inter', system-ui, sans-serif; }
        .gradient-text {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-gradient {
            background: var(--gradient-hero);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .feature-icon {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        }
        .pulse-dot {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .stats-card {
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }
    </style>
</head>
<body class="antialiased text-gray-900 bg-white">
<div class="flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl hero-gradient flex items-center justify-center">
                        <i data-lucide="salad" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">RawPlan</span>
                </a>
                <nav class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-gray-600 hover:text-green-600 font-medium transition">Возможности</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-green-600 font-medium transition">Как это работает</a>
                    <a href="#pricing" class="text-gray-600 hover:text-green-600 font-medium transition">Тарифы</a>
                    <a href="#faq" class="text-gray-600 hover:text-green-600 font-medium transition">FAQ</a>
                </nav>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                            Мой кабинет
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 font-medium hover:text-green-600 transition">Войти</a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                            Начать бесплатно
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-gradient relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28 relative">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 rounded-full text-sm font-medium mb-6 backdrop-blur-sm border border-white/20">
                        <span class="w-2 h-2 bg-green-400 rounded-full pulse-dot"></span>
                        7 дней бесплатно
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                        Худейте вкусно<br>
                        <span class="text-green-300">без подсчёта калорий</span>
                    </h1>
                    <p class="text-xl text-green-100 mb-8 max-w-lg">
                        Готовые планы питания 1200–1400 ккал с рецептами и списками покупок. Всё уже посчитано за вас.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 mb-10">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-white text-green-700 rounded-xl font-bold text-lg hover:bg-green-50 transition shadow-xl inline-flex items-center justify-center gap-2">
                                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                                Перейти в кабинет
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-green-700 rounded-xl font-bold text-lg hover:bg-green-50 transition shadow-xl inline-flex items-center justify-center gap-2">
                                <i data-lucide="rocket" class="w-5 h-5"></i>
                                Попробовать бесплатно
                            </a>
                            <a href="#how-it-works" class="px-8 py-4 bg-white/10 text-white rounded-xl font-semibold text-lg hover:bg-white/20 transition border border-white/20 inline-flex items-center justify-center gap-2">
                                <i data-lucide="play-circle" class="w-5 h-5"></i>
                                Как это работает
                            </a>
                        @endauth
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="stats-card px-4 py-3 rounded-xl">
                            <div class="text-2xl font-bold">1200+</div>
                            <div class="text-green-200 text-sm">рецептов</div>
                        </div>
                        <div class="stats-card px-4 py-3 rounded-xl">
                            <div class="text-2xl font-bold">50+</div>
                            <div class="text-green-200 text-sm">меню</div>
                        </div>
                        <div class="stats-card px-4 py-3 rounded-xl">
                            <div class="text-2xl font-bold">10k+</div>
                            <div class="text-green-200 text-sm">пользователей</div>
                        </div>
                    </div>
                </div>
                <div class="relative hidden lg:block">
                    <div class="bg-white rounded-3xl shadow-2xl p-6 transform rotate-2 hover:rotate-0 transition-transform duration-500">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-900">Меню на сегодня</h3>
                            <span class="text-sm text-green-600 font-semibold bg-green-50 px-3 py-1 rounded-full">1280 ккал</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-4 p-3 bg-orange-50 rounded-xl">
                                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                    <i data-lucide="sunrise" class="w-6 h-6 text-orange-500"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Завтрак</div>
                                    <div class="text-sm text-gray-500">Овсянка с ягодами и орехами</div>
                                </div>
                                <div class="text-sm font-medium text-gray-600">350 ккал</div>
                            </div>
                            <div class="flex items-center gap-4 p-3 bg-yellow-50 rounded-xl">
                                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                    <i data-lucide="sun" class="w-6 h-6 text-yellow-500"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Обед</div>
                                    <div class="text-sm text-gray-500">Куриная грудка с киноа</div>
                                </div>
                                <div class="text-sm font-medium text-gray-600">420 ккал</div>
                            </div>
                            <div class="flex items-center gap-4 p-3 bg-green-50 rounded-xl">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                    <i data-lucide="apple" class="w-6 h-6 text-green-500"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Перекус</div>
                                    <div class="text-sm text-gray-500">Греческий йогурт с мёдом</div>
                                </div>
                                <div class="text-sm font-medium text-gray-600">150 ккал</div>
                            </div>
                            <div class="flex items-center gap-4 p-3 bg-purple-50 rounded-xl">
                                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                    <i data-lucide="moon" class="w-6 h-6 text-purple-500"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Ужин</div>
                                    <div class="text-sm text-gray-500">Лосось с овощами на пару</div>
                                </div>
                                <div class="text-sm font-medium text-gray-600">360 ккал</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-green-600 font-semibold text-sm uppercase tracking-wider">Возможности</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3 mb-4">Всё для комфортного похудения</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Мы продумали каждую деталь, чтобы вы могли сосредоточиться на результате</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 card-hover shadow-sm border border-gray-100">
                    <div class="feature-icon mb-6">
                        <i data-lucide="calendar-days" class="w-7 h-7 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Меню на месяц</h3>
                    <p class="text-gray-600">Готовые планы питания на каждый день. Завтрак, обед, ужин и перекусы — всё расписано.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 card-hover shadow-sm border border-gray-100">
                    <div class="feature-icon mb-6">
                        <i data-lucide="calculator" class="w-7 h-7 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">КБЖУ посчитаны</h3>
                    <p class="text-gray-600">Калории, белки, жиры и углеводы рассчитаны для каждого блюда. Вам не нужно ничего считать.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 card-hover shadow-sm border border-gray-100">
                    <div class="feature-icon mb-6">
                        <i data-lucide="shopping-cart" class="w-7 h-7 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Списки покупок</h3>
                    <p class="text-gray-600">Автоматические списки продуктов на неделю. Скачивайте в PDF или Excel.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 card-hover shadow-sm border border-gray-100">
                    <div class="feature-icon mb-6">
                        <i data-lucide="chef-hat" class="w-7 h-7 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Простые рецепты</h3>
                    <p class="text-gray-600">Пошаговые инструкции с фото. Даже новичок справится за 30 минут.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 card-hover shadow-sm border border-gray-100">
                    <div class="feature-icon mb-6">
                        <i data-lucide="scale" class="w-7 h-7 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">1200–1400 ккал</h3>
                    <p class="text-gray-600">Оптимальный дефицит для здорового похудения. Минус 4-6 кг в месяц.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 card-hover shadow-sm border border-gray-100">
                    <div class="feature-icon mb-6">
                        <i data-lucide="smartphone" class="w-7 h-7 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Доступ с любого устройства</h3>
                    <p class="text-gray-600">Смотрите меню на телефоне, планшете или компьютере. Всегда под рукой.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="how-it-works" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-green-600 font-semibold text-sm uppercase tracking-wider">Как это работает</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3 mb-4">Начните за 3 простых шага</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
                <div class="text-center relative">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl hero-gradient flex items-center justify-center shadow-lg shadow-green-500/30">
                        <i data-lucide="user-plus" class="w-10 h-10 text-white"></i>
                    </div>
                    <div class="absolute top-10 left-1/2 w-full h-0.5 bg-green-200 hidden md:block" style="transform: translateX(50%);"></div>
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-bold mb-4">Шаг 1</span>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Зарегистрируйтесь</h3>
                    <p class="text-gray-600">Создайте аккаунт за 30 секунд. Нужен только email.</p>
                </div>
                <div class="text-center relative">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl hero-gradient flex items-center justify-center shadow-lg shadow-green-500/30">
                        <i data-lucide="credit-card" class="w-10 h-10 text-white"></i>
                    </div>
                    <div class="absolute top-10 left-1/2 w-full h-0.5 bg-green-200 hidden md:block" style="transform: translateX(50%);"></div>
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-bold mb-4">Шаг 2</span>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Выберите тариф</h3>
                    <p class="text-gray-600">7 дней бесплатно. Потом — от 1990₽ в месяц.</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl hero-gradient flex items-center justify-center shadow-lg shadow-green-500/30">
                        <i data-lucide="utensils" class="w-10 h-10 text-white"></i>
                    </div>
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-bold mb-4">Шаг 3</span>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Получайте меню</h3>
                    <p class="text-gray-600">Новые рецепты каждый месяц. Готовьте и худейте!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="py-24 bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-green-600 font-semibold text-sm uppercase tracking-wider">Тарифы</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3 mb-4">Выберите свой план</h2>
                <p class="text-xl text-gray-600">Начните с 7 дней бесплатно. Отмена в любой момент.</p>
            </div>
            
            <div class="flex justify-center mb-10">
                <div class="bg-gray-100 p-1 rounded-xl inline-flex">
                    <button id="monthlyBtn" class="px-6 py-2.5 rounded-lg font-semibold transition bg-white text-gray-900 shadow-sm">Месяц</button>
                    <button id="yearlyBtn" class="px-6 py-2.5 rounded-lg font-semibold transition text-gray-600 hover:text-gray-900">Год <span class="text-green-600">-25%</span></button>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <div class="bg-white rounded-2xl p-8 border border-gray-200 card-hover">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Стандарт</h3>
                        <p class="text-gray-500 text-sm">Для начинающих</p>
                    </div>
                    <div class="mb-6">
                        <span class="text-4xl font-extrabold text-gray-900 price" data-month="1990" data-year="1493">1990</span>
                        <span class="text-gray-500">₽/<span class="period">мес</span></span>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check" class="w-5 h-5 text-green-500"></i>
                            Меню на текущий месяц
                        </li>
                        <li class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check" class="w-5 h-5 text-green-500"></i>
                            Рецепты с КБЖУ
                        </li>
                        <li class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check" class="w-5 h-5 text-green-500"></i>
                            Списки покупок
                        </li>
                        <li class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check" class="w-5 h-5 text-green-500"></i>
                            Экспорт в PDF
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 text-center font-semibold rounded-xl border-2 border-green-500 text-green-600 hover:bg-green-50 transition">
                        Начать бесплатно
                    </a>
                </div>

                <div class="bg-white rounded-2xl p-8 border-2 border-green-500 card-hover relative shadow-xl shadow-green-500/10">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-green-500 text-white text-sm font-bold px-4 py-1 rounded-full">Популярный</span>
                    </div>
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Премиум</h3>
                        <p class="text-gray-500 text-sm">Максимум возможностей</p>
                    </div>
                    <div class="mb-6">
                        <span class="text-4xl font-extrabold text-gray-900 price" data-month="17910" data-year="1493">17910</span>
                        <span class="text-gray-500">₽/<span class="period">год</span></span>
                        <div class="text-sm text-green-600 font-medium mt-1">Экономия 5970₽</div>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check" class="w-5 h-5 text-green-500"></i>
                            Всё из Стандарт
                        </li>
                        <li class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check" class="w-5 h-5 text-green-500"></i>
                            Доступ к архиву меню
                        </li>
                        <li class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check" class="w-5 h-5 text-green-500"></i>
                            Ранний доступ к новинкам
                        </li>
                        <li class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check" class="w-5 h-5 text-green-500"></i>
                            Приоритетная поддержка
                        </li>
                        <li class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check" class="w-5 h-5 text-green-500"></i>
                            Безлимитный экспорт
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 text-center font-semibold rounded-xl bg-green-500 text-white hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                        Начать бесплатно
                    </a>
                </div>

                <div class="bg-white rounded-2xl p-8 border border-gray-200 card-hover">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Индивидуальный</h3>
                        <p class="text-gray-500 text-sm">Персональный подход</p>
                    </div>
                    <div class="mb-6">
                        <span class="text-4xl font-extrabold text-gray-900">4990</span>
                        <span class="text-gray-500">₽/мес</span>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check" class="w-5 h-5 text-green-500"></i>
                            Всё из Премиум
                        </li>
                        <li class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check" class="w-5 h-5 text-green-500"></i>
                            Персональный план питания
                        </li>
                        <li class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check" class="w-5 h-5 text-green-500"></i>
                            Учёт аллергий и предпочтений
                        </li>
                        <li class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check" class="w-5 h-5 text-green-500"></i>
                            Консультация нутрициолога
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 text-center font-semibold rounded-xl border-2 border-green-500 text-green-600 hover:bg-green-50 transition">
                        Связаться
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-green-600 font-semibold text-sm uppercase tracking-wider">Отзывы</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3 mb-4">Что говорят наши пользователи</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-gray-50 rounded-2xl p-8 card-hover">
                    <div class="flex items-center gap-1 mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                    </div>
                    <p class="text-gray-700 mb-6 text-lg">"За 3 месяца похудела на 12 кг! Рецепты простые и вкусные, муж тоже ест с удовольствием."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                            <span class="text-green-600 font-bold">АК</span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Анна Козлова</div>
                            <div class="text-sm text-gray-500">Москва</div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-2xl p-8 card-hover">
                    <div class="flex items-center gap-1 mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                    </div>
                    <p class="text-gray-700 mb-6 text-lg">"Наконец-то не нужно думать, что готовить. Списки покупок — это гениально!"</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-blue-600 font-bold">ИП</span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Игорь Петров</div>
                            <div class="text-sm text-gray-500">Санкт-Петербург</div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-2xl p-8 card-hover">
                    <div class="flex items-center gap-1 mb-4">
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                        <i data-lucide="star" class="w-5 h-5 text-yellow-400 fill-yellow-400"></i>
                    </div>
                    <p class="text-gray-700 mb-6 text-lg">"Пользуюсь уже полгода. Минус 8 кг и отличное самочувствие. Рекомендую всем!"</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                            <span class="text-purple-600 font-bold">МС</span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Мария Смирнова</div>
                            <div class="text-sm text-gray-500">Казань</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-24 bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-green-600 font-semibold text-sm uppercase tracking-wider">FAQ</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3 mb-4">Частые вопросы</h2>
            </div>
            <div class="space-y-4">
                <details class="bg-white rounded-2xl p-6 group" open>
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <span class="font-semibold text-gray-900 text-lg">Есть ли пробный период?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-4 text-gray-600">Да! Первые 7 дней — бесплатно. Вы получите полный доступ ко всем функциям. Отменить можно в любой момент.</p>
                </details>
                <details class="bg-white rounded-2xl p-6 group">
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <span class="font-semibold text-gray-900 text-lg">Когда я получу меню?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-4 text-gray-600">Сразу после регистрации! Меню на текущий месяц будет доступно в личном кабинете.</p>
                </details>
                <details class="bg-white rounded-2xl p-6 group">
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <span class="font-semibold text-gray-900 text-lg">Можно ли отменить подписку?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-4 text-gray-600">Конечно. Вы можете отменить подписку в любой момент в личном кабинете. Без скрытых условий.</p>
                </details>
                <details class="bg-white rounded-2xl p-6 group">
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <span class="font-semibold text-gray-900 text-lg">Подходит ли меню для вегетарианцев?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-4 text-gray-600">Пока у нас стандартное меню, но в индивидуальном тарифе мы учитываем все ваши предпочтения и ограничения.</p>
                </details>
                <details class="bg-white rounded-2xl p-6 group">
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <span class="font-semibold text-gray-900 text-lg">Какие способы оплаты доступны?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-4 text-gray-600">Банковские карты Visa, MasterCard, МИР. Также доступна оплата через ЮKassa и CloudPayments.</p>
                </details>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-24 hero-gradient relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 right-20 w-64 h-64 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 left-20 w-80 h-80 bg-white rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-6">Готовы изменить свою жизнь?</h2>
            <p class="text-xl text-green-100 mb-10 max-w-2xl mx-auto">Присоединяйтесь к тысячам людей, которые уже достигли своих целей с RawPlan</p>
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-10 py-4 bg-white text-green-700 rounded-xl font-bold text-lg hover:bg-green-50 transition shadow-xl">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    Перейти в кабинет
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-10 py-4 bg-white text-green-700 rounded-xl font-bold text-lg hover:bg-green-50 transition shadow-xl">
                    <i data-lucide="rocket" class="w-5 h-5"></i>
                    Начать бесплатно — 7 дней
                </a>
            @endauth
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                <div class="md:col-span-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 rounded-xl hero-gradient flex items-center justify-center">
                            <i data-lucide="salad" class="w-6 h-6 text-white"></i>
                        </div>
                        <span class="text-xl font-bold text-white">RawPlan</span>
                    </a>
                    <p class="text-gray-400 max-w-sm">Готовые планы питания для здорового похудения. Меню, рецепты и списки покупок — всё в одном месте.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Навигация</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="hover:text-white transition">Возможности</a></li>
                        <li><a href="#pricing" class="hover:text-white transition">Тарифы</a></li>
                        <li><a href="#faq" class="hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Правовая информация</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition">Политика конфиденциальности</a></li>
                        <li><a href="#" class="hover:text-white transition">Пользовательское соглашение</a></li>
                        <li><a href="#" class="hover:text-white transition">Оферта</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-sm">© {{ date('Y') }} RawPlan. Все права защищены.</p>
                <div class="flex items-center gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gray-700 transition">
                        <i data-lucide="instagram" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gray-700 transition">
                        <i data-lucide="send" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gray-700 transition">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</div>

<script>
    lucide.createIcons();
    
    const monthlyBtn = document.getElementById('monthlyBtn');
    const yearlyBtn = document.getElementById('yearlyBtn');
    const prices = document.querySelectorAll('.price');
    const periods = document.querySelectorAll('.period');
    
    if (monthlyBtn && yearlyBtn) {
        monthlyBtn.addEventListener('click', () => {
            monthlyBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
            monthlyBtn.classList.remove('text-gray-600');
            yearlyBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
            yearlyBtn.classList.add('text-gray-600');
            prices.forEach(p => p.textContent = p.dataset.month);
            periods.forEach(p => p.textContent = 'мес');
        });
        yearlyBtn.addEventListener('click', () => {
            yearlyBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
            yearlyBtn.classList.remove('text-gray-600');
            monthlyBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
            monthlyBtn.classList.add('text-gray-600');
            prices.forEach(p => p.textContent = p.dataset.year);
            periods.forEach(p => p.textContent = 'год');
        });
    }
</script>

</body>
</html>

