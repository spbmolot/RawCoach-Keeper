@extends('layouts.public')

@section('title', $menu->title . ' — RawPlan')
@section('description', Str::limit($menu->description, 160))

@php $activeNav = 'menus'; @endphp

@push('styles')
<style>
    .day-card { transition: all 0.2s ease; }
    .day-card:hover { transform: scale(1.02); }
    .day-card.active { ring: 2px; ring-color: #22c55e; }
</style>
@endpush

@section('content')
    <!-- Breadcrumbs -->
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-green-600">Главная</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <a href="{{ route('menus.index') }}" class="hover:text-green-600">Меню</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="text-gray-900">{{ $menu->title }}</span>
            </nav>
        </div>
    </div>

    <!-- Menu Header -->
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">{{ $menu->title }}</h1>
                    <p class="text-lg text-gray-600">{{ $menu->description }}</p>
                    <div class="flex items-center gap-6 mt-4 text-sm text-gray-500">
                        <span class="flex items-center gap-2">
                            <i data-lucide="calendar-days" class="w-5 h-5 text-green-500"></i>
                            {{ $menu->days->count() }} дней
                        </span>
                        <span class="flex items-center gap-2">
                            <i data-lucide="flame" class="w-5 h-5 text-orange-500"></i>
                            ~{{ $menu->total_calories ?? 1300 }} ккал/день
                        </span>
                        <span class="flex items-center gap-2">
                            <i data-lucide="utensils" class="w-5 h-5 text-blue-500"></i>
                            4 приёма пищи
                        </span>
                    </div>
                </div>
                <div class="flex gap-3">
                    @if($menu->pdf_file)
                        <a href="{{ Storage::url($menu->pdf_file) }}" class="px-5 py-2.5 bg-red-50 text-red-600 rounded-xl font-semibold hover:bg-red-100 transition flex items-center gap-2">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                            PDF
                        </a>
                    @endif
                    @if($menu->excel_file)
                        <a href="{{ Storage::url($menu->excel_file) }}" class="px-5 py-2.5 bg-green-50 text-green-600 rounded-xl font-semibold hover:bg-green-100 transition flex items-center gap-2">
                            <i data-lucide="table" class="w-5 h-5"></i>
                            Excel
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Calendar Grid -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Week Headers -->
        <div class="grid grid-cols-7 gap-2 mb-4">
            @foreach(['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'] as $dayName)
                <div class="text-center text-sm font-semibold text-gray-500 py-2">{{ $dayName }}</div>
            @endforeach
        </div>

        <!-- Days Grid -->
        <div class="grid grid-cols-7 gap-2 mb-8">
            @php
                $firstDayOfMonth = \Carbon\Carbon::create($menu->year, $menu->month, 1);
                $startOffset = $firstDayOfMonth->dayOfWeekIso - 1;
                $daysInMonth = $firstDayOfMonth->daysInMonth;
            @endphp
            
            @for($i = 0; $i < $startOffset; $i++)
                <div class="aspect-square"></div>
            @endfor
            
            @foreach($menu->days->sortBy('day_number') as $day)
                <button 
                    onclick="showDay({{ $day->day_number }})" 
                    class="day-card aspect-square bg-white rounded-xl shadow-sm border-2 border-transparent hover:border-green-300 p-2 text-left transition {{ $day->day_number == 1 ? 'ring-2 ring-green-500' : '' }}"
                    id="day-btn-{{ $day->day_number }}"
                >
                    <div class="text-lg font-bold text-gray-900">{{ $day->day_number }}</div>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ $day->total_calories ?? '~1300' }} ккал
                    </div>
                </button>
            @endforeach
        </div>

        <!-- Selected Day Details -->
        <div class="bg-white rounded-2xl shadow-sm p-6 md:p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900" id="selected-day-title">День 1</h2>
                <div class="flex items-center gap-2">
                    <button onclick="prevDay()" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                    </button>
                    <button onclick="nextDay()" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            @foreach($menu->days->sortBy('day_number') as $day)
                <div class="day-content hidden" id="day-content-{{ $day->day_number }}" data-day="{{ $day->day_number }}">
                    <div class="grid md:grid-cols-2 gap-6">
                        @foreach($day->meals->sortBy('order') as $meal)
                            <div class="bg-gray-50 rounded-xl p-5">
                                <div class="flex items-center gap-3 mb-3">
                                    @switch($meal->meal_type)
                                        @case('breakfast')
                                            <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
                                                <i data-lucide="sunrise" class="w-5 h-5 text-orange-500"></i>
                                            </div>
                                            <span class="font-semibold text-gray-900">Завтрак</span>
                                            @break
                                        @case('lunch')
                                            <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                                                <i data-lucide="sun" class="w-5 h-5 text-yellow-500"></i>
                                            </div>
                                            <span class="font-semibold text-gray-900">Обед</span>
                                            @break
                                        @case('snack')
                                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                                                <i data-lucide="apple" class="w-5 h-5 text-green-500"></i>
                                            </div>
                                            <span class="font-semibold text-gray-900">Перекус</span>
                                            @break
                                        @case('dinner')
                                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                                <i data-lucide="moon" class="w-5 h-5 text-purple-500"></i>
                                            </div>
                                            <span class="font-semibold text-gray-900">Ужин</span>
                                            @break
                                    @endswitch
                                </div>
                                
                                @if($meal->recipe)
                                    <a href="{{ route('recipes.show', $meal->recipe) }}" class="block group">
                                        <h4 class="font-medium text-gray-900 group-hover:text-green-600 transition mb-2">
                                            {{ $meal->recipe->title }}
                                        </h4>
                                        <div class="flex items-center gap-4 text-sm text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="flame" class="w-4 h-4 text-orange-400"></i>
                                                {{ $meal->recipe->calories }} ккал
                                            </span>
                                            <span>Б: {{ $meal->recipe->proteins }}г</span>
                                            <span>Ж: {{ $meal->recipe->fats }}г</span>
                                            <span>У: {{ $meal->recipe->carbs }}г</span>
                                        </div>
                                    </a>
                                @else
                                    <p class="text-gray-400">Рецепт не назначен</p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Day Summary -->
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="text-gray-600">
                                <span class="font-semibold text-gray-900">Итого за день:</span>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-orange-500">{{ $day->total_calories ?? $day->meals->sum(fn($m) => $m->recipe?->calories ?? 0) }}</div>
                                    <div class="text-xs text-gray-500">ккал</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-blue-500">{{ $day->total_proteins ?? $day->meals->sum(fn($m) => $m->recipe?->proteins ?? 0) }}</div>
                                    <div class="text-xs text-gray-500">белки</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-yellow-500">{{ $day->total_fats ?? $day->meals->sum(fn($m) => $m->recipe?->fats ?? 0) }}</div>
                                    <div class="text-xs text-gray-500">жиры</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-purple-500">{{ $day->total_carbs ?? $day->meals->sum(fn($m) => $m->recipe?->carbs ?? 0) }}</div>
                                    <div class="text-xs text-gray-500">углеводы</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </main>

@endsection

@push('scripts')
<script>
    let currentDay = 1;
    const totalDays = {{ $menu->days->count() }};
    
    function showDay(dayNumber) {
        document.querySelectorAll('.day-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.day-card').forEach(el => {
            el.classList.remove('ring-2', 'ring-green-500');
        });
        
        const content = document.getElementById('day-content-' + dayNumber);
        if (content) content.classList.remove('hidden');
        
        const btn = document.getElementById('day-btn-' + dayNumber);
        if (btn) btn.classList.add('ring-2', 'ring-green-500');
        
        document.getElementById('selected-day-title').textContent = 'День ' + dayNumber;
        currentDay = dayNumber;
        lucide.createIcons();
    }
    
    function prevDay() { if (currentDay > 1) showDay(currentDay - 1); }
    function nextDay() { if (currentDay < totalDays) showDay(currentDay + 1); }
    
    showDay(1);
</script>
@endpush
