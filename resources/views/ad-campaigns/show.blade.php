<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('ad-campaigns.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Назад к кампаниям
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        @php
            $statusColors = ['pending' => 'bg-yellow-100 text-yellow-700', 'active' => 'bg-green-100 text-green-700', 'paused' => 'bg-gray-100 text-gray-700', 'completed' => 'bg-blue-100 text-blue-700'];
            $statusLabels = ['pending' => 'На модерации', 'active' => 'Активна', 'paused' => 'Приостановлена', 'completed' => 'Завершена'];
        @endphp

        {{-- Заголовок --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $adCampaign->name }}</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$adCampaign->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $statusLabels[$adCampaign->status] ?? $adCampaign->status }}
                    </span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if(in_array($adCampaign->status, ['pending', 'paused']))
                        <a href="{{ route('ad-campaigns.edit', $adCampaign) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-sm font-medium transition">
                            <i data-lucide="pencil" class="w-4 h-4"></i> Редактировать
                        </a>
                    @endif
                    @if($adCampaign->status === 'active')
                        <form action="{{ route('ad-campaigns.pause', $adCampaign) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-xl text-sm font-medium transition">
                                <i data-lucide="pause" class="w-4 h-4"></i> Приостановить
                            </button>
                        </form>
                    @endif
                    @if($adCampaign->status === 'paused')
                        <form action="{{ route('ad-campaigns.resume', $adCampaign) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-medium transition">
                                <i data-lucide="play" class="w-4 h-4"></i> Возобновить
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('ad-campaigns.stats', $adCampaign) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl text-sm font-medium transition">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Статистика
                    </a>
                    <a href="{{ route('ad-campaigns.creatives', $adCampaign) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-xl text-sm font-medium transition">
                        <i data-lucide="image" class="w-4 h-4"></i> Креативы
                    </a>
                    @if(!in_array($adCampaign->status, ['active']))
                        <form action="{{ route('ad-campaigns.destroy', $adCampaign) }}" method="POST" onsubmit="return confirm('Удалить кампанию?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-xl text-sm font-medium transition">
                                <i data-lucide="trash-2" class="w-4 h-4"></i> Удалить
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Статистика --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Показы</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($stats['impressions']) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Клики</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($stats['clicks']) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">CTR</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['ctr'] }}%</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Потрачено</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($stats['spent'], 0, ',', ' ') }} ₽</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Остаток</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($stats['remaining'], 0, ',', ' ') }} ₽</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Дней осталось</p>
                <p class="text-xl font-bold text-gray-900">{{ max(0, $stats['days_left']) }}</p>
            </div>
        </div>

        {{-- Детали --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i data-lucide="info" class="w-5 h-5 text-blue-500"></i> Детали кампании
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-xl">
                    <span class="text-xs text-gray-500 block mb-1">Бюджет</span>
                    <span class="font-medium text-gray-900">{{ number_format($adCampaign->budget, 0, ',', ' ') }} ₽</span>
                </div>
                @if($adCampaign->daily_budget)
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <span class="text-xs text-gray-500 block mb-1">Дневной бюджет</span>
                        <span class="font-medium text-gray-900">{{ number_format($adCampaign->daily_budget, 0, ',', ' ') }} ₽</span>
                    </div>
                @endif
                <div class="p-3 bg-gray-50 rounded-xl">
                    <span class="text-xs text-gray-500 block mb-1">Период</span>
                    <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($adCampaign->starts_at)->format('d.m.Y') }} — {{ \Carbon\Carbon::parse($adCampaign->ends_at)->format('d.m.Y') }}</span>
                </div>
                <div class="p-3 bg-gray-50 rounded-xl">
                    <span class="text-xs text-gray-500 block mb-1">Создана</span>
                    <span class="font-medium text-gray-900">{{ $adCampaign->created_at->format('d.m.Y H:i') }}</span>
                </div>
            </div>

            @if($adCampaign->description)
                <div class="mt-4">
                    <span class="text-xs text-gray-500 block mb-1">Описание</span>
                    <p class="text-gray-700 text-sm">{{ $adCampaign->description }}</p>
                </div>
            @endif
        </div>

        {{-- Размещения --}}
        @if($adCampaign->placements && $adCampaign->placements->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="layout" class="w-5 h-5 text-purple-500"></i> Размещения
                </h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($adCampaign->placements as $placement)
                        <span class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium">{{ $placement->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Креативы --}}
        @if($adCampaign->creatives && $adCampaign->creatives->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i data-lucide="image" class="w-5 h-5 text-indigo-500"></i> Креативы ({{ $adCampaign->creatives->count() }})
                    </h2>
                    <a href="{{ route('ad-campaigns.creatives', $adCampaign) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Управление →</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($adCampaign->creatives->take(6) as $creative)
                        <div class="border border-gray-200 rounded-xl p-3">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $creative->name }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($creative->type) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
