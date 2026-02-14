<x-app-layout>
    <div class="max-w-5xl mx-auto" x-data="referralPage()">
        {{-- Заголовок --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 sm:mb-8">
            <div>
                <div class="flex items-center gap-2 sm:gap-3 mb-1">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
                        <i data-lucide="arrow-left" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </a>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Пригласи друга</h1>
                </div>
                <p class="text-gray-500 text-sm sm:text-base">Приглашайте друзей и получайте бесплатные дни подписки</p>
            </div>
        </div>

        {{-- Как это работает --}}
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-5 sm:p-8 mb-6 sm:mb-8 text-white shadow-xl shadow-green-500/20">
            <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6">Как это работает?</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                <div class="flex gap-3 sm:block">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 sm:mb-3">
                        <i data-lucide="share-2" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-1">1. Поделитесь ссылкой</h3>
                        <p class="text-green-100 text-sm">Отправьте персональную ссылку другу</p>
                    </div>
                </div>
                <div class="flex gap-3 sm:block">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 sm:mb-3">
                        <i data-lucide="user-plus" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-1">2. Друг подписывается</h3>
                        <p class="text-green-100 text-sm">Друг получает <strong>15% скидку</strong> на первую подписку</p>
                    </div>
                </div>
                <div class="flex gap-3 sm:block">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 sm:mb-3">
                        <i data-lucide="gift" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-1">3. Вы получаете бонус</h3>
                        <p class="text-green-100 text-sm"><strong>+7 дней</strong> к подписке за каждого друга</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ваша ссылка --}}
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
            <h3 class="font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
                <i data-lucide="link" class="w-5 h-5 text-green-500"></i>
                Ваша реферальная ссылка
            </h3>
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input 
                        type="text" 
                        readonly 
                        value="{{ $stats['referral_url'] }}" 
                        id="referralUrl"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 pr-12"
                    >
                    <button 
                        @click="copyUrl()" 
                        class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-green-600 transition"
                        :title="copied ? 'Скопировано!' : 'Копировать'"
                    >
                        <i data-lucide="copy" class="w-4 h-4" x-show="!copied"></i>
                        <i data-lucide="check" class="w-4 h-4 text-green-500" x-show="copied" x-cloak></i>
                    </button>
                </div>
                <div class="flex gap-2">
                    <a :href="'https://t.me/share/url?url=' + encodeURIComponent('{{ $stats['referral_url'] }}') + '&text=' + encodeURIComponent('Присоединяйся к RawPlan — сервису здорового питания! По моей ссылке скидка 15% на первую подписку 🥗')" 
                       target="_blank" 
                       class="flex items-center justify-center gap-2 px-4 py-3 bg-[#0088cc] text-white rounded-xl text-sm font-medium hover:bg-[#0077b5] transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                        <span class="hidden sm:inline">Telegram</span>
                    </a>
                    <a :href="'https://wa.me/?text=' + encodeURIComponent('Присоединяйся к RawPlan — сервису здорового питания! По моей ссылке скидка 15% 🥗 {{ $stats['referral_url'] }}')" 
                       target="_blank" 
                       class="flex items-center justify-center gap-2 px-4 py-3 bg-[#25D366] text-white rounded-xl text-sm font-medium hover:bg-[#20bd5a] transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <span class="hidden sm:inline">WhatsApp</span>
                    </a>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">Ваш код: <span class="font-mono font-bold text-gray-600">{{ $stats['referral_code'] }}</span></p>
        </div>

        {{-- Статистика --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $stats['total_invited'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Приглашено</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 text-center">
                <div class="text-2xl font-bold text-yellow-500">{{ $stats['registered'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Ожидают оплаты</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 text-center">
                <div class="text-2xl font-bold text-green-500">{{ $stats['rewarded'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Оплатили</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 text-center">
                <div class="text-2xl font-bold text-blue-500">+{{ $stats['total_days_earned'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Дней заработано</div>
            </div>
        </div>

        {{-- Вехи --}}
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i data-lucide="target" class="w-5 h-5 text-green-500"></i>
                Бонусные вехи
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                @php
                    $rewarded = $stats['rewarded'];
                    $milestones = [
                        ['threshold' => 3, 'days' => 7, 'label' => '3 друга'],
                        ['threshold' => 5, 'days' => 14, 'label' => '5 друзей'],
                        ['threshold' => 10, 'days' => 30, 'label' => '10 друзей'],
                    ];
                @endphp
                @foreach($milestones as $ms)
                    @php $reached = $rewarded >= $ms['threshold']; @endphp
                    <div class="flex items-center gap-3 p-3 rounded-xl {{ $reached ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $reached ? 'bg-green-100' : 'bg-gray-200' }}">
                            @if($reached)
                                <i data-lucide="check" class="w-5 h-5 text-green-600"></i>
                            @else
                                <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-medium text-sm {{ $reached ? 'text-green-700' : 'text-gray-500' }}">{{ $ms['label'] }}</p>
                            <p class="text-xs {{ $reached ? 'text-green-600' : 'text-gray-400' }}">+{{ $ms['days'] }} дней бонус</p>
                        </div>
                        @if(!$reached)
                            <div class="ml-auto text-xs text-gray-400 font-medium">{{ $rewarded }}/{{ $ms['threshold'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Приглашённые друзья --}}
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4 sm:mb-6">
            <div class="p-4 sm:p-6 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                    <i data-lucide="users" class="w-5 h-5 text-green-500"></i>
                    Приглашённые друзья
                </h3>
            </div>
            @if($referrals->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($referrals as $referral)
                        <div class="flex items-center justify-between p-3 sm:p-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="user" class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 text-sm truncate">{{ $referral->referred->name ?? 'Пользователь' }}</p>
                                    <p class="text-xs text-gray-400">{{ $referral->created_at->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium flex-shrink-0
                                {{ $referral->status_color === 'green' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $referral->status_color === 'yellow' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $referral->status_color === 'blue' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $referral->status_color === 'gray' ? 'bg-gray-100 text-gray-500' : '' }}
                            ">
                                {{ $referral->status_label }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 sm:p-8 text-center text-gray-400">
                    <i data-lucide="user-plus" class="w-10 h-10 mx-auto mb-2 opacity-50"></i>
                    <p class="text-sm">Пока никого не пригласили</p>
                    <p class="text-xs mt-1">Поделитесь ссылкой, чтобы начать</p>
                </div>
            @endif
        </div>

        {{-- История наград --}}
        @if($rewards->count() > 0)
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i data-lucide="gift" class="w-5 h-5 text-green-500"></i>
                        История наград
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($rewards as $reward)
                        <div class="flex items-center justify-between p-3 sm:p-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="plus" class="w-4 h-4 text-green-600"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm text-gray-700 truncate">{{ $reward->description }}</p>
                                    <p class="text-xs text-gray-400">{{ $reward->created_at->translatedFormat('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            @if($reward->days_added > 0)
                                <span class="text-sm font-bold text-green-600 flex-shrink-0">+{{ $reward->days_added }} дн.</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function referralPage() {
            return {
                copied: false,

                copyUrl() {
                    const input = document.getElementById('referralUrl');
                    navigator.clipboard.writeText(input.value).then(() => {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    }).catch(() => {
                        input.select();
                        document.execCommand('copy');
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    });
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
