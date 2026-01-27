{{-- ============================================= --}}
{{-- ЕДИНЫЙ КОМПОНЕНТ ШАПКИ                       --}}
{{-- Параметры:                                    --}}
{{--   variant: 'landing' | 'public' | 'app'      --}}
{{--   activeNav: текущий активный пункт меню      --}}
{{-- ============================================= --}}

@props([
    'variant' => 'public',
    'activeNav' => null
])

@php
    $isLanding = $variant === 'landing';
    $isApp = $variant === 'app';
    $isPublic = $variant === 'public';
    
    $user = auth()->user();
    $isGuest = !$user;
    $isSubscriber = $user && $user->hasActiveSubscription();
    $isAdmin = $user && $user->hasRole('admin');
@endphp

<header 
    x-data="{ mobileMenuOpen: false }" 
    class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 shadow-sm"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0">
                <div class="w-10 h-10 rounded-xl hero-gradient flex items-center justify-center">
                    <i data-lucide="salad" class="w-6 h-6 text-white"></i>
                </div>
                <span class="text-xl font-bold text-gray-900 hidden sm:block">RawPlan</span>
            </a>
            
            {{-- Desktop Navigation --}}
            <nav class="hidden md:flex items-center gap-8">
                @if($isLanding)
                    {{-- Навигация для лендинга (якорные ссылки) --}}
                    <a href="#features" class="text-gray-600 hover:text-green-600 font-medium transition">Возможности</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-green-600 font-medium transition">Как это работает</a>
                    <a href="#pricing" class="text-gray-600 hover:text-green-600 font-medium transition">Тарифы</a>
                    <a href="#faq" class="text-gray-600 hover:text-green-600 font-medium transition">FAQ</a>
                @else
                    {{-- Навигация для остальных страниц --}}
                    @if($isGuest)
                        {{-- Гость --}}
                        <a href="{{ route('home') }}#features" 
                           class="{{ $activeNav === 'features' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                            Возможности
                        </a>
                        <a href="{{ route('plans.index') }}" 
                           class="{{ $activeNav === 'plans' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                            Тарифы
                        </a>
                        <a href="{{ route('about') }}" 
                           class="{{ $activeNav === 'about' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                            О нас
                        </a>
                        <a href="{{ route('contact') }}" 
                           class="{{ $activeNav === 'contact' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                            Контакты
                        </a>
                    @else
                        {{-- Авторизованный пользователь --}}
                        <a href="{{ route('dashboard') }}" 
                           class="{{ $activeNav === 'dashboard' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                            Сегодня
                        </a>
                        <a href="{{ route('menus.index') }}" 
                           class="{{ $activeNav === 'menus' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                            Меню
                        </a>
                        <a href="{{ route('recipes.index') }}" 
                           class="{{ $activeNav === 'recipes' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                            Рецепты
                        </a>
                        @if($isSubscriber)
                            <a href="{{ route('shopping-list.index') }}" 
                               class="{{ $activeNav === 'shopping' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                                Покупки
                            </a>
                        @endif
                        <a href="{{ route('plans.index') }}" 
                           class="{{ $activeNav === 'plans' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                            Тарифы
                        </a>
                    @endif
                @endif
            </nav>
            
            {{-- Desktop Auth/User Buttons --}}
            <div class="hidden md:flex items-center gap-3">
                @if($isGuest)
                    <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 font-medium hover:text-green-600 transition">Войти</a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                        Начать бесплатно
                    </a>
                @else
                    {{-- Админ-панель для админов --}}
                    @if($isAdmin)
                        <a href="/admin" class="px-4 py-2 text-gray-700 font-medium hover:text-green-600 transition">
                            <i data-lucide="settings" class="w-4 h-4 inline mr-1"></i>
                            Админ
                        </a>
                    @endif
                    
                    {{-- Профиль пользователя --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-gray-100 transition">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                <span class="text-green-600 font-semibold text-sm">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <span class="text-gray-700 font-medium hidden lg:block">{{ $user->name }}</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                        </button>
                        
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50"
                             style="display: none;">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 inline mr-2"></i>
                                Мой кабинет
                            </a>
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                <i data-lucide="user" class="w-4 h-4 inline mr-2"></i>
                                Профиль
                            </a>
                            @if($isSubscriber)
                                <a href="{{ route('subscriptions.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                    <i data-lucide="credit-card" class="w-4 h-4 inline mr-2"></i>
                                    Подписка
                                </a>
                            @endif
                            <hr class="my-2 border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition">
                                    <i data-lucide="log-out" class="w-4 h-4 inline mr-2"></i>
                                    Выйти
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
            
            {{-- Mobile Menu Button --}}
            <button 
                type="button" 
                class="md:hidden p-2 text-gray-600 hover:text-green-600 hover:bg-gray-100 rounded-lg transition"
                @click="mobileMenuOpen = !mobileMenuOpen"
                :aria-expanded="mobileMenuOpen"
            >
                <i x-show="!mobileMenuOpen" data-lucide="menu" class="w-6 h-6"></i>
                <i x-show="mobileMenuOpen" data-lucide="x" class="w-6 h-6" style="display: none;"></i>
            </button>
        </div>
    </div>
    
    {{-- Mobile Navigation (Full Screen Overlay) --}}
    <div 
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.away="mobileMenuOpen = false"
        class="md:hidden fixed inset-x-0 top-16 bottom-0 bg-white z-40 overflow-y-auto"
        style="display: none;"
    >
        <div class="px-4 py-6 space-y-2">
            @if($isLanding)
                {{-- Мобильная навигация для лендинга --}}
                <a href="#features" @click="mobileMenuOpen = false"
                   class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                    <i data-lucide="sparkles" class="w-5 h-5 inline mr-3"></i>
                    Возможности
                </a>
                <a href="#how-it-works" @click="mobileMenuOpen = false"
                   class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                    <i data-lucide="list-ordered" class="w-5 h-5 inline mr-3"></i>
                    Как это работает
                </a>
                <a href="#pricing" @click="mobileMenuOpen = false"
                   class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                    <i data-lucide="credit-card" class="w-5 h-5 inline mr-3"></i>
                    Тарифы
                </a>
                <a href="#faq" @click="mobileMenuOpen = false"
                   class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                    <i data-lucide="help-circle" class="w-5 h-5 inline mr-3"></i>
                    FAQ
                </a>
            @else
                @if($isGuest)
                    {{-- Мобильная навигация для гостя --}}
                    <a href="{{ route('home') }}#features" @click="mobileMenuOpen = false"
                       class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                        <i data-lucide="sparkles" class="w-5 h-5 inline mr-3"></i>
                        Возможности
                    </a>
                    <a href="{{ route('plans.index') }}" @click="mobileMenuOpen = false"
                       class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                        <i data-lucide="credit-card" class="w-5 h-5 inline mr-3"></i>
                        Тарифы
                    </a>
                    <a href="{{ route('about') }}" @click="mobileMenuOpen = false"
                       class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                        <i data-lucide="info" class="w-5 h-5 inline mr-3"></i>
                        О нас
                    </a>
                    <a href="{{ route('contact') }}" @click="mobileMenuOpen = false"
                       class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                        <i data-lucide="mail" class="w-5 h-5 inline mr-3"></i>
                        Контакты
                    </a>
                @else
                    {{-- Мобильная навигация для авторизованного --}}
                    <a href="{{ route('dashboard') }}" @click="mobileMenuOpen = false"
                       class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 inline mr-3"></i>
                        Сегодня
                    </a>
                    <a href="{{ route('menus.index') }}" @click="mobileMenuOpen = false"
                       class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                        <i data-lucide="calendar" class="w-5 h-5 inline mr-3"></i>
                        Меню
                    </a>
                    <a href="{{ route('recipes.index') }}" @click="mobileMenuOpen = false"
                       class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                        <i data-lucide="chef-hat" class="w-5 h-5 inline mr-3"></i>
                        Рецепты
                    </a>
                    @if($isSubscriber)
                        <a href="{{ route('shopping-list.index') }}" @click="mobileMenuOpen = false"
                           class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                            <i data-lucide="shopping-cart" class="w-5 h-5 inline mr-3"></i>
                            Покупки
                        </a>
                    @endif
                    <a href="{{ route('plans.index') }}" @click="mobileMenuOpen = false"
                       class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                        <i data-lucide="credit-card" class="w-5 h-5 inline mr-3"></i>
                        Тарифы
                    </a>
                    
                    {{-- Админ-панель --}}
                    @if($isAdmin)
                        <a href="/admin" @click="mobileMenuOpen = false"
                           class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                            <i data-lucide="settings" class="w-5 h-5 inline mr-3"></i>
                            Админ-панель
                        </a>
                    @endif
                    
                    <hr class="my-4 border-gray-100">
                    
                    <a href="{{ route('profile.show') }}" @click="mobileMenuOpen = false"
                       class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                        <i data-lucide="user" class="w-5 h-5 inline mr-3"></i>
                        Профиль
                    </a>
                    @if($isSubscriber)
                        <a href="{{ route('subscriptions.index') }}" @click="mobileMenuOpen = false"
                           class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                            <i data-lucide="credit-card" class="w-5 h-5 inline mr-3"></i>
                            Моя подписка
                        </a>
                    @endif
                @endif
            @endif
            
            {{-- Mobile Auth Buttons --}}
            <div class="pt-6 mt-6 border-t border-gray-100 space-y-3">
                @if($isGuest)
                    <a href="{{ route('register') }}" @click="mobileMenuOpen = false"
                       class="block w-full px-6 py-4 bg-green-500 text-white rounded-xl font-semibold text-center text-lg hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                        <i data-lucide="rocket" class="w-5 h-5 inline mr-2"></i>
                        Начать бесплатно
                    </a>
                    <a href="{{ route('login') }}" @click="mobileMenuOpen = false"
                       class="block w-full px-6 py-4 bg-gray-100 text-gray-700 rounded-xl font-semibold text-center text-lg hover:bg-gray-200 transition">
                        <i data-lucide="log-in" class="w-5 h-5 inline mr-2"></i>
                        Войти
                    </a>
                @else
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" @click="mobileMenuOpen = false"
                                class="w-full px-6 py-4 bg-red-50 text-red-600 rounded-xl font-semibold text-center text-lg hover:bg-red-100 transition">
                            <i data-lucide="log-out" class="w-5 h-5 inline mr-2"></i>
                            Выйти
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</header>
