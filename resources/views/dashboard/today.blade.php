<x-app-layout>
    <div class="max-w-7xl mx-auto">
        {{-- Заголовок с датой --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 sm:gap-4 mb-6 sm:mb-8">
            <div>
                <div class="flex items-center gap-2 sm:gap-3 mb-1 sm:mb-2">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
                        <i data-lucide="arrow-left" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </a>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Меню на сегодня</h1>
                </div>
                <p class="text-gray-600 flex items-center gap-2 text-sm sm:text-base">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                    {{ $today->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2 sm:gap-3">
                <a href="{{ route('dashboard.week') }}" class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition text-sm sm:text-base">
                    <i data-lucide="calendar-days" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                    <span class="hidden xs:inline">Вся</span> неделя
                </a>
                <a href="{{ route('shopping-list.index') }}" class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 bg-green-500 text-white rounded-xl hover:bg-green-600 transition text-sm sm:text-base">
                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                    <span class="hidden xs:inline">Список</span> покупок
                </a>
            </div>
        </div>

        {{-- Баннер бесплатного превью --}}
        @if(!empty($isFreePreview))
            <div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i data-lucide="gift" class="w-6 h-6 text-amber-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 mb-1">Бесплатный день меню</h3>
                        <p class="text-sm text-gray-600">Это превью первого дня меню. Оформите подписку, чтобы получить меню на каждый день с рецептами, списками покупок и БЖУ.</p>
                    </div>
                    <a href="{{ route('plans.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition shadow-lg shadow-green-500/25 flex-shrink-0 text-sm">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                        Оформить подписку
                    </a>
                </div>
            </div>
        @endif

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
                                @foreach($recipes->get($type) as $meal)
                                    @php $recipe = $meal->recipe; @endphp
                                    @if($recipe)
                                    <div class="flex items-center gap-4 p-4 hover:bg-gray-50 transition group relative" id="meal-card-{{ $meal->id }}">
                                        <a href="{{ route('recipes.show', $recipe) }}" class="flex items-center gap-4 flex-1 min-w-0">
                                            @if($recipe->image)
                                                <img src="{{ Storage::url($recipe->image) }}" alt="{{ $recipe->title }}" class="w-20 h-20 rounded-xl object-cover flex-shrink-0">
                                            @else
                                                <div class="w-20 h-20 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                                    <i data-lucide="chef-hat" class="w-8 h-8 text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="font-medium text-gray-900 group-hover:text-green-600 transition truncate">{{ $recipe->title }}</h4>
                                                    @if($meal->swapped)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full flex-shrink-0">
                                                            <i data-lucide="repeat-2" class="w-3 h-3"></i>
                                                            Замена
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
                                                    <span class="flex items-center gap-1">
                                                        <i data-lucide="flame" class="w-4 h-4 text-orange-400"></i>
                                                        {{ $recipe->calories ?? 0 }} ккал
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <i data-lucide="timer" class="w-4 h-4 text-blue-400"></i>
                                                        {{ $recipe->total_time ?? 0 }} мин
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
                                        </a>
                                        {{-- Кнопки замены / сброса --}}
                                        @if($hasSubscription && empty($isFreePreview))
                                            <div class="flex items-center gap-1 flex-shrink-0">
                                                @if($meal->swapped)
                                                    <button
                                                        onclick="resetSwap({{ $meal->id }})"
                                                        class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition"
                                                        title="Вернуть оригинал"
                                                    >
                                                        <i data-lucide="undo-2" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                                                    </button>
                                                @endif
                                                <button
                                                    onclick="openSwapModal({{ $meal->id }})"
                                                    class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition"
                                                    title="Заменить рецепт"
                                                >
                                                    <i data-lucide="arrow-left-right" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Итого за день --}}
            @php
                $allRecipes = $recipes->flatten()->map(fn($m) => $m->recipe)->filter();
                $totalCalories = $allRecipes->sum('calories');
                $totalProteins = $allRecipes->sum('proteins');
                $totalFats = $allRecipes->sum('fats');
                $totalCarbs = $allRecipes->sum('carbs');
            @endphp
            <div class="mt-6 sm:mt-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl sm:rounded-2xl p-4 sm:p-6 text-white">
                <h3 class="font-semibold mb-3 sm:mb-4 flex items-center gap-2 text-sm sm:text-base">
                    <i data-lucide="calculator" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    Итого за день
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4">
                    <div class="bg-white/20 rounded-lg sm:rounded-xl p-3 sm:p-4 backdrop-blur-sm">
                        <div class="text-green-100 text-xs sm:text-sm mb-0.5 sm:mb-1">Калории</div>
                        <div class="text-lg sm:text-2xl font-bold">{{ $totalCalories }} <span class="text-sm sm:text-base">ккал</span></div>
                    </div>
                    <div class="bg-white/20 rounded-lg sm:rounded-xl p-3 sm:p-4 backdrop-blur-sm">
                        <div class="text-green-100 text-xs sm:text-sm mb-0.5 sm:mb-1">Белки</div>
                        <div class="text-lg sm:text-2xl font-bold">{{ $totalProteins }} г</div>
                    </div>
                    <div class="bg-white/20 rounded-lg sm:rounded-xl p-3 sm:p-4 backdrop-blur-sm">
                        <div class="text-green-100 text-xs sm:text-sm mb-0.5 sm:mb-1">Жиры</div>
                        <div class="text-lg sm:text-2xl font-bold">{{ $totalFats }} г</div>
                    </div>
                    <div class="bg-white/20 rounded-lg sm:rounded-xl p-3 sm:p-4 backdrop-blur-sm">
                        <div class="text-green-100 text-xs sm:text-sm mb-0.5 sm:mb-1">Углеводы</div>
                        <div class="text-lg sm:text-2xl font-bold">{{ $totalCarbs }} г</div>
                    </div>
                </div>
            </div>
        @else
            {{-- Пустое состояние --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-12 text-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-6">
                    <i data-lucide="calendar-x" class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Меню на сегодня не найдено</h3>
                <p class="text-gray-600 mb-4 sm:mb-6 max-w-md mx-auto text-sm sm:text-base">
                    Возможно, меню на этот день ещё не опубликовано или у вас нет активной подписки
                </p>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 justify-center">
                    <a href="{{ route('menus.index') }}" class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-green-500 text-white rounded-xl font-medium hover:bg-green-600 transition text-sm sm:text-base">
                        <i data-lucide="book-open" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        Все меню
                    </a>
                    <a href="{{ route('plans.index') }}" class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition text-sm sm:text-base">
                        <i data-lucide="crown" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        Выбрать план
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Модалка замены рецепта --}}
    @if($hasSubscription && empty($isFreePreview))
    <div id="swapModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeSwapModal()"></div>
        <div class="absolute inset-x-4 top-[5%] bottom-[5%] sm:inset-x-auto sm:left-1/2 sm:-translate-x-1/2 sm:w-full sm:max-w-lg bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden">
            {{-- Заголовок модалки --}}
            <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Заменить рецепт</h3>
                    <p class="text-sm text-gray-500 mt-0.5" id="swapModalSubtitle">Выберите альтернативу</p>
                </div>
                <button onclick="closeSwapModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            {{-- Причина замены (опционально) --}}
            <div class="px-4 sm:px-5 py-3 border-b border-gray-100 flex-shrink-0">
                <select id="swapReason" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Причина замены (необязательно)</option>
                    <option value="Не нравится вкус">Не нравится вкус</option>
                    <option value="Аллергия/непереносимость">Аллергия/непереносимость</option>
                    <option value="Нет ингредиентов">Нет ингредиентов</option>
                    <option value="Слишком сложно готовить">Слишком сложно готовить</option>
                    <option value="Слишком долго">Слишком долго</option>
                    <option value="Хочу разнообразия">Хочу разнообразия</option>
                </select>
            </div>

            {{-- Список альтернатив --}}
            <div class="flex-1 overflow-y-auto" id="swapAlternativesList">
                <div class="flex items-center justify-center h-32">
                    <div class="flex items-center gap-3 text-gray-500">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Загрузка альтернатив...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    let currentDayMealId = null;

    function openSwapModal(dayMealId) {
        currentDayMealId = dayMealId;
        const modal = document.getElementById('swapModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        loadAlternatives(dayMealId);
    }

    function closeSwapModal() {
        const modal = document.getElementById('swapModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        currentDayMealId = null;
    }

    async function loadAlternatives(dayMealId) {
        const list = document.getElementById('swapAlternativesList');
        list.innerHTML = '<div class="flex items-center justify-center h-32"><div class="flex items-center gap-3 text-gray-500"><svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Загрузка альтернатив...</div></div>';

        try {
            const resp = await fetch(`/meal-swap/${dayMealId}/alternatives`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await resp.json();

            if (!data.success || !data.data.alternatives.length) {
                list.innerHTML = '<div class="flex flex-col items-center justify-center h-32 text-gray-500"><i data-lucide="search-x" class="w-8 h-8 mb-2"></i><p>Альтернатив не найдено</p></div>';
                if (window.lucide) lucide.createIcons();
                return;
            }

            const orig = data.data.original;
            document.getElementById('swapModalSubtitle').textContent = `Замена для: ${orig.title}`;

            let html = '<div class="divide-y divide-gray-100">';
            data.data.alternatives.forEach(alt => {
                const diffSign = alt.calorie_diff > 0 ? '+' : '';
                const diffColor = Math.abs(alt.calorie_diff) <= 50 ? 'text-green-600' : 'text-amber-600';
                html += `
                <button onclick="performSwap(${dayMealId}, ${alt.id})" class="w-full flex items-center gap-3 p-4 hover:bg-green-50 transition text-left">
                    <div class="w-14 h-14 rounded-xl flex-shrink-0 overflow-hidden bg-gray-100">
                        ${alt.image_url ? `<img src="${alt.image_url}" alt="" class="w-full h-full object-cover">` : '<div class="w-full h-full flex items-center justify-center"><i data-lucide="chef-hat" class="w-6 h-6 text-gray-400"></i></div>'}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 text-sm truncate">${alt.title}</h4>
                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                            <span>${alt.calories} ккал</span>
                            <span class="${diffColor}">(${diffSign}${alt.calorie_diff})</span>
                            <span>${alt.cook_time} мин</span>
                        </div>
                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-400">
                            <span>Б:${alt.proteins}г</span>
                            <span>Ж:${alt.fats}г</span>
                            <span>У:${alt.carbs}г</span>
                        </div>
                    </div>
                    <i data-lucide="arrow-right" class="w-4 h-4 text-green-500 flex-shrink-0"></i>
                </button>`;
            });
            html += '</div>';
            list.innerHTML = html;
            if (window.lucide) lucide.createIcons();
        } catch (err) {
            list.innerHTML = '<div class="flex flex-col items-center justify-center h-32 text-red-500"><p>Ошибка загрузки</p></div>';
        }
    }

    async function performSwap(dayMealId, recipeId) {
        const reason = document.getElementById('swapReason').value;
        try {
            const resp = await fetch(`/meal-swap/${dayMealId}/swap`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ recipe_id: recipeId, reason: reason || null })
            });
            const data = await resp.json();
            if (data.success) {
                closeSwapModal();
                window.location.reload();
            } else {
                alert(data.message || 'Ошибка замены');
            }
        } catch (err) {
            alert('Ошибка сети');
        }
    }

    async function resetSwap(dayMealId) {
        if (!confirm('Вернуть оригинальный рецепт?')) return;
        try {
            const resp = await fetch(`/meal-swap/${dayMealId}/reset`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await resp.json();
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Ошибка сброса');
            }
        } catch (err) {
            alert('Ошибка сети');
        }
    }

    // Закрытие по Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSwapModal();
    });
    </script>
    @endif
</x-app-layout>
