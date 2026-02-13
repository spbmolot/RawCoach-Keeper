<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('personal-plans.show', $personalPlan) }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Назад к плану
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Редактирование анкеты</h1>
                <p class="text-gray-600 mt-1 text-sm">Персональный план #{{ $personalPlan->id }}</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
            @endif

            @php $q = $personalPlan->questionnaire; @endphp

            <form action="{{ route('personal-plans.update', $personalPlan) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Цель --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Цель <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach(['weight_loss' => 'Похудение', 'weight_gain' => 'Набор массы', 'maintenance' => 'Поддержание веса', 'muscle_gain' => 'Набор мышц'] as $value => $label)
                            <label class="relative flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:border-green-300 transition has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                                <input type="radio" name="goal" value="{{ $value }}" {{ old('goal', $q->goal) === $value ? 'checked' : '' }} class="text-green-500 focus:ring-green-500">
                                <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('goal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Физические параметры --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label for="current_weight" class="block text-sm font-medium text-gray-700 mb-1">Текущий вес (кг) <span class="text-red-500">*</span></label>
                        <input type="number" name="current_weight" id="current_weight" value="{{ old('current_weight', $q->current_weight) }}" step="0.1" min="30" max="300" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                        @error('current_weight') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="target_weight" class="block text-sm font-medium text-gray-700 mb-1">Целевой вес (кг) <span class="text-red-500">*</span></label>
                        <input type="number" name="target_weight" id="target_weight" value="{{ old('target_weight', $q->target_weight) }}" step="0.1" min="30" max="300" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                        @error('target_weight') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 mb-1">Рост (см) <span class="text-red-500">*</span></label>
                        <input type="number" name="height" id="height" value="{{ old('height', $q->height) }}" min="100" max="250" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                        @error('height') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700 mb-1">Возраст <span class="text-red-500">*</span></label>
                        <input type="number" name="age" id="age" value="{{ old('age', $q->age) }}" min="16" max="100" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                        @error('age') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Пол --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Пол <span class="text-red-500">*</span></label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 px-4 py-2.5 border rounded-xl cursor-pointer hover:border-green-300 transition has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                            <input type="radio" name="gender" value="female" {{ old('gender', $q->gender) === 'female' ? 'checked' : '' }} class="text-green-500 focus:ring-green-500">
                            <span class="text-sm font-medium text-gray-700">Женский</span>
                        </label>
                        <label class="flex items-center gap-2 px-4 py-2.5 border rounded-xl cursor-pointer hover:border-green-300 transition has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                            <input type="radio" name="gender" value="male" {{ old('gender', $q->gender) === 'male' ? 'checked' : '' }} class="text-green-500 focus:ring-green-500">
                            <span class="text-sm font-medium text-gray-700">Мужской</span>
                        </label>
                    </div>
                    @error('gender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Уровень активности --}}
                <div>
                    <label for="activity_level" class="block text-sm font-medium text-gray-700 mb-1">Уровень активности <span class="text-red-500">*</span></label>
                    <select name="activity_level" id="activity_level" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                        <option value="">Выберите...</option>
                        @foreach(['sedentary' => 'Сидячий образ жизни', 'light' => 'Лёгкая активность (1-2 тренировки/нед)', 'moderate' => 'Умеренная (3-4 тренировки/нед)', 'active' => 'Активная (5-6 тренировок/нед)', 'very_active' => 'Очень активная (ежедневные тренировки)'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('activity_level', $q->activity_level) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('activity_level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Предпочтения в питании --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Предпочтения в питании</label>
                    <div class="flex flex-wrap gap-2">
                        @php $currentPrefs = old('dietary_preferences', $q->dietary_preferences ?? []); @endphp
                        @foreach(['Вегетарианство', 'Веганство', 'Без глютена', 'Без лактозы', 'Кето', 'Палео', 'Низкоуглеводная', 'Высокобелковая'] as $pref)
                            <label class="flex items-center gap-1.5 px-3 py-1.5 border rounded-lg cursor-pointer hover:border-green-300 transition has-[:checked]:border-green-500 has-[:checked]:bg-green-50 text-sm">
                                <input type="checkbox" name="dietary_preferences[]" value="{{ $pref }}" {{ in_array($pref, $currentPrefs) ? 'checked' : '' }} class="rounded text-green-500 focus:ring-green-500">
                                {{ $pref }}
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Аллергии --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Аллергии и непереносимости</label>
                    <div class="flex flex-wrap gap-2">
                        @php $currentAllergies = old('allergies', $q->allergies ?? []); @endphp
                        @foreach(['Орехи', 'Арахис', 'Молочные продукты', 'Яйца', 'Рыба', 'Морепродукты', 'Соя', 'Пшеница', 'Кунжут'] as $allergy)
                            <label class="flex items-center gap-1.5 px-3 py-1.5 border rounded-lg cursor-pointer hover:border-red-300 transition has-[:checked]:border-red-400 has-[:checked]:bg-red-50 text-sm">
                                <input type="checkbox" name="allergies[]" value="{{ $allergy }}" {{ in_array($allergy, $currentAllergies) ? 'checked' : '' }} class="rounded text-red-500 focus:ring-red-500">
                                {{ $allergy }}
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Время готовки и бюджет --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="cooking_time_preference" class="block text-sm font-medium text-gray-700 mb-1">Время на готовку <span class="text-red-500">*</span></label>
                        <select name="cooking_time_preference" id="cooking_time_preference" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                            <option value="">Выберите...</option>
                            @foreach(['quick' => 'Быстро (до 30 мин)', 'medium' => 'Средне (30-60 мин)', 'any' => 'Любое время'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('cooking_time_preference', $q->cooking_time_preference) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @error('cooking_time_preference') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="budget_level" class="block text-sm font-medium text-gray-700 mb-1">Бюджет на продукты <span class="text-red-500">*</span></label>
                        <select name="budget_level" id="budget_level" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                            <option value="">Выберите...</option>
                            @foreach(['low' => 'Экономный', 'medium' => 'Средний', 'high' => 'Без ограничений'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('budget_level', $q->budget_level) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @error('budget_level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Дополнительные пожелания --}}
                <div>
                    <label for="additional_notes" class="block text-sm font-medium text-gray-700 mb-1">Дополнительные пожелания</label>
                    <textarea name="additional_notes" id="additional_notes" rows="3" maxlength="1000" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Расскажите о своих предпочтениях...">{{ old('additional_notes', $q->additional_notes) }}</textarea>
                    @error('additional_notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition shadow-lg shadow-green-500/25">
                        <i data-lucide="save" class="w-4 h-4"></i> Сохранить изменения
                    </button>
                    <a href="{{ route('personal-plans.show', $personalPlan) }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
