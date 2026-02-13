<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('personal-plans.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Назад к списку
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        {{-- Заголовок --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Персональный план #{{ $personalPlan->id }}</h1>
                    @php
                        $statusColors = ['pending' => 'bg-yellow-100 text-yellow-700', 'in_progress' => 'bg-blue-100 text-blue-700', 'completed' => 'bg-green-100 text-green-700', 'cancelled' => 'bg-red-100 text-red-700'];
                        $statusLabels = ['pending' => 'Ожидает обработки', 'in_progress' => 'В работе у нутрициолога', 'completed' => 'Готов', 'cancelled' => 'Отменён'];
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$personalPlan->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $statusLabels[$personalPlan->status] ?? $personalPlan->status }}
                    </span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($personalPlan->status === 'pending')
                        <a href="{{ route('personal-plans.edit', $personalPlan) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-sm font-medium transition">
                            <i data-lucide="pencil" class="w-4 h-4"></i> Редактировать
                        </a>
                        <form action="{{ route('personal-plans.cancel', $personalPlan) }}" method="POST" onsubmit="return confirm('Отменить персональный план?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-xl text-sm font-medium transition">
                                <i data-lucide="x" class="w-4 h-4"></i> Отменить
                            </button>
                        </form>
                    @endif
                    @if(in_array($personalPlan->status, ['in_progress', 'completed']))
                        <a href="{{ route('personal-plans.chat', $personalPlan) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-xl text-sm font-medium transition">
                            <i data-lucide="message-circle" class="w-4 h-4"></i> Чат с нутрициологом
                        </a>
                    @endif
                    @if($personalPlan->status === 'completed')
                        <a href="{{ route('personal-plans.download', $personalPlan) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-medium transition">
                            <i data-lucide="download" class="w-4 h-4"></i> Скачать PDF
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Анкета --}}
        @if($personalPlan->questionnaire)
            @php $q = $personalPlan->questionnaire; @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-purple-500"></i> Данные анкеты
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <span class="text-xs text-gray-500 block mb-1">Цель</span>
                        <span class="font-medium text-gray-900">{{ ['weight_loss' => 'Похудение', 'weight_gain' => 'Набор массы', 'maintenance' => 'Поддержание веса', 'muscle_gain' => 'Набор мышечной массы'][$q->goal] ?? $q->goal }}</span>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <span class="text-xs text-gray-500 block mb-1">Текущий вес</span>
                        <span class="font-medium text-gray-900">{{ $q->current_weight }} кг</span>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <span class="text-xs text-gray-500 block mb-1">Целевой вес</span>
                        <span class="font-medium text-gray-900">{{ $q->target_weight }} кг</span>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <span class="text-xs text-gray-500 block mb-1">Рост</span>
                        <span class="font-medium text-gray-900">{{ $q->height }} см</span>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <span class="text-xs text-gray-500 block mb-1">Возраст</span>
                        <span class="font-medium text-gray-900">{{ $q->age }} лет</span>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <span class="text-xs text-gray-500 block mb-1">Пол</span>
                        <span class="font-medium text-gray-900">{{ $q->gender === 'male' ? 'Мужской' : 'Женский' }}</span>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <span class="text-xs text-gray-500 block mb-1">Активность</span>
                        <span class="font-medium text-gray-900">{{ ['sedentary' => 'Сидячий', 'light' => 'Лёгкая', 'moderate' => 'Умеренная', 'active' => 'Активная', 'very_active' => 'Очень активная'][$q->activity_level] ?? $q->activity_level }}</span>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <span class="text-xs text-gray-500 block mb-1">Время готовки</span>
                        <span class="font-medium text-gray-900">{{ ['quick' => 'Быстро (до 30 мин)', 'medium' => 'Средне (30-60 мин)', 'any' => 'Любое'][$q->cooking_time_preference] ?? $q->cooking_time_preference }}</span>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <span class="text-xs text-gray-500 block mb-1">Бюджет</span>
                        <span class="font-medium text-gray-900">{{ ['low' => 'Экономный', 'medium' => 'Средний', 'high' => 'Без ограничений'][$q->budget_level] ?? $q->budget_level }}</span>
                    </div>
                </div>

                @if(!empty($q->dietary_preferences))
                    <div class="mt-4">
                        <span class="text-xs text-gray-500 block mb-2">Предпочтения в питании</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($q->dietary_preferences as $pref)
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-medium">{{ $pref }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty($q->allergies))
                    <div class="mt-4">
                        <span class="text-xs text-gray-500 block mb-2">Аллергии</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($q->allergies as $allergy)
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-medium">{{ $allergy }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($q->additional_notes)
                    <div class="mt-4">
                        <span class="text-xs text-gray-500 block mb-2">Дополнительные пожелания</span>
                        <p class="text-gray-700 text-sm">{{ $q->additional_notes }}</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- Нутрициолог --}}
        @if($personalPlan->nutritionist)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="user-check" class="w-5 h-5 text-blue-500"></i> Ваш нутрициолог
                </h2>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                        <i data-lucide="user" class="w-7 h-7 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $personalPlan->nutritionist->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $personalPlan->nutritionist->email }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Оценка (для завершённых планов) --}}
        @if($personalPlan->status === 'completed' && !$personalPlan->rating)
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-2 flex items-center gap-2">
                    <i data-lucide="star" class="w-5 h-5 text-amber-500"></i> Оцените план
                </h2>
                <p class="text-gray-600 text-sm mb-4">Ваша оценка поможет нам улучшить качество персональных планов</p>
                <div class="flex items-center gap-2" id="ratingStars">
                    @for($i = 1; $i <= 5; $i++)
                        <button onclick="submitRating({{ $i }})" class="w-10 h-10 rounded-lg bg-white border border-amber-200 hover:bg-amber-100 flex items-center justify-center transition" data-star="{{ $i }}">
                            <i data-lucide="star" class="w-5 h-5 text-amber-400"></i>
                        </button>
                    @endfor
                </div>
                <script>
                    function submitRating(rating) {
                        fetch('{{ route("personal-plans.rate", $personalPlan) }}', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                            body: JSON.stringify({rating: rating})
                        }).then(r => r.json()).then(data => {
                            if (data.success) location.reload();
                        });
                    }
                </script>
            </div>
        @elseif($personalPlan->rating)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-2 flex items-center gap-2">
                    <i data-lucide="star" class="w-5 h-5 text-amber-500"></i> Ваша оценка
                </h2>
                <div class="flex items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i data-lucide="star" class="w-5 h-5 {{ $i <= $personalPlan->rating ? 'text-amber-400 fill-amber-400' : 'text-gray-300' }}"></i>
                    @endfor
                    <span class="ml-2 text-gray-600 text-sm">{{ $personalPlan->rating }}/5</span>
                </div>
                @if($personalPlan->feedback)
                    <p class="mt-2 text-gray-600 text-sm">{{ $personalPlan->feedback }}</p>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
