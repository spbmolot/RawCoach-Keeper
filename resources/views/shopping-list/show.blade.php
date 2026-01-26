<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Список покупок - {{ $menu->title }} | RawPlan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .hero-gradient { background: linear-gradient(135deg, #065f46 0%, #047857 50%, #10b981 100%); }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>
<body class="antialiased text-gray-900 bg-gray-50">

    <!-- Header -->
    <header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 shadow-sm no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl hero-gradient flex items-center justify-center">
                        <i data-lucide="salad" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">RawPlan</span>
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('shopping-list.index') }}" class="px-4 py-2 text-gray-600 hover:text-green-600 font-medium transition flex items-center gap-2">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        Назад
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Список покупок</h1>
                    <p class="text-gray-500">{{ $menu->title }} • Дни {{ $startDay }}-{{ $endDay }}</p>
                </div>
                <div class="flex gap-3 no-print">
                    <button onclick="window.print()" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition flex items-center gap-2">
                        <i data-lucide="printer" class="w-5 h-5"></i>
                        Печать
                    </button>
                    <a href="{{ route('shopping-list.pdf', ['menu' => $menu, 'start_day' => $startDay, 'end_day' => $endDay]) }}" class="px-4 py-2.5 bg-red-500 text-white rounded-xl font-semibold hover:bg-red-600 transition flex items-center gap-2">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                        Скачать PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Shopping List -->
        @if(count($shoppingList) > 0)
            <div class="space-y-6">
                @foreach($shoppingList as $category => $items)
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                        <div class="bg-green-50 px-6 py-4 border-b border-green-100">
                            <h2 class="text-lg font-bold text-green-800 flex items-center gap-2">
                                <i data-lucide="tag" class="w-5 h-5"></i>
                                {{ $category }}
                            </h2>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            @foreach($items as $item)
                                <li class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-green-500 focus:ring-green-500">
                                        <span class="text-gray-900">{{ $item['name'] }}</span>
                                    </div>
                                    <span class="text-gray-600 font-medium">{{ round($item['amount'], 1) }} {{ $item['unit'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                <i data-lucide="shopping-cart" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Список пуст</h3>
                <p class="text-gray-500">В выбранном периоде нет рецептов с ингредиентами</p>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm">© {{ date('Y') }} RawPlan. Все права защищены.</p>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
