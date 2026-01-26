<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Список покупок | RawPlan</title>
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
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-green-600 font-medium transition">Кабинет</a>
                </nav>
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                    Мой кабинет
                </a>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <section class="hero-gradient py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">Список покупок</h1>
            <p class="text-lg text-green-100">Выберите меню и период для генерации списка</p>
        </div>
    </section>

    <!-- Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($menus->count() > 0)
            <div class="space-y-6">
                @foreach($menus as $menu)
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $menu->title }}</h3>
                                <p class="text-gray-500">{{ $menu->days->count() }} дней</p>
                            </div>
                            <i data-lucide="calendar" class="w-8 h-8 text-green-500"></i>
                        </div>
                        
                        <form action="{{ route('shopping-list.show', $menu) }}" method="GET" class="flex flex-wrap items-end gap-4">
                            <div class="flex-1 min-w-[120px]">
                                <label class="block text-sm font-medium text-gray-700 mb-1">С дня</label>
                                <select name="start_day" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                                    @for($i = 1; $i <= $menu->days->count(); $i++)
                                        <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>День {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="flex-1 min-w-[120px]">
                                <label class="block text-sm font-medium text-gray-700 mb-1">По день</label>
                                <select name="end_day" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                                    @for($i = 1; $i <= $menu->days->count(); $i++)
                                        <option value="{{ $i }}" {{ $i == 7 ? 'selected' : '' }}>День {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <button type="submit" class="px-6 py-2.5 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition flex items-center gap-2">
                                <i data-lucide="list" class="w-5 h-5"></i>
                                Сформировать
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <i data-lucide="shopping-cart" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Нет доступных меню</h3>
                <p class="text-gray-500">Меню пока не опубликованы</p>
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
