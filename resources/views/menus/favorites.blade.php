<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <div class="mb-6 sm:mb-8">
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('menus.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">Меню</a>
                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                <span class="text-sm text-gray-900 font-medium">Избранное</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Избранные меню</h1>
            <p class="text-gray-600 mt-1 text-sm">Меню, которые вы добавили в избранное</p>
        </div>

        @if($favoriteMenus->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($favoriteMenus as $menu)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-red-200 group relative">
                        <button onclick="toggleFavorite({{ $menu->id }}, this)" class="absolute top-3 right-3 z-10 w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-md hover:bg-red-50 transition">
                            <i data-lucide="heart" class="w-4 h-4 text-red-500 fill-red-500"></i>
                        </button>
                        <a href="{{ route('menus.show', $menu) }}">
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
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $favoriteMenus->links() }}</div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12 text-center">
                <div class="w-20 h-20 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="heart" class="w-10 h-10 text-red-400"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Нет избранных меню</h2>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">Нажмите на сердечко на любом меню, чтобы добавить его в избранное</p>
                <a href="{{ route('menus.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition">
                    <i data-lucide="book-open" class="w-5 h-5"></i> Перейти к меню
                </a>
            </div>
        @endif
    </div>

    <script>
        function toggleFavorite(menuId, btn) {
            fetch(`/menus/${menuId}/favorite`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
            })
            .then(r => r.json())
            .then(data => {
                if (data.success && !data.is_favorite) {
                    btn.closest('.group').remove();
                }
            });
        }
    </script>
</x-app-layout>
