<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 sm:mb-8 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Рекламные кампании</h1>
                <p class="text-gray-600 mt-1 text-sm">Управление вашими рекламными кампаниями</p>
            </div>
            <a href="{{ route('ad-campaigns.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition shadow-lg shadow-green-500/25 text-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Создать кампанию
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        {{-- Фильтры --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <select name="status" class="rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
                    <option value="">Все статусы</option>
                    @foreach(['pending' => 'На модерации', 'active' => 'Активные', 'paused' => 'Приостановлены', 'completed' => 'Завершены'] as $val => $lbl)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">
                    Фильтровать
                </button>
                @if(request()->hasAny(['status', 'date_from', 'date_to']))
                    <a href="{{ route('ad-campaigns.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Сбросить</a>
                @endif
            </form>
        </div>

        @if($campaigns->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12 text-center">
                <div class="w-20 h-20 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="megaphone" class="w-10 h-10 text-indigo-500"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Нет рекламных кампаний</h2>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">Создайте первую рекламную кампанию для продвижения на платформе RawPlan</p>
                <a href="{{ route('ad-campaigns.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition">
                    <i data-lucide="plus" class="w-5 h-5"></i> Создать кампанию
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($campaigns as $campaign)
                    @php
                        $statusColors = ['pending' => 'bg-yellow-100 text-yellow-700', 'active' => 'bg-green-100 text-green-700', 'paused' => 'bg-gray-100 text-gray-700', 'completed' => 'bg-blue-100 text-blue-700'];
                        $statusLabels = ['pending' => 'На модерации', 'active' => 'Активна', 'paused' => 'Приостановлена', 'completed' => 'Завершена'];
                    @endphp
                    <a href="{{ route('ad-campaigns.show', $campaign) }}" class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 hover:shadow-lg transition-all duration-300 hover:border-indigo-200">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-semibold text-gray-900 truncate">{{ $campaign->name }}</h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium flex-shrink-0 {{ $statusColors[$campaign->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $statusLabels[$campaign->status] ?? $campaign->status }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                                        {{ number_format($campaign->budget, 0, ',', ' ') }} ₽
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                        {{ \Carbon\Carbon::parse($campaign->starts_at)->format('d.m.Y') }} — {{ \Carbon\Carbon::parse($campaign->ends_at)->format('d.m.Y') }}
                                    </span>
                                    @if($campaign->creatives_count ?? $campaign->creatives->count())
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="image" class="w-3.5 h-3.5"></i>
                                            {{ $campaign->creatives->count() }} креативов
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- Прогресс бюджета --}}
                            <div class="flex-shrink-0 w-full sm:w-32">
                                @php $spentPercent = $campaign->budget > 0 ? min(100, ($campaign->spent_budget / $campaign->budget) * 100) : 0; @endphp
                                <div class="text-xs text-gray-500 mb-1 text-right">{{ round($spentPercent) }}% бюджета</div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-indigo-500 h-2 rounded-full transition-all" style="width: {{ $spentPercent }}%"></div>
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="w-5 h-5 text-gray-400 flex-shrink-0 hidden sm:block"></i>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">{{ $campaigns->withQueryString()->links() }}</div>
        @endif
    </div>
</x-app-layout>
