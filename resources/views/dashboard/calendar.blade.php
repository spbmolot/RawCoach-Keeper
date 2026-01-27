<x-app-layout>
    <div class="max-w-7xl mx-auto">
        {{-- Заголовок --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Календарь меню</h1>
                </div>
                <p class="text-gray-600">Обзор вашего плана питания на месяц</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('dashboard.today') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-xl hover:bg-green-600 transition">
                    <i data-lucide="sun" class="w-4 h-4"></i>
                    Сегодня
                </a>
            </div>
        </div>

        {{-- Календарь --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Навигация по месяцам --}}
            <div class="p-4 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between">
                    <a href="{{ route('dashboard.calendar', ['month' => $startDate->copy()->subMonth()->month, 'year' => $startDate->copy()->subMonth()->year]) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-white rounded-xl transition">
                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                        <span class="hidden sm:inline">{{ $startDate->copy()->subMonth()->translatedFormat('F') }}</span>
                    </a>
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $startDate->translatedFormat('F Y') }}
                    </h2>
                    <a href="{{ route('dashboard.calendar', ['month' => $startDate->copy()->addMonth()->month, 'year' => $startDate->copy()->addMonth()->year]) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-white rounded-xl transition">
                        <span class="hidden sm:inline">{{ $startDate->copy()->addMonth()->translatedFormat('F') }}</span>
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>

            <div class="p-4">
                {{-- Дни недели --}}
                <div class="grid grid-cols-7 gap-2 mb-2">
                    @foreach(['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'] as $dayName)
                        <div class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wide py-2">
                            {{ $dayName }}
                        </div>
                    @endforeach
                </div>

                {{-- Дни месяца --}}
                <div class="grid grid-cols-7 gap-2">
                    @php
                        $firstDayOfWeek = $startDate->copy()->startOfMonth()->dayOfWeekIso;
                        $daysInMonth = $startDate->daysInMonth;
                    @endphp

                    @for($i = 1; $i < $firstDayOfWeek; $i++)
                        <div class="aspect-square"></div>
                    @endfor

                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $currentDate = $startDate->copy()->day($day);
                            $dateKey = $currentDate->format('Y-m-d');
                            $hasMenu = $menuDays->has($dateKey);
                            $isToday = $currentDate->isToday();
                            $isPast = $currentDate->isPast() && !$isToday;
                        @endphp
                        <div class="aspect-square p-1">
                            <div class="h-full rounded-xl p-2 flex flex-col {{ $isToday ? 'bg-green-500 text-white' : ($hasMenu ? 'bg-green-50 hover:bg-green-100' : 'bg-gray-50 hover:bg-gray-100') }} {{ $isPast ? 'opacity-50' : '' }} transition cursor-pointer">
                                <div class="text-sm font-medium {{ $isToday ? '' : 'text-gray-900' }}">
                                    {{ $day }}
                                </div>
                                @if($hasMenu && !$isToday)
                                    <div class="mt-auto">
                                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Легенда --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                <div class="flex flex-wrap items-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-green-500 rounded"></div>
                        <span class="text-gray-600">Сегодня</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-green-50 rounded flex items-center justify-center">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                        </div>
                        <span class="text-gray-600">Есть меню</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-gray-100 rounded"></div>
                        <span class="text-gray-600">Нет меню</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
