<x-app-layout>
    @section('title', 'Расскажите о себе — RawPlan')

    <style>
        .ob-option { cursor: pointer; }
        .ob-option input { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); }

        /* Радио-кнопки (цели) */
        .ob-radio-box {
            display: flex; align-items: center; gap: 12px;
            padding: 14px; border-radius: 12px;
            border: 2px solid #e5e7eb; background: #fff;
            transition: border-color .2s, background .2s;
        }
        .ob-radio-box:hover { border-color: #d1d5db; }
        .ob-radio-box.is-selected { border-color: #22c55e; background: #f0fdf4; }

        /* Чипсы (чекбоксы) */
        .ob-chip {
            display: inline-block;
            padding: 8px 16px; border-radius: 9999px;
            border: 2px solid #e5e7eb; background: #fff;
            font-size: 14px; font-weight: 500; color: #4b5563;
            transition: border-color .2s, background .2s, color .2s;
        }
        .ob-chip:hover { border-color: #d1d5db; }
        .ob-chip.is-selected { border-color: #22c55e; background: #f0fdf4; color: #15803d; }

        .ob-icon-box { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    </style>

    <div class="min-h-[80vh] flex items-center justify-center px-4 py-8">
        <div class="max-w-2xl w-full">
            {{-- Прогресс-бар --}}
            <div class="flex items-center justify-center gap-2 mb-8">
                <div class="w-10 h-1.5 rounded-full bg-green-500"></div>
                <div class="w-10 h-1.5 rounded-full bg-green-500"></div>
                <div class="w-10 h-1.5 rounded-full bg-gray-200"></div>
                <span class="ml-2 text-xs text-gray-400">Шаг 2 из 3</span>
            </div>

            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                <div class="px-6 sm:px-10 py-8">
                    <div class="text-center mb-8">
                        <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="clipboard-list" class="w-7 h-7 text-green-600"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">Расскажите о себе</h1>
                        <p class="text-gray-500">Это поможет нам подобрать идеальное меню для вас</p>
                    </div>

                    <form action="{{ route('onboarding.survey.store') }}" method="POST" id="surveyForm">
                        @csrf

                        {{-- Цель --}}
                        <div class="mb-8">
                            <div class="text-sm font-semibold text-gray-900 mb-3">
                                Какая ваша главная цель? <span class="text-red-500">*</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" id="goalGroup">
                                @php
                                    $goalIcons = [
                                        'lose_weight'  => ['icon' => 'trending-down', 'bg' => '#fee2e2', 'fg' => '#dc2626'],
                                        'gain_weight'  => ['icon' => 'trending-up',   'bg' => '#dbeafe', 'fg' => '#2563eb'],
                                        'maintain'     => ['icon' => 'scale',          'bg' => '#dcfce7', 'fg' => '#16a34a'],
                                        'eat_healthy'  => ['icon' => 'apple',          'bg' => '#fef3c7', 'fg' => '#d97706'],
                                        'save_time'    => ['icon' => 'clock',          'bg' => '#f3e8ff', 'fg' => '#9333ea'],
                                    ];
                                @endphp
                                @foreach($goals as $value => $label)
                                    <label class="ob-option">
                                        <input type="radio" name="goal" value="{{ $value }}" {{ old('goal') === $value ? 'checked' : '' }}>
                                        <div class="ob-radio-box {{ old('goal') === $value ? 'is-selected' : '' }}">
                                            <div class="ob-icon-box" style="background:{{ $goalIcons[$value]['bg'] }}">
                                                <i data-lucide="{{ $goalIcons[$value]['icon'] }}" class="w-4 h-4" style="color:{{ $goalIcons[$value]['fg'] }}"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('goal')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Диетические предпочтения --}}
                        <div class="mb-8">
                            <div class="text-sm font-semibold text-gray-900 mb-3">
                                Есть ли у вас пищевые ограничения?
                            </div>
                            <div class="flex flex-wrap gap-2" id="dietGroup">
                                @foreach($dietaryOptions as $value => $label)
                                    <label class="ob-option">
                                        <input type="checkbox" name="dietary_preferences[]" value="{{ $value }}"
                                               {{ is_array(old('dietary_preferences')) && in_array($value, old('dietary_preferences')) ? 'checked' : '' }}>
                                        <span class="ob-chip {{ is_array(old('dietary_preferences')) && in_array($value, old('dietary_preferences')) ? 'is-selected' : '' }}">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Аллергии --}}
                        <div class="mb-8">
                            <div class="text-sm font-semibold text-gray-900 mb-3">
                                Есть ли у вас аллергии?
                            </div>
                            <div class="flex flex-wrap gap-2" id="allergyGroup">
                                @foreach($allergyOptions as $value => $label)
                                    <label class="ob-option">
                                        <input type="checkbox" name="allergies[]" value="{{ $value }}"
                                               {{ is_array(old('allergies')) && in_array($value, old('allergies')) ? 'checked' : '' }}>
                                        <span class="ob-chip {{ is_array(old('allergies')) && in_array($value, old('allergies')) ? 'is-selected' : '' }}">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Кнопки --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <a href="{{ route('onboarding.welcome') }}"
                               class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                Назад
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-8 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition shadow-lg shadow-green-500/25 hover:shadow-green-500/40">
                                Получить бесплатный доступ
                                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Радио-кнопки (цели) — только одна выбрана
        document.getElementById('goalGroup').addEventListener('click', function(e) {
            var label = e.target.closest('.ob-option');
            if (!label) return;
            var boxes = this.querySelectorAll('.ob-radio-box');
            boxes.forEach(function(b) { b.classList.remove('is-selected'); });
            label.querySelector('.ob-radio-box').classList.add('is-selected');
        });

        // Чекбоксы (чипсы) — множественный выбор
        function initChipGroup(groupId) {
            var group = document.getElementById(groupId);
            if (!group) return;
            group.addEventListener('click', function(e) {
                var label = e.target.closest('.ob-option');
                if (!label) return;
                var chip = label.querySelector('.ob-chip');
                var input = label.querySelector('input');
                // setTimeout чтобы input уже обновился
                setTimeout(function() {
                    if (input.checked) {
                        chip.classList.add('is-selected');
                    } else {
                        chip.classList.remove('is-selected');
                    }
                }, 0);
            });
        }
        initChipGroup('dietGroup');
        initChipGroup('allergyGroup');
    });
    </script>
</x-app-layout>
