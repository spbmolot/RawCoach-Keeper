<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Меню на сегодня</h1>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <i data-lucide="calendar-x" class="w-10 h-10 text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Меню на сегодня не найдено</h3>

            @if(empty($hasSubscription))
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Оформите подписку, чтобы получить доступ к ежедневному меню с рецептами, списками покупок и подсчётом БЖУ
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('plans.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                        <i data-lucide="sparkles" class="w-5 h-5"></i>
                        Оформить подписку
                    </a>
                    <a href="{{ route('recipes.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition">
                        <i data-lucide="chef-hat" class="w-5 h-5"></i>
                        Бесплатные рецепты
                    </a>
                </div>
            @else
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Возможно, меню на этот день ещё не опубликовано
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('menus.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-500 text-white rounded-xl font-medium hover:bg-green-600 transition">
                        <i data-lucide="book-open" class="w-5 h-5"></i>
                        Все меню
                    </a>
                    <a href="{{ route('dashboard.week') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition">
                        <i data-lucide="calendar-days" class="w-5 h-5"></i>
                        Меню на неделю
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
