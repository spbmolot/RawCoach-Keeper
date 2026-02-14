<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RawPlan — Планы питания для похудения</title>
    <meta name="description" content="Готовые планы питания 1200–1400 ккал. Меню на месяц, рецепты с КБЖУ и списки покупок. Худейте вкусно!" />
    <meta name="keywords" content="план питания, похудение, рецепты, КБЖУ, меню на месяц, здоровое питание, диета">
    <meta name="author" content="RawPlan">
    <link rel="canonical" href="{{ url()->current() }}">
    
    {{-- Favicon --}}
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/favicon.ico" sizes="32x32">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#22c55e">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="RawPlan — Планы питания для похудения">
    <meta property="og:description" content="Готовые планы питания 1200–1400 ккал. Меню на месяц, рецепты с КБЖУ и списки покупок.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="RawPlan">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="RawPlan — Планы питания для похудения">
    <meta name="twitter:description" content="Готовые планы питания 1200–1400 ккал. Меню на месяц, рецепты с КБЖУ.">
    <meta name="twitter:image" content="{{ asset('images/og-image.jpg') }}">
    
    {{-- Schema.org --}}
    <x-schema-organization />
    <x-schema-website />
    <x-schema-faq :faqs="[
        ['question' => 'Есть ли пробный период?', 'answer' => 'Да! Первые 7 дней — бесплатно. Вы получите полный доступ ко всем функциям. Отменить можно в любой момент.'],
        ['question' => 'Когда я получу меню?', 'answer' => 'Сразу после регистрации! Меню на текущий месяц будет доступно в личном кабинете.'],
        ['question' => 'Можно ли отменить подписку?', 'answer' => 'Конечно. Вы можете отменить подписку в любой момент в личном кабинете. Без скрытых условий.'],
        ['question' => 'Подходит ли меню для вегетарианцев?', 'answer' => 'Пока у нас стандартное меню, но в индивидуальном тарифе мы учитываем все ваши предпочтения и ограничения.'],
        ['question' => 'Какие способы оплаты доступны?', 'answer' => 'Банковские карты Visa, MasterCard, МИР. Также доступна оплата через ЮKassa и CloudPayments.']
    ]" />
    
    {{-- Analytics --}}
    @include('components.analytics')
    
    {{-- AOS Animation Library --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Alpine.js подключается в конце body после Lucide -->
    <style>
        [x-cloak] { display: none !important; }
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
        
        /* Очень маленькие экраны (до 360px) */
        @media (max-width: 360px) {
            .hero-title { font-size: 1.75rem !important; line-height: 1.2; }
            .hero-subtitle { font-size: 0.95rem !important; }
            .hero-badge { font-size: 0.7rem !important; padding: 0.375rem 0.75rem !important; }
            .hero-btn { padding: 0.75rem 1.25rem !important; font-size: 0.875rem !important; }
            .hero-stats { gap: 0.5rem !important; }
            .hero-stats > div { padding: 0.5rem 0.625rem !important; }
            .hero-stats .stat-value { font-size: 1.25rem !important; }
            .hero-stats .stat-label { font-size: 0.65rem !important; }
            .section-title { font-size: 1.375rem !important; }
            .section-subtitle { font-size: 0.875rem !important; }
            .price-card { padding: 1rem !important; }
            .price-value { font-size: 1.75rem !important; }
            .price-btn { padding: 0.625rem 1rem !important; font-size: 0.8rem !important; }
            .feature-list li { font-size: 0.8rem !important; }
            .faq-question { font-size: 0.9rem !important; }
            .faq-answer { font-size: 0.8rem !important; }
        }
        
        /* Экстремально маленькие экраны (до 320px) */
        @media (max-width: 320px) {
            .hero-title { font-size: 1.5rem !important; }
            .hero-stats { flex-wrap: wrap; justify-content: center; }
            .hero-stats > div { flex: 0 0 auto; }
            .price-card { padding: 0.875rem !important; }
            .price-value { font-size: 1.5rem !important; }
        }
    </style>
</head>
<body class="antialiased text-gray-900 bg-white">
<div class="flex flex-col min-h-screen">

    {{-- Header Component (единый) --}}
    <x-header variant="landing" />

    <!-- Hero Section -->
    <section class="hero-gradient relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28 relative">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <div class="hero-badge inline-flex items-center gap-2 px-3 py-1.5 sm:px-4 sm:py-2 bg-white/10 rounded-full text-xs sm:text-sm font-medium mb-4 sm:mb-6 backdrop-blur-sm border border-white/20">
                        <span class="w-2 h-2 bg-green-400 rounded-full pulse-dot"></span>
                        7 дней бесплатно
                    </div>
                    <h1 class="hero-title text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-4 sm:mb-6">
                        Худейте вкусно<br>
                        <span class="text-green-300">без подсчёта калорий</span>
                    </h1>
                    <p class="hero-subtitle text-base sm:text-lg md:text-xl text-green-100 mb-6 sm:mb-8 max-w-lg">
                        Готовые планы питания 1200–1400 ккал с рецептами и списками покупок.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-8 sm:mb-10">
                        @auth
                            <a href="{{ route('dashboard') }}" class="hero-btn px-6 py-3 sm:px-8 sm:py-4 bg-white text-green-700 rounded-xl font-bold text-base sm:text-lg hover:bg-green-50 transition shadow-xl inline-flex items-center justify-center gap-2">
                                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                                Мой кабинет
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="hero-btn px-6 py-3 sm:px-8 sm:py-4 bg-white text-green-700 rounded-xl font-bold text-base sm:text-lg hover:bg-green-50 transition shadow-xl inline-flex items-center justify-center gap-2">
                                <i data-lucide="rocket" class="w-5 h-5"></i>
                                Попробовать бесплатно
                            </a>
                            <a href="#how-it-works" class="hero-btn px-6 py-3 sm:px-8 sm:py-4 bg-white/10 text-white rounded-xl font-semibold text-base sm:text-lg hover:bg-white/20 transition border border-white/20 inline-flex items-center justify-center gap-2">
                                <i data-lucide="play-circle" class="w-5 h-5"></i>
                                Как это работает
                            </a>
                        @endauth
                    </div>
                    <div class="hero-stats flex items-center gap-3 sm:gap-6">
                        <div class="stats-card px-3 py-2 sm:px-4 sm:py-3 rounded-xl">
                            <div class="stat-value text-xl sm:text-2xl font-bold">{{ $stats['recipes'] ?? 0 }}+</div>
                            <div class="stat-label text-green-200 text-xs sm:text-sm">рецептов</div>
                        </div>
                        <div class="stats-card px-3 py-2 sm:px-4 sm:py-3 rounded-xl">
                            <div class="stat-value text-xl sm:text-2xl font-bold">{{ $stats['menus'] ?? 0 }}+</div>
                            <div class="stat-label text-green-200 text-xs sm:text-sm">меню</div>
                        </div>
                        <div class="stats-card px-3 py-2 sm:px-4 sm:py-3 rounded-xl">
                            <div class="stat-value text-xl sm:text-2xl font-bold">{{ $stats['users'] ?? 0 }}+</div>
                            <div class="stat-label text-green-200 text-xs sm:text-sm">пользователей</div>
                        </div>
                        <div class="stats-card px-3 py-2 sm:px-4 sm:py-3 rounded-xl">
                            <div class="stat-value text-xl sm:text-2xl font-bold flex items-center gap-1">
                                <svg class="w-5 h-5 text-yellow-400 fill-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                {{ $stats['avg_rating'] ?? '4.8' }}
                            </div>
                            <div class="stat-label text-green-200 text-xs sm:text-sm">рейтинг</div>
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
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-green-600 font-semibold text-sm uppercase tracking-wider">Возможности</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3 mb-4">Всё для комфортного похудения</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Мы продумали каждую деталь, чтобы вы могли сосредоточиться на результате</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 card-hover shadow-sm border border-gray-100" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon mb-6">
                        <i data-lucide="calendar-days" class="w-7 h-7 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Меню на месяц</h3>
                    <p class="text-gray-600">Готовые планы питания на каждый день. Завтрак, обед, ужин и перекусы — всё расписано.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 card-hover shadow-sm border border-gray-100" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon mb-6">
                        <i data-lucide="calculator" class="w-7 h-7 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">КБЖУ посчитаны</h3>
                    <p class="text-gray-600">Калории, белки, жиры и углеводы рассчитаны для каждого блюда. Вам не нужно ничего считать.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 card-hover shadow-sm border border-gray-100" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon mb-6">
                        <i data-lucide="shopping-cart" class="w-7 h-7 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Списки покупок</h3>
                    <p class="text-gray-600">Автоматические списки продуктов на неделю. Скачивайте в PDF или Excel.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 card-hover shadow-sm border border-gray-100" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon mb-6">
                        <i data-lucide="chef-hat" class="w-7 h-7 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Простые рецепты</h3>
                    <p class="text-gray-600">Пошаговые инструкции с фото. Даже новичок справится за 30 минут.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 card-hover shadow-sm border border-gray-100" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon mb-6">
                        <i data-lucide="scale" class="w-7 h-7 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">1200–1400 ккал</h3>
                    <p class="text-gray-600">Оптимальный дефицит для здорового похудения. Минус 4-6 кг в месяц.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 card-hover shadow-sm border border-gray-100" data-aos="fade-up" data-aos-delay="300">
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
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-green-600 font-semibold text-sm uppercase tracking-wider">Как это работает</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3 mb-4">Начните за 3 простых шага</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8 lg:gap-12 relative">
                <!-- Соединительные линии -->
                <div class="hidden md:block absolute top-10 left-1/3 right-1/3 h-0.5 bg-green-200" style="left: calc(16.67% + 40px); right: calc(16.67% + 40px);"></div>
                
                <div class="text-center relative z-10" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl hero-gradient flex items-center justify-center shadow-lg shadow-green-500/30">
                        <i data-lucide="user-plus" class="w-10 h-10 text-white"></i>
                    </div>
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-bold mb-4">Шаг 1</span>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Зарегистрируйтесь</h3>
                    <p class="text-gray-600">Создайте аккаунт за 30 секунд. Нужен только email.</p>
                </div>
                <div class="text-center relative z-10" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl hero-gradient flex items-center justify-center shadow-lg shadow-green-500/30">
                        <i data-lucide="credit-card" class="w-10 h-10 text-white"></i>
                    </div>
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-bold mb-4">Шаг 2</span>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Выберите тариф</h3>
                    <p class="text-gray-600">7 дней бесплатно. Потом — от 1990₽ в месяц.</p>
                </div>
                <div class="text-center relative z-10" data-aos="fade-up" data-aos-delay="300">
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
    <section id="pricing" class="py-24 bg-gradient-to-b from-gray-50 to-white" x-data="{ billingPeriod: 'monthly' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-12">
                <span class="text-green-600 font-semibold text-xs sm:text-sm uppercase tracking-wider">Тарифы</span>
                <h2 class="section-title text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mt-2 sm:mt-3 mb-3 sm:mb-4">Выберите план</h2>
                <p class="section-subtitle text-base sm:text-lg lg:text-xl text-gray-600">7 дней бесплатно. Отмена в любой момент.</p>
            </div>
            
            <div class="flex justify-center mb-6 sm:mb-10">
                <div class="bg-gray-100 p-1 rounded-xl inline-flex text-sm sm:text-base">
                    <button @click="billingPeriod = 'monthly'" 
                            :class="billingPeriod === 'monthly' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                            class="px-4 py-2 sm:px-6 sm:py-2.5 rounded-lg font-semibold transition">
                        Месяц
                    </button>
                    <button @click="billingPeriod = 'yearly'" 
                            :class="billingPeriod === 'yearly' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                            class="px-4 py-2 sm:px-6 sm:py-2.5 rounded-lg font-semibold transition">
                        Год <span class="text-green-600">-25%</span>
                    </button>
                </div>
            </div>

            @php
                $trialPlan = $plans->where('slug', 'trial')->first();
                $standardMonthly = $plans->where('slug', 'standard-monthly')->first();
                $standardYearly = $plans->where('slug', 'standard-yearly')->first();
                $personalMonthly = $plans->where('slug', 'personal-monthly')->first();
                $personalYearly = $plans->where('slug', 'personal-yearly')->first();
            @endphp

            <div class="grid md:grid-cols-3 gap-6 lg:gap-8 max-w-5xl mx-auto">
                {{-- Пробный период --}}
                @if($trialPlan)
                <div class="price-card bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 lg:p-8 border border-gray-200 card-hover">
                    <div class="mb-3 sm:mb-4 lg:mb-6">
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 mb-1">Пробный</h3>
                        <p class="text-gray-500 text-xs sm:text-sm">7 дней бесплатно</p>
                    </div>
                    <div class="mb-3 sm:mb-4 lg:mb-6">
                        <span class="price-value text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900">0</span>
                        <span class="text-gray-500">₽</span>
                        <div class="text-[10px] sm:text-xs text-green-600 font-medium mt-1">Без карты</div>
                    </div>
                    @php $features = is_string($trialPlan->features) ? json_decode($trialPlan->features, true) : $trialPlan->features; @endphp
                    <ul class="feature-list space-y-1.5 sm:space-y-2 lg:space-y-3 mb-4 sm:mb-6 lg:mb-8">
                        @foreach(array_slice($features ?? [], 0, 4) as $feature)
                        <li class="flex items-start gap-2 text-gray-600 text-xs sm:text-sm">
                            <i data-lucide="check" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-green-500 flex-shrink-0 mt-0.5"></i>
                            <span>{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="price-btn block w-full py-2 sm:py-2.5 lg:py-3 px-3 sm:px-4 lg:px-6 text-center font-semibold text-xs sm:text-sm lg:text-base rounded-xl border-2 border-green-500 text-green-600 hover:bg-green-50 transition">
                        Попробовать
                    </a>
                </div>
                @endif

                {{-- Стандарт (Популярный) --}}
                @if($standardMonthly && $standardYearly)
                <div class="price-card bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 lg:p-8 border-2 border-green-500 card-hover relative shadow-xl shadow-green-500/10">
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                        <span class="bg-green-500 text-white text-[10px] sm:text-xs font-bold px-2 sm:px-3 py-0.5 sm:py-1 rounded-full">Популярный</span>
                    </div>
                    <div class="mb-3 sm:mb-4 lg:mb-6 mt-1 sm:mt-0">
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 mb-1">Стандарт</h3>
                        <p class="text-gray-500 text-xs sm:text-sm">Полный доступ</p>
                    </div>
                    <div class="mb-3 sm:mb-4 lg:mb-6">
                        <div x-show="billingPeriod === 'monthly'">
                            <span class="price-value text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900">{{ number_format($standardMonthly->price, 0, ',', ' ') }}</span>
                            <span class="text-gray-500 text-sm">₽/мес</span>
                        </div>
                        <div x-show="billingPeriod === 'yearly'">
                            <span class="price-value text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900">{{ number_format($standardYearly->price / 12, 0, ',', ' ') }}</span>
                            <span class="text-gray-500 text-xs sm:text-sm">₽/мес</span>
                            <div class="text-[10px] sm:text-xs text-green-600 font-medium mt-1">Экономия {{ number_format($standardYearly->original_price - $standardYearly->price, 0, ',', ' ') }}₽</div>
                        </div>
                    </div>
                    @php $features = is_string($standardMonthly->features) ? json_decode($standardMonthly->features, true) : $standardMonthly->features; @endphp
                    <ul class="feature-list space-y-1.5 sm:space-y-2 lg:space-y-3 mb-4 sm:mb-6 lg:mb-8">
                        @foreach(array_slice($features ?? [], 0, 5) as $feature)
                        <li class="flex items-start gap-2 text-gray-600 text-xs sm:text-sm">
                            <i data-lucide="check" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-green-500 flex-shrink-0 mt-0.5"></i>
                            <span>{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="price-btn block w-full py-2 sm:py-2.5 lg:py-3 px-3 sm:px-4 lg:px-6 text-center font-semibold text-xs sm:text-sm lg:text-base rounded-xl bg-green-500 text-white hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                        Начать бесплатно
                    </a>
                </div>
                @endif

                {{-- Индивидуальный --}}
                @if($personalMonthly && $personalYearly)
                <div class="price-card bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 lg:p-8 border border-gray-200 card-hover">
                    <div class="mb-3 sm:mb-4 lg:mb-6">
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 mb-1">Индивидуальный</h3>
                        <p class="text-gray-500 text-xs sm:text-sm">Персональный подход</p>
                    </div>
                    <div class="mb-3 sm:mb-4 lg:mb-6">
                        <div x-show="billingPeriod === 'monthly'">
                            <span class="price-value text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900">{{ number_format($personalMonthly->price, 0, ',', ' ') }}</span>
                            <span class="text-gray-500 text-xs sm:text-sm">₽/мес</span>
                        </div>
                        <div x-show="billingPeriod === 'yearly'">
                            <span class="price-value text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900">{{ number_format($personalYearly->price / 12, 0, ',', ' ') }}</span>
                            <span class="text-gray-500 text-xs sm:text-sm">₽/мес</span>
                            <div class="text-[10px] sm:text-xs text-green-600 font-medium mt-1">Экономия {{ number_format($personalYearly->original_price - $personalYearly->price, 0, ',', ' ') }}₽</div>
                        </div>
                    </div>
                    @php $features = is_string($personalMonthly->features) ? json_decode($personalMonthly->features, true) : $personalMonthly->features; @endphp
                    <ul class="feature-list space-y-1.5 sm:space-y-2 lg:space-y-3 mb-4 sm:mb-6 lg:mb-8">
                        @foreach(array_slice($features ?? [], 0, 4) as $feature)
                        <li class="flex items-start gap-2 text-gray-600 text-xs sm:text-sm">
                            <i data-lucide="check" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-green-500 flex-shrink-0 mt-0.5"></i>
                            <span>{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="price-btn block w-full py-2 sm:py-2.5 lg:py-3 px-3 sm:px-4 lg:px-6 text-center font-semibold text-xs sm:text-sm lg:text-base rounded-xl border-2 border-green-500 text-green-600 hover:bg-green-50 transition">
                        Связаться
                    </a>
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Success Stories -->
    <section class="py-16 sm:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10 sm:mb-16" data-aos="fade-up">
                <span class="text-green-600 font-semibold text-xs sm:text-sm uppercase tracking-wider">Результаты</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mt-2 sm:mt-3 mb-4">Истории успеха наших пользователей</h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">Реальные люди, реальные результаты. Посмотрите, чего добились наши подписчики.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 lg:gap-8">
                {{-- История 1 --}}
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 sm:p-8 border border-green-100 card-hover" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-14 h-14 rounded-full bg-green-200 flex items-center justify-center text-green-700 font-bold text-lg">АК</div>
                        <div>
                            <div class="font-bold text-gray-900">Анна Козлова</div>
                            <div class="text-sm text-gray-500">32 года, Москва</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 mb-5">
                        <div class="flex-1 text-center p-3 bg-white rounded-xl">
                            <div class="text-xs text-gray-400 mb-1">Было</div>
                            <div class="text-xl font-bold text-gray-400">78 кг</div>
                        </div>
                        <div class="flex-shrink-0">
                            <i data-lucide="arrow-right" class="w-5 h-5 text-green-500"></i>
                        </div>
                        <div class="flex-1 text-center p-3 bg-green-500 text-white rounded-xl">
                            <div class="text-xs opacity-80 mb-1">Стало</div>
                            <div class="text-xl font-bold">66 кг</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mb-4 px-3 py-2 bg-green-100 rounded-lg">
                        <i data-lucide="trending-down" class="w-4 h-4 text-green-600"></i>
                        <span class="text-sm font-semibold text-green-700">-12 кг за 3 месяца</span>
                    </div>
                    <p class="text-gray-600 text-sm">"Рецепты простые и вкусные. Не чувствуешь, что на диете! Семья тоже с удовольствием ест."</p>
                </div>

                {{-- История 2 --}}
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 sm:p-8 border border-blue-100 card-hover" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-14 h-14 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold text-lg">ДН</div>
                        <div>
                            <div class="font-bold text-gray-900">Дмитрий Новиков</div>
                            <div class="text-sm text-gray-500">28 лет, Екатеринбург</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 mb-5">
                        <div class="flex-1 text-center p-3 bg-white rounded-xl">
                            <div class="text-xs text-gray-400 mb-1">Было</div>
                            <div class="text-xl font-bold text-gray-400">95 кг</div>
                        </div>
                        <div class="flex-shrink-0">
                            <i data-lucide="arrow-right" class="w-5 h-5 text-blue-500"></i>
                        </div>
                        <div class="flex-1 text-center p-3 bg-blue-500 text-white rounded-xl">
                            <div class="text-xs opacity-80 mb-1">Стало</div>
                            <div class="text-xl font-bold">79 кг</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mb-4 px-3 py-2 bg-blue-100 rounded-lg">
                        <i data-lucide="trending-down" class="w-4 h-4 text-blue-600"></i>
                        <span class="text-sm font-semibold text-blue-700">-16 кг за 5 месяцев</span>
                    </div>
                    <p class="text-gray-600 text-sm">"Списки покупок — это гениально. Экономлю время и деньги. Готовлю за 30 минут."</p>
                </div>

                {{-- История 3 --}}
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 sm:p-8 border border-purple-100 card-hover" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-14 h-14 rounded-full bg-purple-200 flex items-center justify-center text-purple-700 font-bold text-lg">МС</div>
                        <div>
                            <div class="font-bold text-gray-900">Мария Смирнова</div>
                            <div class="text-sm text-gray-500">35 лет, Казань</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 mb-5">
                        <div class="flex-1 text-center p-3 bg-white rounded-xl">
                            <div class="text-xs text-gray-400 mb-1">Было</div>
                            <div class="text-xl font-bold text-gray-400">72 кг</div>
                        </div>
                        <div class="flex-shrink-0">
                            <i data-lucide="arrow-right" class="w-5 h-5 text-purple-500"></i>
                        </div>
                        <div class="flex-1 text-center p-3 bg-purple-500 text-white rounded-xl">
                            <div class="text-xs opacity-80 mb-1">Стало</div>
                            <div class="text-xl font-bold">64 кг</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mb-4 px-3 py-2 bg-purple-100 rounded-lg">
                        <i data-lucide="trending-down" class="w-4 h-4 text-purple-600"></i>
                        <span class="text-sm font-semibold text-purple-700">-8 кг за 2 месяца</span>
                    </div>
                    <p class="text-gray-600 text-sm">"После родов не могла вернуться в форму 2 года. С RawPlan получилось за 2 месяца!"</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16 sm:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10 sm:mb-16" data-aos="fade-up">
                <span class="text-green-600 font-semibold text-xs sm:text-sm uppercase tracking-wider">Отзывы</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mt-2 sm:mt-3 mb-4">Что говорят пользователи</h2>
                <div class="flex items-center justify-center gap-2 mt-3">
                    <div class="flex items-center gap-0.5">
                        @for($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 text-yellow-400 fill-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @endfor
                    </div>
                    <span class="text-gray-700 font-semibold">{{ $stats['avg_rating'] ?? '4.8' }}</span>
                    <span class="text-gray-400 text-sm">средняя оценка</span>
                </div>
            </div>
            <div class="grid md:grid-cols-3 gap-6 lg:gap-8">
                <div class="bg-white rounded-2xl p-5 sm:p-8 card-hover shadow-sm" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-400 fill-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-700 mb-4 sm:mb-6 text-base sm:text-lg leading-relaxed">"За 3 месяца похудела на 12 кг! Рецепты простые и вкусные. Муж даже не заметил, что это диетическое питание."</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-green-100 flex items-center justify-center">
                                <span class="text-green-600 font-bold text-sm">АК</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 text-sm sm:text-base">Анна Козлова</div>
                                <div class="text-xs sm:text-sm text-gray-500">Москва</div>
                            </div>
                        </div>
                        <div class="text-xs text-green-600 font-semibold bg-green-50 px-2 py-1 rounded-full">-12 кг</div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 sm:p-8 card-hover shadow-sm" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center gap-1 mb-3 sm:mb-4">
                        @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-400 fill-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-700 mb-4 sm:mb-6 text-base sm:text-lg leading-relaxed">"Как программист, я не умел готовить. С RawPlan всё расписано по шагам — ошибиться невозможно. Плюс экономлю на доставке еды."</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-blue-600 font-bold text-sm">ИП</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 text-sm sm:text-base">Игорь Петров</div>
                                <div class="text-xs sm:text-sm text-gray-500">Санкт-Петербург</div>
                            </div>
                        </div>
                        <div class="text-xs text-blue-600 font-semibold bg-blue-50 px-2 py-1 rounded-full">6 мес</div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 sm:p-8 card-hover shadow-sm" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center gap-1 mb-3 sm:mb-4">
                        @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-400 fill-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-700 mb-4 sm:mb-6 text-base sm:text-lg leading-relaxed">"Минус 8 кг и отличное самочувствие! Энергии стало больше, одежда снова по размеру. Подписку продлила на год."</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-purple-100 flex items-center justify-center">
                                <span class="text-purple-600 font-bold text-sm">МС</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 text-sm sm:text-base">Мария Смирнова</div>
                                <div class="text-xs sm:text-sm text-gray-500">Казань</div>
                            </div>
                        </div>
                        <div class="text-xs text-purple-600 font-semibold bg-purple-50 px-2 py-1 rounded-full">-8 кг</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-16 sm:py-24 bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-10 lg:mb-16">
                <span class="text-green-600 font-semibold text-xs sm:text-sm uppercase tracking-wider">FAQ</span>
                <h2 class="section-title text-xl sm:text-2xl lg:text-4xl font-bold text-gray-900 mt-2 sm:mt-3 mb-4">Частые вопросы</h2>
            </div>
            <div class="space-y-2 sm:space-y-3 lg:space-y-4">
                <details class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 lg:p-6 group" open>
                    <summary class="flex items-center justify-between cursor-pointer list-none gap-2">
                        <span class="faq-question font-semibold text-gray-900 text-sm sm:text-base lg:text-lg">Есть ли пробный период?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="faq-answer mt-2 sm:mt-3 lg:mt-4 text-gray-600 text-xs sm:text-sm lg:text-base">Да! 7 дней бесплатно. Полный доступ.</p>
                </details>
                <details class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 lg:p-6 group">
                    <summary class="flex items-center justify-between cursor-pointer list-none gap-2">
                        <span class="faq-question font-semibold text-gray-900 text-sm sm:text-base lg:text-lg">Когда я получу меню?</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="faq-answer mt-2 sm:mt-3 lg:mt-4 text-gray-600 text-xs sm:text-sm lg:text-base">Сразу после регистрации! Меню доступно в личном кабинете.</p>
                </details>
                <details class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 lg:p-6 group">
                    <summary class="flex items-center justify-between cursor-pointer list-none gap-2">
                        <span class="faq-question font-semibold text-gray-900 text-sm sm:text-base lg:text-lg">Можно отменить?</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0"></i>
                    </summary>
                    <p class="faq-answer mt-2 sm:mt-3 lg:mt-4 text-gray-600 text-xs sm:text-sm lg:text-base">Да, в любой момент. Без скрытых условий.</p>
                </details>
                <details class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 lg:p-6 group">
                    <summary class="flex items-center justify-between cursor-pointer list-none gap-2">
                        <span class="faq-question font-semibold text-gray-900 text-sm sm:text-base lg:text-lg">Вегетарианцам?</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0"></i>
                    </summary>
                    <p class="faq-answer mt-2 sm:mt-3 lg:mt-4 text-gray-600 text-xs sm:text-sm lg:text-base">В индивидуальном тарифе учтём все.</p>
                </details>
                <details class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 lg:p-6 group">
                    <summary class="flex items-center justify-between cursor-pointer list-none gap-2">
                        <span class="faq-question font-semibold text-gray-900 text-sm sm:text-base lg:text-lg">Способы оплаты?</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0"></i>
                    </summary>
                    <p class="faq-answer mt-2 sm:mt-3 lg:mt-4 text-gray-600 text-xs sm:text-sm lg:text-base">Visa, MC, МИР, ЮKassa, CloudPayments.</p>
                </details>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 sm:py-24 hero-gradient relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 right-10 sm:right-20 w-40 sm:w-64 h-40 sm:h-64 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 left-10 sm:left-20 w-48 sm:w-80 h-48 sm:h-80 bg-white rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
            <h2 class="text-2xl sm:text-3xl lg:text-5xl font-bold text-white mb-4 sm:mb-6">Готовы изменить жизнь?</h2>
            <p class="text-base sm:text-xl text-green-100 mb-8 sm:mb-10 max-w-2xl mx-auto">Присоединяйтесь к 10 000+ пользователей RawPlan</p>
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 sm:px-10 py-3 sm:py-4 bg-white text-green-700 rounded-xl font-bold text-base sm:text-lg hover:bg-green-50 transition shadow-xl">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span class="hidden sm:inline">Перейти в кабинет</span>
                    <span class="sm:hidden">Мой кабинет</span>
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 sm:px-10 py-3 sm:py-4 bg-white text-green-700 rounded-xl font-bold text-base sm:text-lg hover:bg-green-50 transition shadow-xl">
                    <i data-lucide="rocket" class="w-5 h-5"></i>
                    <span class="hidden sm:inline">Начать бесплатно — 7 дней</span>
                    <span class="sm:hidden">Начать бесплатно</span>
                </a>
            @endauth
        </div>
    </section>

    {{-- Footer Component (единый) --}}
    <x-footer />
    
    {{-- Scroll to Top Button --}}
    <x-scroll-to-top />
    
    {{-- Cookie Consent --}}
    <x-cookie-consent />
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    lucide.createIcons();
    
    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true,
        offset: 50
    });
    
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
<!-- Alpine.js (загружается после Lucide для корректной работы x-data/x-show) -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>

</body>
</html>

