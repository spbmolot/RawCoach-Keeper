@extends('layouts.public')

@section('title', 'Список покупок - ' . $menu->title . ' — RawPlan')
@section('description', 'Список покупок для меню ' . $menu->title)

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white; }
    }
</style>
@endpush

@php
    $storageKey = 'shoplist_' . $menu->id . '_' . $startDay . '_' . $endDay;
    $itemsJson = [];
    $flatIndex = 0;
    if (count($shoppingList) > 0) {
        foreach ($shoppingList as $category => $items) {
            foreach ($items as $item) {
                $itemsJson[] = ['name' => $item['name'], 'amount' => round($item['amount'], 1), 'unit' => $item['unit'], 'category' => $category];
                $flatIndex++;
            }
        }
    }
    $totalItems = $flatIndex;
@endphp

@section('content')
    <!-- Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="shoppingListPage({{ json_encode($itemsJson) }}, '{{ $storageKey }}')">
        
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 mb-6 no-print">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Список покупок</h1>
                    <p class="text-gray-500 text-sm sm:text-base">{{ $menu->title }} • Дни {{ $startDay }}-{{ $endDay }}</p>
                </div>
                <div class="flex gap-2 sm:gap-3 flex-wrap">
                    {{-- Шаринг --}}
                    <div class="relative" x-data="{ shareOpen: false }" @click.away="shareOpen = false">
                        <button @click="shareOpen = !shareOpen" class="px-3 sm:px-4 py-2 sm:py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition flex items-center gap-2 text-sm sm:text-base">
                            <i data-lucide="share-2" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                            <span class="hidden sm:inline">Поделиться</span>
                        </button>
                        <div x-show="shareOpen" x-transition.opacity class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50" x-cloak>
                            <button @click="shareWhatsApp(); shareOpen = false" class="w-full px-4 py-2.5 text-left text-sm hover:bg-gray-50 flex items-center gap-3 transition">
                                <svg class="w-5 h-5 text-green-500" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                WhatsApp
                            </button>
                            <button @click="shareTelegram(); shareOpen = false" class="w-full px-4 py-2.5 text-left text-sm hover:bg-gray-50 flex items-center gap-3 transition">
                                <svg class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                                Telegram
                            </button>
                            <button @click="shareMax(); shareOpen = false" class="w-full px-4 py-2.5 text-left text-sm hover:bg-gray-50 flex items-center gap-3 transition">
                                <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/></svg>
                                MAX
                            </button>
                            <div class="border-t border-gray-100 my-1"></div>
                            <button @click="copyList(); shareOpen = false" class="w-full px-4 py-2.5 text-left text-sm hover:bg-gray-50 flex items-center gap-3 transition">
                                <i data-lucide="copy" class="w-5 h-5 text-gray-400"></i>
                                <span x-text="copied ? 'Скопировано!' : 'Копировать текст'"></span>
                            </button>
                        </div>
                    </div>
                    <button onclick="window.print()" class="px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition flex items-center gap-2 text-sm sm:text-base">
                        <i data-lucide="printer" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        <span class="hidden sm:inline">Печать</span>
                    </button>
                    <a href="{{ route('shopping-list.pdf', ['menu' => $menu, 'start_day' => $startDay, 'end_day' => $endDay]) }}" class="px-3 sm:px-4 py-2 sm:py-2.5 bg-red-500 text-white rounded-xl font-medium hover:bg-red-600 transition flex items-center gap-2 text-sm sm:text-base">
                        <i data-lucide="file-text" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        <span class="hidden sm:inline">PDF</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Shopping List -->
        @if($totalItems > 0)
            {{-- Прогресс --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm p-4 sm:p-5 mb-4 sm:mb-6 no-print">
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl flex items-center justify-center transition-colors duration-300" :class="allChecked ? 'bg-green-100' : 'bg-gray-100'">
                            <i data-lucide="shopping-cart" class="w-4 h-4 sm:w-5 sm:h-5 transition-colors duration-300" :class="allChecked ? 'text-green-600' : 'text-gray-400'"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900 text-sm sm:text-base" x-text="checkedCount + ' из ' + totalCount + ' куплено'"></span>
                            <p class="text-xs text-gray-400" x-show="checkedCount > 0 && !allChecked" x-transition>осталось <span x-text="totalCount - checkedCount"></span></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm sm:text-base font-bold transition-colors duration-300" :class="allChecked ? 'text-green-600' : 'text-gray-900'" x-text="progressPercent + '%'"></span>
                        <button x-show="checkedCount > 0" x-transition @click="clearAll()" class="text-xs text-gray-400 hover:text-red-500 underline underline-offset-2 ml-2 transition no-print">
                            сбросить
                        </button>
                    </div>
                </div>
                <div class="h-2 sm:h-2.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 ease-out" :class="allChecked ? 'bg-green-500' : 'bg-green-400'" :style="'width: ' + progressPercent + '%'"></div>
                </div>
            </div>

            {{-- Карточка "Всё куплено!" --}}
            <div x-show="allChecked" x-transition.duration.500ms class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl sm:rounded-2xl shadow-sm p-5 sm:p-6 mb-4 sm:mb-6 text-center text-white no-print" x-cloak>
                <div class="flex flex-col items-center gap-2">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-7 h-7"></i>
                    </div>
                    <p class="font-bold text-lg">Всё куплено!</p>
                    <p class="text-sm text-white/80">Можно приступать к готовке</p>
                </div>
            </div>

            @php $idx = 0; @endphp
            <div class="space-y-4 sm:space-y-6">
                @foreach($shoppingList as $category => $items)
                    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm overflow-hidden">
                        <div class="bg-green-50 px-4 sm:px-6 py-3 sm:py-4 border-b border-green-100">
                            <h2 class="text-base sm:text-lg font-bold text-green-800 flex items-center gap-2">
                                <i data-lucide="tag" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                                {{ $category }}
                                <span class="text-xs sm:text-sm font-normal text-green-600">({{ count($items) }})</span>
                            </h2>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            @foreach($items as $item)
                                <li>
                                    <label class="flex items-center gap-3 sm:gap-4 px-4 sm:px-6 py-3 sm:py-4 hover:bg-gray-50 transition cursor-pointer" :class="checked[{{ $idx }}] ? 'bg-green-50/50' : ''">
                                        <input 
                                            type="checkbox" 
                                            class="w-4 h-4 sm:w-5 sm:h-5 rounded border-gray-300 text-green-500 focus:ring-green-500 transition"
                                            :checked="checked[{{ $idx }}]"
                                            @change="toggle({{ $idx }})"
                                        >
                                        <span class="flex-1 text-sm sm:text-base transition-all duration-200" :class="checked[{{ $idx }}] ? 'text-gray-400 line-through' : 'text-gray-900'">{{ $item['name'] }}</span>
                                        <span class="font-medium text-xs sm:text-sm flex-shrink-0 transition-all duration-200" :class="checked[{{ $idx }}] ? 'text-gray-300' : 'text-gray-600'">{{ round($item['amount'], 1) }} {{ $item['unit'] }}</span>
                                    </label>
                                </li>
                                @php $idx++; @endphp
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm p-8 sm:p-12 text-center">
                <i data-lucide="shopping-cart" class="w-12 h-12 sm:w-16 sm:h-16 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Список пуст</h3>
                <p class="text-gray-500 text-sm sm:text-base">В выбранном периоде нет рецептов с ингредиентами</p>
            </div>
        @endif
    </main>

@endsection

@if($totalItems > 0)
@push('scripts')
<script>
    function shoppingListPage(items, storageKey) {
        return {
            items: items,
            storageKey: storageKey,
            checked: Array(items.length).fill(false),
            copied: false,

            init() {
                const saved = localStorage.getItem(this.storageKey);
                if (saved) {
                    try {
                        const parsed = JSON.parse(saved);
                        if (Array.isArray(parsed) && parsed.length === this.items.length) {
                            this.checked = parsed;
                        }
                    } catch (e) {}
                }
            },

            get totalCount() { return this.items.length; },
            get checkedCount() { return this.checked.filter(Boolean).length; },
            get progressPercent() { return this.totalCount > 0 ? Math.round((this.checkedCount / this.totalCount) * 100) : 0; },
            get allChecked() { return this.checkedCount === this.totalCount && this.totalCount > 0; },

            toggle(index) {
                this.checked[index] = !this.checked[index];
                this.save();
            },

            clearAll() {
                this.checked = Array(this.items.length).fill(false);
                this.save();
            },

            save() {
                localStorage.setItem(this.storageKey, JSON.stringify(this.checked));
            },

            buildText() {
                let text = '🛒 Список покупок — RawPlan\n\n';
                let currentCategory = '';
                this.items.forEach((item, i) => {
                    if (item.category !== currentCategory) {
                        currentCategory = item.category;
                        text += '\n📌 ' + currentCategory + '\n';
                    }
                    const mark = this.checked[i] ? '✅' : '⬜';
                    text += mark + ' ' + item.name + ' — ' + item.amount + ' ' + item.unit + '\n';
                });
                return text.trim();
            },

            shareWhatsApp() {
                const text = this.buildText();
                window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(text), '_blank');
            },

            shareTelegram() {
                const text = this.buildText();
                window.open('https://t.me/share/url?url=' + encodeURIComponent(window.location.href) + '&text=' + encodeURIComponent(text), '_blank');
            },

            shareMax() {
                const text = this.buildText();
                window.open('https://max.ru/share?text=' + encodeURIComponent(text), '_blank');
            },

            copyList() {
                const text = this.buildText();
                navigator.clipboard.writeText(text).then(() => {
                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2000);
                });
            }
        };
    }
</script>
@endpush
@endif
