@props(['activeNav' => null])

<header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl hero-gradient flex items-center justify-center">
                    <i data-lucide="salad" class="w-6 h-6 text-white"></i>
                </div>
                <span class="text-xl font-bold text-gray-900">RawPlan</span>
            </a>
            
            {{-- Desktop Navigation --}}
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}#features" 
                   class="{{ $activeNav === 'features' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                    Возможности
                </a>
                <a href="{{ route('plans.index') }}" 
                   class="{{ $activeNav === 'plans' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                    Тарифы
                </a>
                @auth
                    <a href="{{ route('menus.index') }}" 
                       class="{{ $activeNav === 'menus' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                        Меню
                    </a>
                    <a href="{{ route('recipes.index') }}" 
                       class="{{ $activeNav === 'recipes' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                        Рецепты
                    </a>
                @else
                    <a href="{{ route('about') }}" 
                       class="{{ $activeNav === 'about' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                        О нас
                    </a>
                    <a href="{{ route('contact') }}" 
                       class="{{ $activeNav === 'contact' ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600 font-medium transition' }}">
                        Контакты
                    </a>
                @endauth
            </nav>
            
            {{-- Auth Buttons --}}
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                        Мой кабинет
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 font-medium hover:text-green-600 transition">Войти</a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                        Начать бесплатно
                    </a>
                @endauth
            </div>
            
            {{-- Mobile Menu Button --}}
            <button type="button" class="md:hidden p-2 text-gray-600 hover:text-green-600" onclick="toggleMobileMenu()">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
        </div>
        
        {{-- Mobile Navigation --}}
        <div id="mobile-menu" class="hidden md:hidden pb-4">
            <nav class="flex flex-col gap-2">
                <a href="{{ route('home') }}#features" class="px-3 py-2 text-gray-600 hover:text-green-600 hover:bg-gray-50 rounded-lg font-medium transition">
                    Возможности
                </a>
                <a href="{{ route('plans.index') }}" class="px-3 py-2 text-gray-600 hover:text-green-600 hover:bg-gray-50 rounded-lg font-medium transition">
                    Тарифы
                </a>
                @auth
                    <a href="{{ route('menus.index') }}" class="px-3 py-2 text-gray-600 hover:text-green-600 hover:bg-gray-50 rounded-lg font-medium transition">
                        Меню
                    </a>
                    <a href="{{ route('recipes.index') }}" class="px-3 py-2 text-gray-600 hover:text-green-600 hover:bg-gray-50 rounded-lg font-medium transition">
                        Рецепты
                    </a>
                @else
                    <a href="{{ route('about') }}" class="px-3 py-2 text-gray-600 hover:text-green-600 hover:bg-gray-50 rounded-lg font-medium transition">
                        О нас
                    </a>
                    <a href="{{ route('contact') }}" class="px-3 py-2 text-gray-600 hover:text-green-600 hover:bg-gray-50 rounded-lg font-medium transition">
                        Контакты
                    </a>
                @endauth
            </nav>
        </div>
    </div>
</header>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
}
</script>
