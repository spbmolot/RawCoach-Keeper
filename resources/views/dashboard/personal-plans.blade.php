<x-app-layout>
    <div class="max-w-7xl mx-auto">
        {{-- Заголовок --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Персональные планы</h1>
                </div>
                <p class="text-gray-600">Индивидуальные планы питания от нутрициолога</p>
            </div>
            <a href="{{ route('personal-plans.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-green-500 text-white rounded-xl font-medium hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                <i data-lucide="plus" class="w-5 h-5"></i>
                Заказать план
            </a>
        </div>

        @if($personalPlans->count() > 0)
            <div class="space-y-4">
                @foreach($personalPlans as $plan)
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'in_progress' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $statusLabels = [
                            'pending' => 'Ожидает',
                            'in_progress' => 'В работе',
                            'completed' => 'Готов',
                            'cancelled' => 'Отменён',
                        ];
                    @endphp
                    <a href="{{ route('personal-plans.show', $plan) }}" class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:border-green-200 transition group">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="clipboard-list" class="w-7 h-7 text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 group-hover:text-green-600 transition">
                                        Персональный план #{{ $plan->id }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Создан {{ $plan->created_at->format('d.m.Y') }}
                                        @if($plan->nutritionist)
                                            • Нутрициолог: {{ $plan->nutritionist->name }}
                                        @endif
                                    </p>
                                    @if($plan->questionnaire)
                                        <div class="flex flex-wrap gap-3 mt-3 text-sm text-gray-600">
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="flame" class="w-4 h-4 text-orange-400"></i>
                                                {{ $plan->questionnaire->target_calories ?? '—' }} ккал
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$plan->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$plan->status] ?? $plan->status }}
                                </span>
                                <i data-lucide="chevron-right" class="w-5 h-5 text-gray-400 group-hover:text-green-500 transition"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $personalPlans->links() }}
            </div>
        @else
            {{-- Пустое состояние --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-green-100 to-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="clipboard-list" class="w-12 h-12 text-green-500"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Персональный план питания</h3>
                <p class="text-gray-600 mb-8 max-w-lg mx-auto">
                    Получите индивидуальный план питания, составленный профессиональным нутрициологом 
                    с учётом ваших целей, предпочтений и особенностей организма
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-2xl mx-auto mb-8">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="user-check" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <h4 class="font-medium text-gray-900 mb-1">Персональный подход</h4>
                        <p class="text-sm text-gray-500">Учёт ваших целей и особенностей</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="calculator" class="w-6 h-6 text-green-600"></i>
                        </div>
                        <h4 class="font-medium text-gray-900 mb-1">Точный расчёт КБЖУ</h4>
                        <p class="text-sm text-gray-500">По формуле Миффлина-Сан Жеора</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="message-circle" class="w-6 h-6 text-purple-600"></i>
                        </div>
                        <h4 class="font-medium text-gray-900 mb-1">Поддержка нутрициолога</h4>
                        <p class="text-sm text-gray-500">Консультации и корректировки</p>
                    </div>
                </div>
                <a href="{{ route('personal-plans.create') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                    Заказать персональный план
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
