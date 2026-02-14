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

<style>
    #mobileMenuPanel { position:fixed; left:0; right:0; top:56px; bottom:0; background:#fff; z-index:999; overflow-y:auto; transform:translateX(100%); transition:transform .25s ease; }
    #mobileMenuPanel.is-open { transform:translateX(0); }
    .burger-icon-close { display:none; }
    .burger-open .burger-icon-open { display:none; }
    .burger-open .burger-icon-close { display:block; }
    @media(min-width:768px) {
        #mobileMenuPanel { display:none !important; }
        #mobileMenuBtn { display:none !important; }
    }
    @media(min-width:640px) {
        #mobileMenuPanel { top:64px; }
    }
    .rawplan-logo-text { display:block; }
    @media(max-width:330px) {
        .rawplan-logo-text { display:none; }
    }
</style>

<header 
    id="mainHeader"
    class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 shadow-sm"
>
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14 sm:h-16">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0">
                <div class="w-10 h-10 rounded-xl hero-gradient flex items-center justify-center">
                    <i data-lucide="salad" class="w-6 h-6 text-white"></i>
                </div>
                <span class="text-xl font-bold text-gray-900 rawplan-logo-text">RawPlan</span>
            </a>
            
            {{-- Desktop Navigation --}}
            <nav class="hidden md:flex items-center gap-8">
                @if($isLanding)
                    <a href="#features" class="text-gray-600 hover:text-green-600 font-medium transition">Возможности</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-green-600 font-medium transition">Как это работает</a>
                    <a href="#pricing" class="text-gray-600 hover:text-green-600 font-medium transition">Тарифы</a>
                    <a href="#faq" class="text-gray-600 hover:text-green-600 font-medium transition">FAQ</a>
                @else
                    @if($isGuest)
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
                        <a href="{{ route('blog.index') }}" 
                           class="{{ $activeNav === 'blog' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                            Блог
                        </a>
                        <a href="{{ route('contact') }}" 
                           class="{{ $activeNav === 'contact' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                            Контакты
                        </a>
                    @else
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
                    @if($isAdmin)
                        <a href="/admin" class="px-4 py-2 text-gray-700 font-medium hover:text-green-600 transition">
                            <i data-lucide="settings" class="w-4 h-4 inline mr-1"></i>
                            Админ
                        </a>
                    @endif
                    
                    {{-- Профиль пользователя --}}
                    <div id="profileDropdownWrap" class="relative">
                        <button id="profileDropdownBtn" onclick="document.getElementById('profileDropdownMenu').classList.toggle('hidden')" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-gray-100 transition">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                <span class="text-green-600 font-semibold text-sm">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <span class="text-gray-700 font-medium hidden lg:block">{{ $user->name }}</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                        </button>
                        
                        <div id="profileDropdownMenu"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50 hidden">
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
                id="mobileMenuBtn"
                class="p-2 text-gray-600 hover:text-green-600 hover:bg-gray-100 rounded-lg transition"
                aria-label="Меню"
                onclick="document.getElementById('mobileMenuPanel').classList.toggle('is-open'); this.classList.toggle('burger-open');"
            >
                <svg class="burger-icon-open w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/></svg>
                <svg class="burger-icon-close w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
    </div>
</header>

