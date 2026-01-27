{{-- ============================================= --}}
{{-- ЕДИНЫЙ КОМПОНЕНТ ФУТЕРА                      --}}
{{-- Используется на всех страницах               --}}
{{-- ============================================= --}}

@php
    $user = auth()->user();
    $isGuest = !$user;
    $isSubscriber = $user && $user->hasActiveSubscription();
@endphp

<footer class="bg-gray-900 text-gray-400 py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 sm:gap-12 mb-8 sm:mb-12">
            {{-- Логотип и описание --}}
            <div class="col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 rounded-xl hero-gradient flex items-center justify-center">
                        <i data-lucide="salad" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-white">RawPlan</span>
                </a>
                <p class="text-gray-400 text-sm sm:text-base max-w-sm mb-4">
                    Готовые планы питания для здорового похудения. Меню, рецепты и списки покупок.
                </p>
                {{-- Социальные сети --}}
                <div class="flex items-center gap-3">
                    <a href="https://t.me/rawplan" target="_blank" rel="noopener" 
                       class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gray-700 transition" 
                       title="Telegram">
                        <i data-lucide="send" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </a>
                    <a href="mailto:support@rawplan.ru" 
                       class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gray-700 transition" 
                       title="Email">
                        <i data-lucide="mail" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </a>
                </div>
            </div>
            
            {{-- Навигация --}}
            <div>
                <h4 class="text-white font-semibold mb-3 sm:mb-4 text-sm sm:text-base">Навигация</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}#features" class="hover:text-white transition">Возможности</a></li>
                    <li><a href="{{ route('plans.index') }}" class="hover:text-white transition">Тарифы</a></li>
                    @if($isGuest)
                        <li><a href="{{ route('about') }}" class="hover:text-white transition">О нас</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">Контакты</a></li>
                    @else
                        <li><a href="{{ route('menus.index') }}" class="hover:text-white transition">Меню</a></li>
                        <li><a href="{{ route('recipes.index') }}" class="hover:text-white transition">Рецепты</a></li>
                        @if($isSubscriber)
                            <li><a href="{{ route('shopping-list.index') }}" class="hover:text-white transition">Покупки</a></li>
                        @endif
                    @endif
                </ul>
            </div>
            
            {{-- Правовое --}}
            <div>
                <h4 class="text-white font-semibold mb-3 sm:mb-4 text-sm sm:text-base">Правовое</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('privacy') }}" class="hover:text-white transition">Конфиденциальность</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-white transition">Соглашение</a></li>
                    <li><a href="{{ route('offer') }}" class="hover:text-white transition">Оферта</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white transition">Контакты</a></li>
                </ul>
            </div>
        </div>
        
        {{-- Платёжные системы --}}
        <div class="border-t border-gray-800 pt-6 sm:pt-8 mb-6 sm:mb-8">
            <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6">
                <div class="flex items-center gap-2 text-gray-500">
                    <i data-lucide="shield-check" class="w-4 h-4 sm:w-5 sm:h-5 text-green-500"></i>
                    <span class="text-xs sm:text-sm">Безопасная оплата</span>
                </div>
                <div class="flex items-center gap-2 sm:gap-4">
                    <div class="bg-gray-800 px-2 sm:px-3 py-1 sm:py-1.5 rounded-lg">
                        <span class="text-[10px] sm:text-xs font-bold text-blue-400">VISA</span>
                    </div>
                    <div class="bg-gray-800 px-2 sm:px-3 py-1 sm:py-1.5 rounded-lg">
                        <span class="text-[10px] sm:text-xs font-bold text-orange-400">MC</span>
                    </div>
                    <div class="bg-gray-800 px-2 sm:px-3 py-1 sm:py-1.5 rounded-lg">
                        <span class="text-[10px] sm:text-xs font-bold text-green-400">МИР</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-gray-500">
                    <i data-lucide="lock" class="w-3 h-3 sm:w-4 sm:h-4"></i>
                    <span class="text-xs sm:text-sm">SSL</span>
                </div>
            </div>
        </div>
        
        {{-- Копирайт --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <p class="text-xs sm:text-sm">© {{ date('Y') }} RawPlan. Все права защищены.</p>
            <p class="text-xs text-gray-500">
                Сделано с <i data-lucide="heart" class="w-3 h-3 inline text-red-500"></i> для здорового питания
            </p>
        </div>
    </div>
</footer>
