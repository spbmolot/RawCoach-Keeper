<x-app-layout>
    <div class="max-w-5xl mx-auto" x-data="progressPage()" @recipe-selected.window="formCalories = $event.detail.calories; formProteins = $event.detail.proteins; formFats = $event.detail.fats; formCarbs = $event.detail.carbs">
        {{-- Flash-сообщения --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)" class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center justify-between">
                <span class="flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}</span>
                <button @click="show = false" class="text-green-400 hover:text-green-600"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Заголовок --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 sm:mb-8">
            <div>
                <div class="flex items-center gap-2 sm:gap-3 mb-1">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
                        <i data-lucide="arrow-left" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </a>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Мой прогресс</h1>
                </div>
                @if($streak > 0)
                    <p class="text-sm sm:text-base text-orange-600 font-medium flex items-center gap-1.5">
                        <i data-lucide="flame" class="w-4 h-4"></i>
                        Серия: {{ $streak }} {{ $streak == 1 ? 'день' : ($streak < 5 ? 'дня' : 'дней') }} подряд
                    </p>
                @else
                    <p class="text-gray-500 text-sm sm:text-base">Отслеживайте питание, вес и достижения</p>
                @endif
            </div>
        </div>

        {{-- Табы --}}
        <div class="flex gap-1 sm:gap-2 mb-6 bg-gray-100 rounded-xl p-1 overflow-x-auto">
            <button @click="tab = 'diary'" class="flex-1 min-w-0 px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg text-xs sm:text-sm font-medium transition whitespace-nowrap" :class="tab === 'diary' ? 'bg-white text-green-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                <i data-lucide="book-open" class="w-4 h-4 inline -mt-0.5"></i>
                <span class="hidden xs:inline ml-1">Дневник</span>
            </button>
            <button @click="tab = 'weight'" class="flex-1 min-w-0 px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg text-xs sm:text-sm font-medium transition whitespace-nowrap" :class="tab === 'weight' ? 'bg-white text-green-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                <i data-lucide="scale" class="w-4 h-4 inline -mt-0.5"></i>
                <span class="hidden xs:inline ml-1">Вес</span>
            </button>
            <button @click="tab = 'stats'" class="flex-1 min-w-0 px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg text-xs sm:text-sm font-medium transition whitespace-nowrap" :class="tab === 'stats' ? 'bg-white text-green-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                <i data-lucide="bar-chart-3" class="w-4 h-4 inline -mt-0.5"></i>
                <span class="hidden xs:inline ml-1">Статистика</span>
            </button>
            <button @click="tab = 'achievements'" class="flex-1 min-w-0 px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg text-xs sm:text-sm font-medium transition whitespace-nowrap" :class="tab === 'achievements' ? 'bg-white text-green-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                <i data-lucide="trophy" class="w-4 h-4 inline -mt-0.5"></i>
                <span class="hidden xs:inline ml-1">Достижения</span>
            </button>
        </div>

        {{-- ==================== ДНЕВНИК ПИТАНИЯ ==================== --}}
        <div x-show="tab === 'diary'" x-transition.opacity>
            {{-- Итоги за сегодня --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
                <h3 class="font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-5 h-5 text-green-500"></i>
                    Сегодня, {{ $today->translatedFormat('d F') }}
                </h3>
                <div class="grid grid-cols-4 gap-2 sm:gap-4">
                    <div class="bg-orange-50 rounded-lg sm:rounded-xl p-2 sm:p-4 text-center">
                        <div class="text-lg sm:text-2xl font-bold text-orange-600">{{ round($todayTotals['calories']) }}</div>
                        <div class="text-[10px] sm:text-xs text-gray-500">ккал</div>
                    </div>
                    <div class="bg-blue-50 rounded-lg sm:rounded-xl p-2 sm:p-4 text-center">
                        <div class="text-lg sm:text-2xl font-bold text-blue-600">{{ round($todayTotals['proteins'], 1) }}</div>
                        <div class="text-[10px] sm:text-xs text-gray-500">Белки</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg sm:rounded-xl p-2 sm:p-4 text-center">
                        <div class="text-lg sm:text-2xl font-bold text-yellow-600">{{ round($todayTotals['fats'], 1) }}</div>
                        <div class="text-[10px] sm:text-xs text-gray-500">Жиры</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg sm:rounded-xl p-2 sm:p-4 text-center">
                        <div class="text-lg sm:text-2xl font-bold text-purple-600">{{ round($todayTotals['carbs'], 1) }}</div>
                        <div class="text-[10px] sm:text-xs text-gray-500">Углеводы</div>
                    </div>
                </div>
            </div>

            {{-- Записи дневника за сегодня --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4 sm:mb-6">
                @if($todayEntries->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($todayEntries as $entry)
                            <div class="flex items-center justify-between p-3 sm:p-4 hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0
                                        {{ $entry->meal_type === 'breakfast' ? 'bg-amber-100' : '' }}
                                        {{ $entry->meal_type === 'lunch' ? 'bg-green-100' : '' }}
                                        {{ $entry->meal_type === 'dinner' ? 'bg-blue-100' : '' }}
                                        {{ $entry->meal_type === 'snack' ? 'bg-purple-100' : '' }}
                                    ">
                                        @switch($entry->meal_type)
                                            @case('breakfast') <i data-lucide="sunrise" class="w-4 h-4 sm:w-5 sm:h-5 text-amber-600"></i> @break
                                            @case('lunch') <i data-lucide="sun" class="w-4 h-4 sm:w-5 sm:h-5 text-green-600"></i> @break
                                            @case('dinner') <i data-lucide="sunset" class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600"></i> @break
                                            @case('snack') <i data-lucide="cookie" class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600"></i> @break
                                        @endswitch
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 text-sm sm:text-base truncate">{{ $entry->display_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $entry->meal_type_name }}{{ $entry->portion_size != 1 ? ' × ' . $entry->portion_size : '' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 sm:gap-4 flex-shrink-0">
                                    <span class="text-sm sm:text-base font-semibold text-gray-700">{{ round($entry->calories) }} ккал</span>
                                    <form action="{{ route('dashboard.progress.diary.destroy', $entry) }}" method="POST" onsubmit="return confirm('Удалить запись?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 sm:p-8 text-center text-gray-400">
                        <i data-lucide="utensils" class="w-10 h-10 mx-auto mb-2 opacity-50"></i>
                        <p class="text-sm">Нет записей за сегодня</p>
                    </div>
                @endif
            </div>

            {{-- Форма добавления --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-green-500"></i>
                    Добавить приём пищи
                </h3>
                <form action="{{ route('dashboard.progress.diary.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="date" value="{{ $today->format('Y-m-d') }}">

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach(['breakfast' => 'Завтрак', 'lunch' => 'Обед', 'dinner' => 'Ужин', 'snack' => 'Перекус'] as $type => $label)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="meal_type" value="{{ $type }}" class="peer sr-only" {{ $loop->first ? 'checked' : '' }}>
                                <div class="py-2 px-3 rounded-lg border-2 border-gray-200 text-center text-sm font-medium text-gray-600 transition peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 hover:border-gray-300">
                                    {{ $label }}
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- Поиск рецепта --}}
                    <div x-data="recipeSearch()" class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Блюдо</label>
                        <input 
                            type="text" 
                            x-model="query" 
                            @input.debounce.300ms="search()" 
                            @focus="showResults = results.length > 0"
                            @click.away="showResults = false"
                            placeholder="Начните вводить название или выберите из рецептов..."
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500"
                        >
                        <input type="hidden" name="recipe_id" x-model="selectedId">
                        <input type="hidden" name="custom_name" x-model="customName">

                        {{-- Результаты --}}
                        <div x-show="showResults && results.length > 0" x-transition.opacity class="absolute z-50 w-full mt-1 bg-white rounded-xl shadow-lg border border-gray-100 py-1 max-h-48 overflow-y-auto" x-cloak>
                            <template x-for="r in results" :key="r.id">
                                <button type="button" @click="selectRecipe(r)" class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 flex justify-between">
                                    <span x-text="r.title" class="truncate"></span>
                                    <span class="text-gray-400 flex-shrink-0 ml-2" x-text="r.calories + ' ккал'"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- КБЖУ --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Калории</label>
                            <input type="number" name="calories" x-model="formCalories" step="0.1" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Белки (г)</label>
                            <input type="number" name="proteins" x-model="formProteins" step="0.1" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Жиры (г)</label>
                            <input type="number" name="fats" x-model="formFats" step="0.1" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Углеводы (г)</label>
                            <input type="number" name="carbs" x-model="formCarbs" step="0.1" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-green-500 text-white rounded-xl font-medium hover:bg-green-600 transition flex items-center justify-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Добавить
                    </button>
                </form>
            </div>
        </div>

        {{-- ==================== ГРАФИК ВЕСА ==================== --}}
        <div x-show="tab === 'weight'" x-transition.opacity>
            {{-- Текущие показатели --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                @php
                    $latestWeight = $weightLogs->last();
                    $firstWeight = $weightLogs->first();
                    $targetWeight = auth()->user()->target_weight;
                    $totalChange = ($firstWeight && $latestWeight && $firstWeight->id !== $latestWeight->id)
                        ? round($latestWeight->weight - $firstWeight->weight, 1) : null;
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 text-center">
                    <div class="text-xs text-gray-500 mb-1">Текущий</div>
                    <div class="text-xl sm:text-2xl font-bold text-gray-900">{{ $latestWeight ? $latestWeight->weight : '—' }}</div>
                    <div class="text-xs text-gray-400">кг</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 text-center">
                    <div class="text-xs text-gray-500 mb-1">Цель</div>
                    <div class="text-xl sm:text-2xl font-bold text-green-600">{{ $targetWeight ?? '—' }}</div>
                    <div class="text-xs text-gray-400">кг</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 text-center">
                    <div class="text-xs text-gray-500 mb-1">Изменение</div>
                    <div class="text-xl sm:text-2xl font-bold {{ $totalChange !== null && $totalChange < 0 ? 'text-green-600' : ($totalChange > 0 ? 'text-red-500' : 'text-gray-900') }}">
                        {{ $totalChange !== null ? ($totalChange > 0 ? '+' : '') . $totalChange : '—' }}
                    </div>
                    <div class="text-xs text-gray-400">кг</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 text-center">
                    <div class="text-xs text-gray-500 mb-1">Записей</div>
                    <div class="text-xl sm:text-2xl font-bold text-gray-900">{{ $weightLogs->count() }}</div>
                    <div class="text-xs text-gray-400">замеров</div>
                </div>
            </div>

            {{-- График --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="trending-down" class="w-5 h-5 text-green-500"></i>
                    Динамика веса
                </h3>
                @if($weightLogs->count() >= 2)
                    <div class="relative" style="height: 250px">
                        <canvas id="weightChart"></canvas>
                    </div>
                @else
                    <div class="py-8 text-center text-gray-400">
                        <i data-lucide="line-chart" class="w-10 h-10 mx-auto mb-2 opacity-50"></i>
                        <p class="text-sm">Нужно минимум 2 замера для графика</p>
                    </div>
                @endif
            </div>

            {{-- Форма добавления веса --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-green-500"></i>
                    Записать вес
                </h3>
                <form action="{{ route('dashboard.progress.weight.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="date" name="date" value="{{ $today->format('Y-m-d') }}" max="{{ $today->format('Y-m-d') }}" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500">
                    <input type="number" name="weight" step="0.1" min="20" max="300" placeholder="Вес (кг)" required class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500 w-full sm:w-36" value="{{ $latestWeight?->weight }}">
                    <input type="text" name="notes" placeholder="Заметка (опц.)" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500 flex-1">
                    <button type="submit" class="px-6 py-2.5 bg-green-500 text-white rounded-xl font-medium hover:bg-green-600 transition flex items-center justify-center gap-2 whitespace-nowrap">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Сохранить
                    </button>
                </form>
            </div>
        </div>

        {{-- ==================== СТАТИСТИКА КБЖУ ==================== --}}
        <div x-show="tab === 'stats'" x-transition.opacity>
            {{-- За неделю --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="calendar-days" class="w-5 h-5 text-green-500"></i>
                    КБЖУ за неделю
                </h3>
                @if($weekStats->count() > 0)
                    <div class="overflow-x-auto -mx-4 sm:mx-0">
                        <table class="w-full text-sm min-w-[400px]">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left py-2 px-3 text-gray-500 font-medium">День</th>
                                    <th class="text-right py-2 px-3 text-orange-500 font-medium">Ккал</th>
                                    <th class="text-right py-2 px-3 text-blue-500 font-medium">Б</th>
                                    <th class="text-right py-2 px-3 text-yellow-500 font-medium">Ж</th>
                                    <th class="text-right py-2 px-3 text-purple-500 font-medium">У</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($weekStats as $day)
                                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                                        <td class="py-2.5 px-3 font-medium text-gray-900">{{ Carbon\Carbon::parse($day->date)->translatedFormat('D, d.m') }}</td>
                                        <td class="py-2.5 px-3 text-right text-gray-700">{{ round($day->calories) }}</td>
                                        <td class="py-2.5 px-3 text-right text-gray-700">{{ round($day->proteins, 1) }}</td>
                                        <td class="py-2.5 px-3 text-right text-gray-700">{{ round($day->fats, 1) }}</td>
                                        <td class="py-2.5 px-3 text-right text-gray-700">{{ round($day->carbs, 1) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-6 text-center text-gray-400">
                        <p class="text-sm">Нет данных за текущую неделю</p>
                    </div>
                @endif
            </div>

            {{-- Средние за месяц --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 text-green-500"></i>
                    Среднее за 30 дней
                </h3>
                @if($monthAvg && $monthAvg->avg_calories)
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                        <div class="bg-orange-50 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ round($monthAvg->avg_calories) }}</div>
                            <div class="text-xs text-gray-500 mt-1">ккал / день</div>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ round($monthAvg->avg_proteins, 1) }}</div>
                            <div class="text-xs text-gray-500 mt-1">Белки (г)</div>
                        </div>
                        <div class="bg-yellow-50 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ round($monthAvg->avg_fats, 1) }}</div>
                            <div class="text-xs text-gray-500 mt-1">Жиры (г)</div>
                        </div>
                        <div class="bg-purple-50 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ round($monthAvg->avg_carbs, 1) }}</div>
                            <div class="text-xs text-gray-500 mt-1">Углеводы (г)</div>
                        </div>
                    </div>
                @else
                    <div class="py-6 text-center text-gray-400">
                        <p class="text-sm">Нет данных — начните вести дневник</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ==================== ДОСТИЖЕНИЯ ==================== --}}
        <div x-show="tab === 'achievements'" x-transition.opacity>
            @php
                $unlockedCount = count($userAchievementIds);
                $totalCount = $allAchievements->count();
            @endphp
            {{-- Прогресс достижений --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i data-lucide="trophy" class="w-5 h-5 text-yellow-500"></i>
                        Прогресс
                    </h3>
                    <span class="text-sm font-bold text-gray-700">{{ $unlockedCount }} / {{ $totalCount }}</span>
                </div>
                <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-full transition-all duration-500" style="width: {{ $totalCount > 0 ? round($unlockedCount / $totalCount * 100) : 0 }}%"></div>
                </div>
            </div>

            {{-- Список достижений по категориям --}}
            @foreach($allAchievements->groupBy('category') as $category => $achievements)
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4 sm:mb-6">
                    <div class="p-3 sm:p-4 bg-gray-50 border-b border-gray-100">
                        <h4 class="font-semibold text-gray-900 text-sm sm:text-base">
                            {{ $achievements->first()->category_name }}
                        </h4>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($achievements as $achievement)
                            @php $unlocked = in_array($achievement->id, $userAchievementIds); @endphp
                            <div class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 {{ $unlocked ? '' : 'opacity-50' }}">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center flex-shrink-0 {{ $unlocked ? 'bg-yellow-100' : 'bg-gray-100' }}">
                                    <i data-lucide="{{ $achievement->icon }}" class="w-5 h-5 sm:w-6 sm:h-6 {{ $unlocked ? 'text-yellow-500' : 'text-gray-300' }}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm sm:text-base {{ $unlocked ? 'text-gray-900' : 'text-gray-400' }}">{{ $achievement->name }}</p>
                                    <p class="text-xs sm:text-sm {{ $unlocked ? 'text-gray-500' : 'text-gray-300' }}">{{ $achievement->description }}</p>
                                </div>
                                @if($unlocked)
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                            <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i data-lucide="lock" class="w-4 h-4 text-gray-300"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @push('scripts')
    @if($weightLogs->count() >= 2)
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('weightChart');
            if (!ctx) return;

            const labels = @json($weightLogs->pluck('date')->map(fn($d) => Carbon\Carbon::parse($d)->format('d.m')));
            const data = @json($weightLogs->pluck('weight'));
            const targetWeight = {{ auth()->user()->target_weight ?? 'null' }};

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Вес',
                            data: data,
                            borderColor: '#22c55e',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#22c55e',
                            borderWidth: 2,
                        },
                        ...(targetWeight ? [{
                            label: 'Цель',
                            data: Array(labels.length).fill(targetWeight),
                            borderColor: '#f59e0b',
                            borderDash: [5, 5],
                            borderWidth: 1.5,
                            pointRadius: 0,
                            fill: false,
                        }] : [])
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top', labels: { usePointStyle: true, boxWidth: 6 } },
                    },
                    scales: {
                        y: { 
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { callback: v => v + ' кг' }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
    @endif
    <script>
        function progressPage() {
            return {
                tab: 'diary',
                formCalories: 0,
                formProteins: 0,
                formFats: 0,
                formCarbs: 0,

                init() {
                    const hash = window.location.hash.replace('#', '');
                    if (['diary', 'weight', 'stats', 'achievements'].includes(hash)) {
                        this.tab = hash;
                    }
                    this.$watch('tab', (val) => { window.location.hash = val; });
                }
            };
        }

        function recipeSearch() {
            return {
                query: '',
                results: [],
                showResults: false,
                selectedId: '',
                customName: '',

                async search() {
                    if (this.query.length < 2) {
                        this.results = [];
                        this.showResults = false;
                        this.selectedId = '';
                        this.customName = this.query;
                        return;
                    }

                    this.customName = this.query;
                    this.selectedId = '';

                    try {
                        const res = await fetch('{{ route("dashboard.progress.recipes.search") }}?q=' + encodeURIComponent(this.query));
                        this.results = await res.json();
                        this.showResults = this.results.length > 0;
                    } catch (e) {
                        this.results = [];
                    }
                },

                selectRecipe(r) {
                    this.query = r.title;
                    this.selectedId = r.id;
                    this.customName = '';
                    this.showResults = false;

                    this.$dispatch('recipe-selected', { 
                        calories: r.calories, 
                        proteins: r.proteins, 
                        fats: r.fats, 
                        carbs: r.carbs 
                    });
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