{{-- Mobile Menu Panel (вне header, в body) --}}
<nav id="mobileMenuPanel">
    <div class="px-3 sm:px-4 py-4 sm:py-6 space-y-1 sm:space-y-2">
        @if($isLanding)
            <a href="#features" onclick="closeMobileMenu()"
               class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                <i data-lucide="sparkles" class="w-5 h-5 inline mr-3"></i>
                Возможности
            </a>
            <a href="#how-it-works" onclick="closeMobileMenu()"
               class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                <i data-lucide="list-ordered" class="w-5 h-5 inline mr-3"></i>
                Как это работает
            </a>
            <a href="#pricing" onclick="closeMobileMenu()"
               class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                <i data-lucide="credit-card" class="w-5 h-5 inline mr-3"></i>
                Тарифы
            </a>
            <a href="#faq" onclick="closeMobileMenu()"
               class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                <i data-lucide="help-circle" class="w-5 h-5 inline mr-3"></i>
                FAQ
            </a>
        @else
            @if($isGuest)
                <a href="{{ route('home') }}#features" onclick="closeMobileMenu()"
                   class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                    <i data-lucide="sparkles" class="w-5 h-5 inline mr-3"></i>
                    Возможности
                </a>
                <a href="{{ route('plans.index') }}" onclick="closeMobileMenu()"
                   class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                    <i data-lucide="credit-card" class="w-5 h-5 inline mr-3"></i>
                    Тарифы
                </a>
                <a href="{{ route('about') }}" onclick="closeMobileMenu()"
                   class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                    <i data-lucide="info" class="w-5 h-5 inline mr-3"></i>
                    О нас
                </a>
                <a href="{{ route('blog.index') }}" onclick="closeMobileMenu()"
                   class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                    <i data-lucide="newspaper" class="w-5 h-5 inline mr-3"></i>
                    Блог
                </a>
                <a href="{{ route('contact') }}" onclick="closeMobileMenu()"
                   class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                    <i data-lucide="mail" class="w-5 h-5 inline mr-3"></i>
                    Контакты
                </a>
            @else
                <a href="{{ route('dashboard') }}" onclick="closeMobileMenu()"
                   class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 inline mr-3"></i>
                    Сегодня
                </a>
                <a href="{{ route('menus.index') }}" onclick="closeMobileMenu()"
                   class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                    <i data-lucide="calendar" class="w-5 h-5 inline mr-3"></i>
                    Меню
                </a>
                <a href="{{ route('recipes.index') }}" onclick="closeMobileMenu()"
                   class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                    <i data-lucide="chef-hat" class="w-5 h-5 inline mr-3"></i>
                    Рецепты
                </a>
                @if($isSubscriber)
                    <a href="{{ route('shopping-list.index') }}" onclick="closeMobileMenu()"
                       class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                        <i data-lucide="shopping-cart" class="w-5 h-5 inline mr-3"></i>
                        Покупки
                    </a>
                @endif
                <a href="{{ route('plans.index') }}" onclick="closeMobileMenu()"
                   class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                    <i data-lucide="credit-card" class="w-5 h-5 inline mr-3"></i>
                    Тарифы
                </a>
                
                @if($isAdmin)
                    <a href="/admin" onclick="closeMobileMenu()"
                       class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                        <i data-lucide="settings" class="w-5 h-5 inline mr-3"></i>
                        Админ-панель
                    </a>
                @endif
                
                <hr class="my-4 border-gray-100">
                
                <a href="{{ route('profile.show') }}" onclick="closeMobileMenu()"
                   class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                    <i data-lucide="user" class="w-5 h-5 inline mr-3"></i>
                    Профиль
                </a>
                @if($isSubscriber)
                    <a href="{{ route('subscriptions.index') }}" onclick="closeMobileMenu()"
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
                <a href="{{ route('register') }}" onclick="closeMobileMenu()"
                   class="block w-full px-6 py-4 bg-green-500 text-white rounded-xl font-semibold text-center text-lg hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                    <i data-lucide="rocket" class="w-5 h-5 inline mr-2"></i>
                    Начать бесплатно
                </a>
                <a href="{{ route('login') }}" onclick="closeMobileMenu()"
                   class="block w-full px-6 py-4 bg-gray-100 text-gray-700 rounded-xl font-semibold text-center text-lg hover:bg-gray-200 transition">
                    <i data-lucide="log-in" class="w-5 h-5 inline mr-2"></i>
                    Войти
                </a>
            @else
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" onclick="closeMobileMenu()"
                            class="w-full px-6 py-4 bg-red-50 text-red-600 rounded-xl font-semibold text-center text-lg hover:bg-red-100 transition">
                        <i data-lucide="log-out" class="w-5 h-5 inline mr-2"></i>
                        Выйти
                    </button>
                </form>
            @endif
        </div>
    </div>
</nav>

<script>
function closeMobileMenu() {
    document.getElementById('mobileMenuPanel').classList.remove('is-open');
    document.getElementById('mobileMenuBtn').classList.remove('burger-open');
}
document.addEventListener('click', function(e) {
    var panel = document.getElementById('mobileMenuPanel');
    var btn = document.getElementById('mobileMenuBtn');
    if (panel && panel.classList.contains('is-open') && !panel.contains(e.target) && !btn.contains(e.target)) {
        closeMobileMenu();
    }
    var profMenu = document.getElementById('profileDropdownMenu');
    var profBtn = document.getElementById('profileDropdownBtn');
    if (profMenu && !profMenu.classList.contains('hidden') && profBtn && !profBtn.contains(e.target) && !profMenu.contains(e.target)) {
        profMenu.classList.add('hidden');
    }
});
</script>
