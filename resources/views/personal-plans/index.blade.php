<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 sm:mb-8 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Персональные планы</h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Индивидуальные планы питания от нутрициолога</p>
            </div>
            <a href="{{ route('personal-plans.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition shadow-lg shadow-green-500/25 text-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Создать план
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($personalPlans->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12 text-center">
                <div class="w-20 h-20 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="clipboard-list" class="w-10 h-10 text-purple-500"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">У вас пока нет персональных планов</h2>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Заполните анкету, и наш нутрициолог составит для вас индивидуальный план питания
                </p>
                <a href="{{ route('personal-plans.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    Создать первый план
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($personalPlans as $plan)
                    <a href="{{ route('personal-plans.show', $plan) }}" class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 hover:shadow-lg transition-all duration-300 hover:border-green-200">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex-shrink-0">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-600',
                                        'in_progress' => 'bg-blue-100 text-blue-600',
                                        'completed' => 'bg-green-100 text-green-600',
                                        'cancelled' => 'bg-red-100 text-red-600',
                                    ];
                                    $statusIcons = [
                                        'pending' => 'clock',
                                        'in_progress' => 'loader',
                                        'completed' => 'check-circle',
                                        'cancelled' => 'x-circle',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Ожидает',
                                        'in_progress' => 'В работе',
                                        'completed' => 'Готов',
                                        'cancelled' => 'Отменён',
                                    ];
                                @endphp
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $statusColors[$plan->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    <i data-lucide="{{ $statusIcons[$plan->status] ?? 'help-circle' }}" class="w-6 h-6"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-semibold text-gray-900">Персональный план #{{ $plan->id }}</h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$plan->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $statusLabels[$plan->status] ?? $plan->status }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500">
                                    @if($plan->questionnaire)
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="target" class="w-3.5 h-3.5"></i>
                                            {{ ['weight_loss' => 'Похудение', 'weight_gain' => 'Набор массы', 'maintenance' => 'Поддержание', 'muscle_gain' => 'Набор мышц'][$plan->questionnaire->goal] ?? $plan->questionnaire->goal }}
                                        </span>
                                    @endif
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                        {{ $plan->created_at->format('d.m.Y') }}
                                    </span>
                                    @if($plan->nutritionist)
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                            {{ $plan->nutritionist->name }}
                                        </span>
                                    @endif
                                    @if($plan->rating)
                                        <span class="flex items-center gap-1 text-amber-500">
                                            <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                            {{ $plan->rating }}/5
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="w-5 h-5 text-gray-400 flex-shrink-0 hidden sm:block"></i>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $personalPlans->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
