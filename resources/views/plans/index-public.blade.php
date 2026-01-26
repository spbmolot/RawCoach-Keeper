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
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($plans as $plan)
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden {{ $plan->type === 'yearly' ? 'ring-2 ring-green-500' : '' }}">
                    @if($plan->type === 'yearly')
                        <div class="bg-green-500 text-white text-center py-2 text-sm font-semibold">
                            ПОПУЛЯРНЫЙ
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                        <p class="text-gray-500 text-sm mb-4">{{ $plan->description }}</p>
                        
                        <div class="mb-6">
                            <span class="text-3xl font-bold text-gray-900">{{ number_format($plan->price, 0, ',', ' ') }} ₽</span>
                            @if($plan->original_price && $plan->original_price > $plan->price)
                                <span class="text-sm text-gray-400 line-through ml-2">{{ number_format($plan->original_price, 0, ',', ' ') }} ₽</span>
                                @php
                                    $discount = round((1 - $plan->price / $plan->original_price) * 100);
                                @endphp
                                <div class="text-green-600 text-sm font-semibold">Экономия {{ $discount }}%</div>
                            @endif
                            <div class="text-sm text-gray-500">
                                @if($plan->type === 'trial')
                                    7 дней бесплатно
                                @elseif($plan->type === 'monthly')
                                    за {{ $plan->duration_days }} дней
                                @elseif($plan->type === 'yearly')
                                    за год
                                @else
                                    индивидуально
                                @endif
                            </div>
                        </div>
                        
                        @php
                            $features = is_string($plan->features) ? json_decode($plan->features, true) : $plan->features;
                        @endphp
                        
                        @if($features && is_array($features))
                            <ul class="space-y-2 mb-6 text-sm">
                                @foreach(array_slice($features, 0, 4) as $feature)
                                    <li class="flex items-start gap-2">
                                        <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0"></i>
                                        <span class="text-gray-600">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        
                        @auth
                            @if($currentSubscription && $currentSubscription->plan_id === $plan->id)
                                <button class="w-full py-3 px-4 bg-green-100 text-green-700 rounded-xl font-semibold cursor-not-allowed">
                                    Активная подписка
                                </button>
                            @else
                                <form action="{{ route('subscriptions.create', $plan) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full py-3 px-4 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition">
                                        Выбрать план
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('register') }}" class="block w-full py-3 px-4 bg-green-500 text-white rounded-xl font-semibold text-center hover:bg-green-600 transition">
                                Начать сейчас
                            </a>
                        @endauth
                    </div>
                </div>
            @endforeach
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
