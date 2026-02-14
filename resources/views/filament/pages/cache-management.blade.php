<x-filament-panels::page>
    @php
        $stats = $this->getRedisStats();
        $cacheKeys = $this->getCacheKeys();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Redis Status --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                @if($stats['connected'])
                    <span class="inline-block w-3 h-3 rounded-full bg-green-500"></span>
                    Redis подключён
                @else
                    <span class="inline-block w-3 h-3 rounded-full bg-red-500"></span>
                    Redis недоступен
                @endif
            </h3>

            @if($stats['connected'])
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Память</dt>
                        <dd class="font-semibold text-lg">{{ $stats['used_memory'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Пик памяти</dt>
                        <dd class="font-semibold text-lg">{{ $stats['peak_memory'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Ключей в Redis</dt>
                        <dd class="font-semibold text-lg">{{ $stats['total_keys'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Ops/sec</dt>
                        <dd class="font-semibold text-lg">{{ $stats['ops_per_sec'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Uptime</dt>
                        <dd class="font-semibold text-lg">{{ $stats['uptime_days'] }} дн.</dd>
                    </div>
                </dl>
            @else
                <p class="text-red-500">{{ $stats['error'] ?? 'Не удалось подключиться' }}</p>
            @endif
        </div>

        {{-- Hit Rate --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <h3 class="text-lg font-semibold mb-4">Эффективность кеша</h3>

            @if($stats['connected'])
                @php
                    $hitRateNum = (float) $stats['hit_rate'];
                    $barColor = $hitRateNum >= 80 ? 'bg-green-500' : ($hitRateNum >= 50 ? 'bg-yellow-500' : 'bg-red-500');
                @endphp

                <div class="mb-4">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Hit Rate</span>
                        <span class="font-bold text-2xl">{{ $stats['hit_rate'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="{{ $barColor }} h-3 rounded-full transition-all" style="width: {{ min($hitRateNum, 100) }}%"></div>
                    </div>
                </div>

                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Попаданий (hits)</dt>
                        <dd class="font-semibold text-green-600 dark:text-green-400 text-lg">{{ $stats['hits'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Промахов (misses)</dt>
                        <dd class="font-semibold text-red-600 dark:text-red-400 text-lg">{{ $stats['misses'] }}</dd>
                    </div>
                </dl>
            @endif
        </div>
    </div>

    {{-- Cache Keys Reference --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6 mt-6">
        <h3 class="text-lg font-semibold mb-4">Кешируемые данные</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-2 px-3 font-medium text-gray-500 dark:text-gray-400">Ключ</th>
                        <th class="text-left py-2 px-3 font-medium text-gray-500 dark:text-gray-400">TTL</th>
                        <th class="text-left py-2 px-3 font-medium text-gray-500 dark:text-gray-400">Описание</th>
                        <th class="text-left py-2 px-3 font-medium text-gray-500 dark:text-gray-400">Статус</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cacheKeys as $item)
                        @php
                            $exists = !str_contains($item['key'], '{') && Cache::has($item['key']);
                        @endphp
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-2 px-3 font-mono text-xs">{{ $item['key'] }}</td>
                            <td class="py-2 px-3">{{ $item['ttl'] }}</td>
                            <td class="py-2 px-3">{{ $item['description'] }}</td>
                            <td class="py-2 px-3">
                                @if(str_contains($item['key'], '{'))
                                    <span class="inline-flex items-center gap-1 text-gray-400 text-xs">
                                        <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                        динамический
                                    </span>
                                @elseif($exists)
                                    <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400 text-xs">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                        в кеше
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-gray-400 text-xs">
                                        <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                        не загружен
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Drivers Info --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6 mt-6">
        <h3 class="text-lg font-semibold mb-4">Конфигурация</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-green-100 dark:bg-green-900">
                    <x-heroicon-o-server-stack class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Кеш</dt>
                    <dd class="font-semibold">{{ config('cache.default') }}</dd>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                    <x-heroicon-o-finger-print class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Сессии</dt>
                    <dd class="font-semibold">{{ config('session.driver') }}</dd>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900">
                    <x-heroicon-o-queue-list class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Очереди</dt>
                    <dd class="font-semibold">{{ config('queue.default') }}</dd>
                </div>
            </div>
        </dl>
    </div>
</x-filament-panels::page>
