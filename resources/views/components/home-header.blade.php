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
            
            {{-- Desktop Navigation (Landing page anchors) --}}
            <nav class="hidden md:flex items-center gap-8">
                <a href="#features" class="text-gray-600 hover:text-green-600 font-medium transition">Возможности</a>
                <a href="#how-it-works" class="text-gray-600 hover:text-green-600 font-medium transition">Как это работает</a>
                <a href="#pricing" class="text-gray-600 hover:text-green-600 font-medium transition">Тарифы</a>
                <a href="#faq" class="text-gray-600 hover:text-green-600 font-medium transition">FAQ</a>
            </nav>
            
            {{-- Desktop Auth Buttons --}}
            <div class="hidden md:flex items-center gap-3">
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
            <a href="#features" 
               @click="mobileMenuOpen = false"
               class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                <i data-lucide="sparkles" class="w-5 h-5 inline mr-3"></i>
                Возможности
            </a>
            <a href="#how-it-works" 
               @click="mobileMenuOpen = false"
               class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                <i data-lucide="list-ordered" class="w-5 h-5 inline mr-3"></i>
                Как это работает
            </a>
            <a href="#pricing" 
               @click="mobileMenuOpen = false"
               class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                <i data-lucide="credit-card" class="w-5 h-5 inline mr-3"></i>
                Тарифы
            </a>
            <a href="#faq" 
               @click="mobileMenuOpen = false"
               class="block px-4 py-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl font-medium transition text-lg">
                <i data-lucide="help-circle" class="w-5 h-5 inline mr-3"></i>
                FAQ
            </a>
            
            {{-- Mobile Auth Buttons --}}
            <div class="pt-6 mt-6 border-t border-gray-100 space-y-3">
                @auth
                    <a href="{{ route('dashboard') }}" 
                       @click="mobileMenuOpen = false"
                       class="block w-full px-6 py-4 bg-green-500 text-white rounded-xl font-semibold text-center text-lg hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 inline mr-2"></i>
                        Мой кабинет
                    </a>
                @else
                    <a href="{{ route('register') }}" 
                       @click="mobileMenuOpen = false"
                       class="block w-full px-6 py-4 bg-green-500 text-white rounded-xl font-semibold text-center text-lg hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                        <i data-lucide="rocket" class="w-5 h-5 inline mr-2"></i>
                        Начать бесплатно
                    </a>
                    <a href="{{ route('login') }}" 
                       @click="mobileMenuOpen = false"
                       class="block w-full px-6 py-4 bg-gray-100 text-gray-700 rounded-xl font-semibold text-center text-lg hover:bg-gray-200 transition">
                        <i data-lucide="log-in" class="w-5 h-5 inline mr-2"></i>
                        Войти
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>
