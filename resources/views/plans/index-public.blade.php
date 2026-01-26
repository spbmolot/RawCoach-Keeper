<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Тарифы | RawPlan</title>
    <meta name="description" content="Выберите план питания для похудения. Стандарт, Премиум или Индивидуальный план." />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .hero-gradient { background: linear-gradient(135deg, #065f46 0%, #047857 50%, #10b981 100%); }
    </style>
</head>
<body class="antialiased text-gray-900 bg-gray-50">

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
                    <a href="{{ route('recipes.index') }}" class="text-gray-600 hover:text-green-600 font-medium transition">Рецепты</a>
                    <a href="{{ route('menus.index') }}" class="text-gray-600 hover:text-green-600 font-medium transition">Меню</a>
                    <a href="{{ route('plans.index') }}" class="text-green-600 font-medium">Тарифы</a>
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

    <!-- Page Header -->
    <section class="hero-gradient py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Выберите свой план</h1>
            <p class="text-xl text-green-100 max-w-2xl mx-auto">Готовые планы питания для похудения с подробными рецептами</p>
        </div>
    </section>

    <!-- Plans Grid -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ billingPeriod: 'monthly' }">
        
        <!-- Переключатель месяц/год -->
        <div class="flex justify-center mb-10">
            <div class="bg-white rounded-xl p-1 shadow-sm inline-flex">
                <button @click="billingPeriod = 'monthly'" 
                        :class="billingPeriod === 'monthly' ? 'bg-green-500 text-white' : 'text-gray-600 hover:text-gray-900'"
                        class="px-6 py-2 rounded-lg font-semibold transition">
                    Ежемесячно
                </button>
                <button @click="billingPeriod = 'yearly'" 
                        :class="billingPeriod === 'yearly' ? 'bg-green-500 text-white' : 'text-gray-600 hover:text-gray-900'"
                        class="px-6 py-2 rounded-lg font-semibold transition flex items-center gap-2">
                    Ежегодно
                    <span class="bg-amber-400 text-amber-900 text-xs px-2 py-0.5 rounded-full font-bold">-25%</span>
                </button>
            </div>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            
            <!-- Пробный период -->
            @php $trialPlan = $plans->where('slug', 'trial')->first(); @endphp
            @if($trialPlan)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border-2 border-gray-100">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $trialPlan->name }}</h3>
                    <p class="text-gray-500 text-sm mb-4">{{ $trialPlan->description }}</p>
                    
                    <div class="mb-6">
                        <span class="text-3xl font-bold text-gray-900">Бесплатно</span>
                        <div class="text-sm text-gray-500">7 дней</div>
                    </div>
                    
                    @php
                        $features = is_string($trialPlan->features) ? json_decode($trialPlan->features, true) : $trialPlan->features;
                    @endphp
                    
                    @if($features && is_array($features))
                        <ul class="space-y-2 mb-6 text-sm">
                            @foreach($features as $feature)
                                <li class="flex items-start gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0"></i>
                                    <span class="text-gray-600">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    
                    <a href="{{ route('register') }}" class="block w-full py-3 px-4 bg-gray-100 text-gray-700 rounded-xl font-semibold text-center hover:bg-gray-200 transition">
                        Попробовать бесплатно
                    </a>
                </div>
            </div>
            @endif
            
            <!-- Стандарт -->
            @php 
                $standardMonthly = $plans->where('slug', 'standard-monthly')->first();
                $standardYearly = $plans->where('slug', 'standard-yearly')->first();
            @endphp
            @if($standardMonthly)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border-2 border-gray-100">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Стандарт</h3>
                    <p class="text-gray-500 text-sm mb-4">Доступ к планам питания</p>
                    
                    <div class="mb-6">
                        <template x-if="billingPeriod === 'monthly'">
                            <div>
                                <span class="text-3xl font-bold text-gray-900">{{ number_format($standardMonthly->price, 0, ',', ' ') }} ₽</span>
                                <div class="text-sm text-gray-500">в месяц</div>
                            </div>
                        </template>
                        <template x-if="billingPeriod === 'yearly'">
                            <div>
                                <span class="text-3xl font-bold text-gray-900">{{ number_format($standardYearly->price / 12, 0, ',', ' ') }} ₽</span>
                                <span class="text-sm text-gray-400 line-through ml-2">{{ number_format($standardMonthly->price, 0, ',', ' ') }} ₽</span>
                                <div class="text-sm text-gray-500">в месяц, оплата за год</div>
                                <div class="text-green-600 text-sm font-semibold">Экономия 25% ({{ number_format($standardYearly->original_price - $standardYearly->price, 0, ',', ' ') }} ₽)</div>
                            </div>
                        </template>
                    </div>
                    
                    @php
                        $features = is_string($standardMonthly->features) ? json_decode($standardMonthly->features, true) : $standardMonthly->features;
                    @endphp
                    
                    @if($features && is_array($features))
                        <ul class="space-y-2 mb-6 text-sm">
                            @foreach(array_slice($features, 0, 5) as $feature)
                                <li class="flex items-start gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0"></i>
                                    <span class="text-gray-600">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    
                    @auth
                        <template x-if="billingPeriod === 'monthly'">
                            <form action="{{ route('subscriptions.create', $standardMonthly) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-3 px-4 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition">
                                    Выбрать план
                                </button>
                            </form>
                        </template>
                        <template x-if="billingPeriod === 'yearly'">
                            <form action="{{ route('subscriptions.create', $standardYearly) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-3 px-4 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition">
                                    Выбрать план
                                </button>
                            </form>
                        </template>
                    @else
                        <a href="{{ route('register') }}" class="block w-full py-3 px-4 bg-green-500 text-white rounded-xl font-semibold text-center hover:bg-green-600 transition">
                            Начать сейчас
                        </a>
                    @endauth
                </div>
            </div>
            @endif
            
            <!-- Индивидуальный -->
            @php 
                $personalMonthly = $plans->where('slug', 'personal-monthly')->first();
                $personalYearly = $plans->where('slug', 'personal-yearly')->first();
            @endphp
            @if($personalMonthly)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden ring-2 ring-green-500">
                <div class="bg-green-500 text-white text-center py-2 text-sm font-semibold">
                    РЕКОМЕНДУЕМ
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Индивидуальный</h3>
                    <p class="text-gray-500 text-sm mb-4">Персональный план питания</p>
                    
                    <div class="mb-6">
                        <template x-if="billingPeriod === 'monthly'">
                            <div>
                                <span class="text-3xl font-bold text-gray-900">{{ number_format($personalMonthly->price, 0, ',', ' ') }} ₽</span>
                                <div class="text-sm text-gray-500">в месяц</div>
                            </div>
                        </template>
                        <template x-if="billingPeriod === 'yearly'">
                            <div>
                                <span class="text-3xl font-bold text-gray-900">{{ number_format($personalYearly->price / 12, 0, ',', ' ') }} ₽</span>
                                <span class="text-sm text-gray-400 line-through ml-2">{{ number_format($personalMonthly->price, 0, ',', ' ') }} ₽</span>
                                <div class="text-sm text-gray-500">в месяц, оплата за год</div>
                                <div class="text-green-600 text-sm font-semibold">Экономия 25% ({{ number_format($personalYearly->original_price - $personalYearly->price, 0, ',', ' ') }} ₽)</div>
                            </div>
                        </template>
                    </div>
                    
                    @php
                        $features = is_string($personalMonthly->features) ? json_decode($personalMonthly->features, true) : $personalMonthly->features;
                    @endphp
                    
                    @if($features && is_array($features))
                        <ul class="space-y-2 mb-6 text-sm">
                            @foreach(array_slice($features, 0, 5) as $feature)
                                <li class="flex items-start gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0"></i>
                                    <span class="text-gray-600">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    
                    @auth
                        <template x-if="billingPeriod === 'monthly'">
                            <form action="{{ route('subscriptions.create', $personalMonthly) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-3 px-4 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition">
                                    Выбрать план
                                </button>
                            </form>
                        </template>
                        <template x-if="billingPeriod === 'yearly'">
                            <form action="{{ route('subscriptions.create', $personalYearly) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-3 px-4 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition">
                                    Выбрать план
                                </button>
                            </form>
                        </template>
                    @else
                        <a href="{{ route('register') }}" class="block w-full py-3 px-4 bg-green-500 text-white rounded-xl font-semibold text-center hover:bg-green-600 transition">
                            Начать сейчас
                        </a>
                    @endauth
                </div>
            </div>
            @endif
            
        </div>
        
        <!-- FAQ -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Часто задаваемые вопросы</h2>
            <div class="max-w-3xl mx-auto space-y-4">
                <div class="bg-white rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Могу ли я отменить подписку?</h3>
                    <p class="text-gray-600">Да, вы можете отменить подписку в любой момент. Доступ сохранится до конца оплаченного периода.</p>
                </div>
                <div class="bg-white rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Есть ли пробный период?</h3>
                    <p class="text-gray-600">Да, мы предлагаем 7 дней бесплатного доступа для новых пользователей.</p>
                </div>
                <div class="bg-white rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Какие способы оплаты доступны?</h3>
                    <p class="text-gray-600">Мы принимаем банковские карты Visa, MasterCard, МИР через YooKassa.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm">© {{ date('Y') }} RawPlan. Все права защищены.</p>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
