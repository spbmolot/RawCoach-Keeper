<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <div class="mb-6 sm:mb-8">
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('menus.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">Меню</a>
                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                <span class="text-sm text-gray-900 font-medium">Поиск</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Поиск по меню</h1>
        </div>

        {{-- Форма поиска --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
            <form method="GET" action="{{ route('menus.search') }}" class="flex gap-3">
                <div class="flex-1 relative">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Поиск по названию или описанию..." class="w-full pl-10 rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required minlength="2">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-medium transition">
                    Найти
                </button>
            </form>
        </div>

        @if(isset($query))
            <p class="text-gray-600 text-sm mb-4">
                Результаты по запросу «<strong>{{ $query }}</strong>»: {{ $menus->total() }}
            </p>
        @endif

        @if($menus->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($menus as $menu)
                    <a href="{{ route('menus.show', $menu) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-green-200 group">
                        @if($menu->cover_image)
                            <img src="{{ Storage::url($menu->cover_image) }}" alt="{{ $menu->title }}" class="w-full h-40 object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-40 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <i data-lucide="calendar" class="w-10 h-10 mx-auto mb-1"></i>
                                    <div class="text-2xl font-bold">{{ $menu->getMonthName() ?? 'Меню' }}</div>
                                    <div class="text-sm opacity-80">{{ $menu->year }}</div>
                                </div>
                            </div>
                        @endif
                        <div class="p-4 sm:p-5">
                            <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-green-600 transition">{{ $menu->title }}</h3>
                            @if($menu->description)
                                <p class="text-gray-500 text-sm line-clamp-2 mb-3">{{ $menu->description }}</p>
                            @endif
                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                <span class="flex items-center gap-1">
                                    <i data-lucide="calendar-days" class="w-3.5 h-3.5"></i>
                                    {{ $menu->days->count() }} дней
                                </span>
                                <span class="flex items-center gap-1">
                                    <i data-lucide="flame" class="w-3.5 h-3.5 text-orange-500"></i>
                                    ~{{ $menu->total_calories ?? 1300 }} ккал
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">{{ $menus->withQueryString()->links() }}</div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="search-x" class="w-10 h-10 text-gray-400"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Ничего не найдено</h2>
                <p class="text-gray-600 max-w-md mx-auto">Попробуйте изменить поисковый запрос</p>
            </div>
        @endif
    </div>
</x-app-layout>
