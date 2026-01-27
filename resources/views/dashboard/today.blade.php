<x-app-layout>
    <div class="max-w-7xl mx-auto">
        {{-- Заголовок с датой --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Меню на сегодня</h1>
                </div>
                <p class="text-gray-600 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    {{ $today->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('dashboard.week') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                    <i data-lucide="calendar-days" class="w-4 h-4"></i>
                    Вся неделя
                </a>
                <a href="{{ route('shopping-list.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-xl hover:bg-green-600 transition">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    Список покупок
                </a>
            </div>
        </div>

        @if($recipes->count() > 0)
            @php
                $mealTypes = [
                    'breakfast' => ['label' => 'Завтрак', 'icon' => 'sunrise', 'color' => 'amber', 'time' => '08:00'],
                    'lunch' => ['label' => 'Обед', 'icon' => 'sun', 'color' => 'orange', 'time' => '13:00'],
                    'dinner' => ['label' => 'Ужин', 'icon' => 'sunset', 'color' => 'purple', 'time' => '19:00'],
                    'snack' => ['label' => 'Перекус', 'icon' => 'cookie', 'color' => 'green', 'time' => '16:00'],
                ];
            @endphp

            <div class="space-y-6">
                @foreach($mealTypes as $type => $config)
                    @if($recipes->has($type))
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-5 border-b border-gray-100 bg-{{ $config['color'] }}-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-{{ $config['color'] }}-100 rounded-xl flex items-center justify-center">
                                            <i data-lucide="{{ $config['icon'] }}" class="w-5 h-5 text-{{ $config['color'] }}-600"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $config['label'] }}</h3>
                                            <p class="text-sm text-gray-500">~{{ $config['time'] }}</p>
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $recipes->get($type)->count() }} {{ trans_choice('рецепт|рецепта|рецептов', $recipes->get($type)->count()) }}</span>
                                </div>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @foreach($recipes->get($type) as $recipe)
                                    <a href="{{ route('recipes.show', $recipe) }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 transition group">
                                        @if($recipe->image)
                                            <img src="{{ $recipe->image }}" alt="{{ $recipe->name }}" class="w-20 h-20 rounded-xl object-cover">
                                        @else
                                            <div class="w-20 h-20 bg-gray-100 rounded-xl flex items-center justify-center">
                                                <i data-lucide="chef-hat" class="w-8 h-8 text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-medium text-gray-900 group-hover:text-green-600 transition">{{ $recipe->name }}</h4>
                                            <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <i data-lucide="flame" class="w-4 h-4 text-orange-400"></i>
                                                    {{ $recipe->calories ?? 0 }} ккал
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <i data-lucide="timer" class="w-4 h-4 text-blue-400"></i>
                                                    {{ $recipe->cooking_time ?? 0 }} мин
                                                </span>
                                                @if($recipe->proteins)
                                                    <span class="hidden sm:flex items-center gap-1">
                                                        Б: {{ $recipe->proteins }}г
                                                    </span>
                                                @endif
                                                @if($recipe->fats)
                                                    <span class="hidden sm:flex items-center gap-1">
                                                        Ж: {{ $recipe->fats }}г
                                                    </span>
                                                @endif
                                                @if($recipe->carbs)
                                                    <span class="hidden sm:flex items-center gap-1">
                                                        У: {{ $recipe->carbs }}г
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <i data-lucide="chevron-right" class="w-5 h-5 text-gray-400 group-hover:text-green-500 transition flex-shrink-0"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Итого за день --}}
            @php
                $totalCalories = $recipes->flatten()->sum('calories');
                $totalProteins = $recipes->flatten()->sum('proteins');
                $totalFats = $recipes->flatten()->sum('fats');
                $totalCarbs = $recipes->flatten()->sum('carbs');
            @endphp
            <div class="mt-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white">
                <h3 class="font-semibold mb-4 flex items-center gap-2">
                    <i data-lucide="calculator" class="w-5 h-5"></i>
                    Итого за день
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm">
                        <div class="text-green-100 text-sm mb-1">Калории</div>
                        <div class="text-2xl font-bold">{{ $totalCalories }} ккал</div>
                    </div>
                    <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm">
                        <div class="text-green-100 text-sm mb-1">Белки</div>
                        <div class="text-2xl font-bold">{{ $totalProteins }} г</div>
                    </div>
                    <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm">
                        <div class="text-green-100 text-sm mb-1">Жиры</div>
                        <div class="text-2xl font-bold">{{ $totalFats }} г</div>
                    </div>
                    <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm">
                        <div class="text-green-100 text-sm mb-1">Углеводы</div>
                        <div class="text-2xl font-bold">{{ $totalCarbs }} г</div>
                    </div>
                </div>
            </div>
        @else
            {{-- Пустое состояние --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="calendar-x" class="w-10 h-10 text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Меню на сегодня не найдено</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Возможно, меню на этот день ещё не опубликовано или у вас нет активной подписки
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('menus.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-500 text-white rounded-xl font-medium hover:bg-green-600 transition">
                        <i data-lucide="book-open" class="w-5 h-5"></i>
                        Посмотреть все меню
                    </a>
                    <a href="{{ route('plans.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition">
                        <i data-lucide="crown" class="w-5 h-5"></i>
                        Выбрать план
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
