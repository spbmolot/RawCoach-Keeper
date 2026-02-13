<x-app-layout>
    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('ad-campaigns.show', $adCampaign) }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Назад к кампании
            </a>
        </div>

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Статистика: {{ $adCampaign->name }}</h1>
            <p class="text-gray-600 mt-1 text-sm">Данные за последние 30 дней</p>
        </div>

        {{-- Сводка --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            @php
                $totalImpressions = $dailyStats->sum('impressions');
                $totalClicks = $dailyStats->sum('clicks');
                $totalSpent = $dailyStats->sum('spent');
                $avgCtr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0;
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-gray-500 mb-1">Всего показов</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalImpressions) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-gray-500 mb-1">Всего кликов</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalClicks) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-gray-500 mb-1">Средний CTR</p>
                <p class="text-2xl font-bold text-gray-900">{{ $avgCtr }}%</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-gray-500 mb-1">Потрачено</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalSpent, 0, ',', ' ') }} ₽</p>
            </div>
        </div>

        {{-- Таблица по дням --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i data-lucide="table" class="w-5 h-5 text-indigo-500"></i> Статистика по дням
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Дата</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600">Показы</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600">Клики</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600">CTR</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600">Расход</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($dailyStats as $day)
                            @php $dayCtr = $day['impressions'] > 0 ? round(($day['clicks'] / $day['impressions']) * 100, 2) : 0; @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-900">{{ \Carbon\Carbon::parse($day['date'])->format('d.m.Y') }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($day['impressions']) }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($day['clicks']) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $dayCtr >= 3 ? 'bg-green-100 text-green-700' : ($dayCtr >= 1 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ $dayCtr }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($day['spent'], 0, ',', ' ') }} ₽</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
