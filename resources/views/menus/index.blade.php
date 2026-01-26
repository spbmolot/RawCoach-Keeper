<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Меню | RawPlan</title>
    <meta name="description" content="Готовые планы питания на месяц. Меню 1200-1400 ккал для похудения." />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .hero-gradient { background: linear-gradient(135deg, #065f46 0%, #047857 50%, #10b981 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }
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
                    <a href="{{ route('menus.index') }}" class="text-green-600 font-medium">Меню</a>
                    <a href="{{ route('home') }}#pricing" class="text-gray-600 hover:text-green-600 font-medium transition">Тарифы</a>
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
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Меню на месяц</h1>
            <p class="text-xl text-green-100 max-w-2xl mx-auto">Готовые планы питания с рецептами и списками покупок</p>
        </div>
    </section>

    <!-- Menus Grid -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($menus->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($menus as $menu)
                    <a href="{{ route('menus.show', $menu) }}" class="bg-white rounded-2xl shadow-sm overflow-hidden card-hover group">
                        @if($menu->cover_image)
                            <img src="{{ Storage::url($menu->cover_image) }}" alt="{{ $menu->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <i data-lucide="calendar" class="w-12 h-12 mx-auto mb-2"></i>
                                    <div class="text-3xl font-bold">{{ $menu->getMonthName() ?? 'Меню' }}</div>
                                    <div class="text-lg opacity-80">{{ $menu->year }}</div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-green-600 transition">{{ $menu->title }}</h3>
                            
                            <p class="text-gray-500 mb-4 line-clamp-2">{{ $menu->description }}</p>
                            
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="calendar-days" class="w-4 h-4"></i>
                                        {{ $menu->days->count() }} дней
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="flame" class="w-4 h-4 text-orange-500"></i>
                                        ~{{ $menu->total_calories ?? 1300 }} ккал/день
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <i data-lucide="calendar-x" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Меню пока нет</h3>
                <p class="text-gray-500">Скоро здесь появятся новые планы питания</p>
            </div>
        @endif
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
