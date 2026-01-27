<x-app-layout>
    <div class="max-w-7xl mx-auto">
        {{-- Заголовок --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Меню на неделю</h1>
                </div>
                <p class="text-gray-600">Планируйте питание на 7 дней вперёд</p>
            </div>
        </div>

        {{-- Навигация по неделям --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard.week', ['date' => $startDate->copy()->subWeek()->format('Y-m-d')]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                    <span class="hidden sm:inline">Предыдущая</span>
                </a>
                <div class="text-center">
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ $startDate->format('d') }} — {{ $startDate->copy()->addDays(6)->format('d M Y') }}
                    </h2>
                </div>
                <a href="{{ route('dashboard.week', ['date' => $startDate->copy()->addWeek()->format('Y-m-d')]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition">
                    <span class="hidden sm:inline">Следующая</span>
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </a>
            </div>
        </div>

        {{-- Дни недели --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-4">
            @foreach($weekDays as $day)
                @php
                    $isToday = $day['date']->isToday();
                    $isPast = $day['date']->isPast() && !$isToday;
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border {{ $isToday ? 'border-green-400 ring-2 ring-green-400/20' : 'border-gray-100' }} overflow-hidden {{ $isPast ? 'opacity-60' : '' }}">
                    {{-- Заголовок дня --}}
                    <div class="p-4 {{ $isToday ? 'bg-green-50' : 'bg-gray-50' }} border-b border-gray-100">
                        <div class="text-center">
                            <div class="text-xs uppercase tracking-wide {{ $isToday ? 'text-green-600 font-semibold' : 'text-gray-500' }}">
                                {{ $day['date']->translatedFormat('D') }}
                            </div>
                            <div class="text-2xl font-bold {{ $isToday ? 'text-green-600' : 'text-gray-900' }}">
                                {{ $day['date']->format('d') }}
                            </div>
                            @if($isToday)
                                <span class="inline-block mt-1 px-2 py-0.5 bg-green-500 text-white text-xs rounded-full font-medium">
                                    Сегодня
                                </span>
                            @endif
                        </div>
                    </div>
                    {{-- Контент --}}
                    <div class="p-4">
                        @if($day['menu'])
                            <div class="text-center">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="utensils" class="w-6 h-6 text-green-600"></i>
                                </div>
                                <div class="text-sm font-medium text-gray-900">{{ $day['recipes_count'] }}</div>
                                <div class="text-xs text-gray-500">{{ trans_choice('рецепт|рецепта|рецептов', $day['recipes_count']) }}</div>
                            </div>
                        @else
                            <div class="text-center py-2">
                                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="calendar-x" class="w-6 h-6 text-gray-400"></i>
                                </div>
                                <div class="text-sm text-gray-400">Нет меню</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Действия --}}
        <div class="mt-8 flex flex-wrap gap-4 justify-center">
            <a href="{{ route('dashboard.today') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 text-white rounded-xl font-medium hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                <i data-lucide="sun" class="w-5 h-5"></i>
                Меню на сегодня
            </a>
            <a href="{{ route('shopping-list.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition">
                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                Список покупок
            </a>
            <a href="{{ route('dashboard.calendar') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition">
                <i data-lucide="calendar" class="w-5 h-5"></i>
                Календарь
            </a>
        </div>
    </div>
</x-app-layout>
