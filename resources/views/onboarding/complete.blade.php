<x-app-layout>
    @section('title', 'Всё готово! — RawPlan')

    <div class="min-h-[80vh] flex items-center justify-center px-4 py-8">
        <div class="max-w-2xl w-full">
            {{-- Прогресс-бар --}}
            <div class="flex items-center justify-center gap-2 mb-8">
                <div class="w-10 h-1.5 rounded-full bg-green-500"></div>
                <div class="w-10 h-1.5 rounded-full bg-green-500"></div>
                <div class="w-10 h-1.5 rounded-full bg-green-500"></div>
                <span class="ml-2 text-xs text-gray-400">Готово!</span>
            </div>

            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                {{-- Успех --}}
                <div class="px-6 sm:px-10 pt-10 pb-6 text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5 animate-bounce-slow">
                        <i data-lucide="check-circle-2" class="w-10 h-10 text-green-500"></i>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Всё готово! 🎉</h1>

                    @if($trialActivated)
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-200 rounded-full mb-4">
                            <i data-lucide="gift" class="w-4 h-4 text-green-600"></i>
                            <span class="text-sm font-medium text-green-700">
                                7 дней бесплатного доступа активировано
                            </span>
                        </div>
                        @if($subscription)
                            <p class="text-gray-500 text-sm">
                                Пробный период действует до <strong>{{ $subscription->ends_at->format('d.m.Y') }}</strong>
                            </p>
                        @endif
                    @else
                        <p class="text-gray-500">Ваш профиль настроен. Выберите план для полного доступа.</p>
                    @endif
                </div>

                {{-- Демо-меню на 1 день --}}
                @if($demoDay && $demoDay->meals->count() > 0)
                    <div class="px-6 sm:px-10 pb-6">
                        <div class="bg-gray-50 rounded-2xl p-5">
                            <div class="flex items-center gap-2 mb-4">
                                <i data-lucide="utensils" class="w-5 h-5 text-green-500"></i>
                                <h3 class="font-semibold text-gray-900">Ваше меню на сегодня</h3>
                            </div>

                            <div class="space-y-3">
                                @php
                                    $mealTypes = [
                                        'breakfast' => ['label' => 'Завтрак', 'icon' => 'sun', 'color' => 'amber'],
                                        'lunch' => ['label' => 'Обед', 'icon' => 'sun-medium', 'color' => 'orange'],
                                        'dinner' => ['label' => 'Ужин', 'icon' => 'moon', 'color' => 'indigo'],
                                        'snack' => ['label' => 'Перекус', 'icon' => 'apple', 'color' => 'green'],
                                    ];
                                    $groupedMeals = $demoDay->meals->groupBy('meal_type');
                                @endphp

                                @foreach($mealTypes as $type => $meta)
                                    @if($groupedMeals->has($type))
                                        @foreach($groupedMeals[$type] as $meal)
                                            @if($meal->recipe)
                                                <a href="{{ route('recipes.show', $meal->recipe) }}" class="flex items-center gap-3 bg-white rounded-xl p-3 shadow-sm hover:shadow-md transition group">
                                                    <div class="w-10 h-10 bg-{{ $meta['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                        <i data-lucide="{{ $meta['icon'] }}" class="w-5 h-5 text-{{ $meta['color'] }}-600"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">{{ $meta['label'] }}</p>
                                                        <p class="text-sm font-medium text-gray-900 truncate group-hover:text-green-600 transition">{{ $meal->recipe->title }}</p>
                                                    </div>
                                                    @if($meal->recipe->calories)
                                                        <span class="text-xs text-gray-400 flex-shrink-0">{{ $meal->recipe->calories }} ккал</span>
                                                    @endif
                                                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 flex-shrink-0 group-hover:text-green-500 transition"></i>
                                                </a>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>

                            {{-- Итого калорий --}}
                            @php
                                $totalCalories = $demoDay->meals->sum(fn($m) => $m->recipe?->calories ?? 0);
                            @endphp
                            @if($totalCalories > 0)
                                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-200">
                                    <span class="text-sm text-gray-500">Итого за день:</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $totalCalories }} ккал</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Кнопки --}}
                <div class="px-6 sm:px-10 pb-8">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('dashboard') }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition shadow-lg shadow-green-500/25">
                            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                            Перейти в личный кабинет
                        </a>
                        @if(!$trialActivated)
                            <a href="{{ route('plans.index') }}"
                               class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white border-2 border-green-500 text-green-600 hover:bg-green-50 rounded-xl font-semibold transition">
                                <i data-lucide="crown" class="w-5 h-5"></i>
                                Выбрать план
                            </a>
                        @else
                            <a href="{{ route('dashboard.today') }}"
                               class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-50 rounded-xl font-semibold transition">
                                <i data-lucide="utensils" class="w-5 h-5"></i>
                                Меню на сегодня
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .animate-bounce-slow {
            animation: bounce-slow 2s ease-in-out infinite;
        }
    </style>
    @endpush
</x-app-layout>
