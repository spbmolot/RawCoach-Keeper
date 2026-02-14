<x-app-layout>
    <div class="max-w-7xl mx-auto">
        {{-- Приветствие --}}
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                Привет, {{ auth()->user()->name }}! 👋
            </h1>
            <p class="text-gray-600 mt-1 text-sm sm:text-base">Добро пожаловать в личный кабинет RawPlan</p>
        </div>

        {{-- Карточка подписки --}}
        @if($activeSubscription)
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-4 sm:p-6 mb-6 sm:mb-8 text-white shadow-xl shadow-green-500/20">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <i data-lucide="crown" class="w-5 h-5"></i>
                            <span class="text-green-100 text-sm font-medium">Активная подписка</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold mb-1">{{ $activeSubscription->plan->name }}</h2>
                        <p class="text-green-100">
                            Действует до {{ $activeSubscription->ends_at->format('d.m.Y') }}
                            @if($activeSubscription->ends_at->diffInDays(now()) <= 7)
                                <span class="ml-2 px-2 py-0.5 bg-yellow-400 text-yellow-900 rounded-full text-xs font-semibold">
                                    Осталось {{ $activeSubscription->ends_at->diffInDays(now()) }} дн.
                                </span>
                            @endif
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2 sm:gap-3">
                        @if($activeSubscription->auto_renew)
                            <form action="{{ route('subscriptions.toggle-renewal', $activeSubscription) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-xs sm:text-sm font-medium transition backdrop-blur-sm">
                                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                                    <span class="hidden xs:inline">Автопродление</span>
                                    <span class="xs:hidden">Авто</span> вкл.
                                </button>
                            </form>
                        @else
                            <form action="{{ route('subscriptions.toggle-renewal', $activeSubscription) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-xs sm:text-sm font-medium transition backdrop-blur-sm">
                                    <i data-lucide="refresh-cw-off" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                                    <span class="hidden sm:inline">Включить автопродление</span>
                                    <span class="sm:hidden">Автопродление</span>
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('plans.index') }}" class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 bg-white text-green-600 hover:bg-green-50 rounded-xl text-xs sm:text-sm font-semibold transition">
                            <i data-lucide="arrow-up-circle" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                            <span class="hidden sm:inline">Улучшить план</span>
                            <span class="sm:hidden">Улучшить</span>
                        </a>
                    </div>
                </div>
                {{-- Прогресс-бар --}}
                @php
                    $totalDays = $activeSubscription->started_at->diffInDays($activeSubscription->ends_at);
                    $passedDays = $activeSubscription->started_at->diffInDays(now());
                    $progress = $totalDays > 0 ? min(100, ($passedDays / $totalDays) * 100) : 0;
                    $remainingDays = max(0, $activeSubscription->ends_at->diffInDays(now()));
                @endphp
                <div class="mt-6">
                    <div class="flex justify-between text-sm text-green-100 mb-2">
                        <span>Использовано {{ $passedDays }} из {{ $totalDays }} дней</span>
                        <span>{{ round($progress) }}%</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2">
                        <div class="bg-white h-2 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-gradient-to-r from-gray-100 to-gray-200 rounded-2xl p-8 mb-8 text-center">
                <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i data-lucide="sparkles" class="w-10 h-10 text-green-500"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Начните свой путь к здоровью</h2>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Выберите план питания и получите доступ к сотням рецептов, меню и спискам покупок
                </p>
                <a href="{{ route('plans.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition shadow-lg shadow-green-500/25">
                    <i data-lucide="rocket" class="w-5 h-5"></i>
                    Выбрать план
                </a>
            </div>
        @endif

        {{-- Быстрые действия --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 sm:gap-4 mb-6 sm:mb-8">
            <a href="{{ route('dashboard.today') }}" class="group bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-amber-200">
                <div class="w-10 h-10 sm:w-14 sm:h-14 bg-amber-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-2 sm:mb-4 group-hover:scale-110 transition-transform">
                    <i data-lucide="sun" class="w-5 h-5 sm:w-7 sm:h-7 text-amber-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-0.5 sm:mb-1 text-sm sm:text-base">Сегодня</h3>
                <p class="text-xs sm:text-sm text-gray-500 hidden xs:block">Меню на сегодня</p>
            </a>
            
            <a href="{{ route('dashboard.week') }}" class="group bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-200">
                <div class="w-10 h-10 sm:w-14 sm:h-14 bg-blue-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-2 sm:mb-4 group-hover:scale-110 transition-transform">
                    <i data-lucide="calendar-days" class="w-5 h-5 sm:w-7 sm:h-7 text-blue-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-0.5 sm:mb-1 text-sm sm:text-base">Неделя</h3>
                <p class="text-xs sm:text-sm text-gray-500 hidden xs:block">План на 7 дней</p>
            </a>
            
            <a href="{{ route('shopping-list.index') }}" class="group bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-green-200">
                <div class="w-10 h-10 sm:w-14 sm:h-14 bg-green-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-2 sm:mb-4 group-hover:scale-110 transition-transform">
                    <i data-lucide="shopping-cart" class="w-5 h-5 sm:w-7 sm:h-7 text-green-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-0.5 sm:mb-1 text-sm sm:text-base">Покупки</h3>
                <p class="text-xs sm:text-sm text-gray-500 hidden xs:block">Список продуктов</p>
            </a>

            <a href="{{ route('dashboard.progress') }}" class="group bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-orange-200">
                <div class="w-10 h-10 sm:w-14 sm:h-14 bg-orange-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-2 sm:mb-4 group-hover:scale-110 transition-transform">
                    <i data-lucide="trending-up" class="w-5 h-5 sm:w-7 sm:h-7 text-orange-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-0.5 sm:mb-1 text-sm sm:text-base">Прогресс</h3>
                <p class="text-xs sm:text-sm text-gray-500 hidden xs:block">Дневник и вес</p>
            </a>
            
            <a href="{{ route('recipes.favorites') }}" class="group bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-red-200">
                <div class="w-10 h-10 sm:w-14 sm:h-14 bg-red-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-2 sm:mb-4 group-hover:scale-110 transition-transform">
                    <i data-lucide="heart" class="w-5 h-5 sm:w-7 sm:h-7 text-red-500"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-0.5 sm:mb-1 text-sm sm:text-base">Избранное</h3>
                <p class="text-xs sm:text-sm text-gray-500 hidden xs:block">Любимые рецепты</p>
            </a>

            <a href="{{ route('dashboard.referrals') }}" class="group bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-purple-200">
                <div class="w-10 h-10 sm:w-14 sm:h-14 bg-purple-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-2 sm:mb-4 group-hover:scale-110 transition-transform">
                    <i data-lucide="gift" class="w-5 h-5 sm:w-7 sm:h-7 text-purple-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-0.5 sm:mb-1 text-sm sm:text-base">Друзья</h3>
                <p class="text-xs sm:text-sm text-gray-500 hidden xs:block">Пригласить друга</p>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-8">
            {{-- Основной контент --}}
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                {{-- Меню и рецепты --}}
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 sm:p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i data-lucide="utensils" class="w-5 h-5 text-green-500"></i>
                                Меню и рецепты
                            </h3>
                            <a href="{{ route('menus.index') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">
                                Все меню →
                            </a>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <a href="{{ route('menus.index') }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 transition">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i data-lucide="book-open" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Текущее меню</h4>
                                <p class="text-sm text-gray-500">{{ now()->translatedFormat('F Y') }}</p>
                            </div>
                            <i data-lucide="chevron-right" class="w-5 h-5 text-gray-400"></i>
                        </a>
                        <a href="{{ route('recipes.index') }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 transition">
                            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                                <i data-lucide="chef-hat" class="w-6 h-6 text-amber-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Все рецепты</h4>
                                <p class="text-sm text-gray-500">Каталог рецептов с КБЖУ</p>
                            </div>
                            <i data-lucide="chevron-right" class="w-5 h-5 text-gray-400"></i>
                        </a>
                        <a href="{{ route('dashboard.calendar') }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 transition">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                <i data-lucide="calendar" class="w-6 h-6 text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Календарь</h4>
                                <p class="text-sm text-gray-500">Планирование питания</p>
                            </div>
                            <i data-lucide="chevron-right" class="w-5 h-5 text-gray-400"></i>
                        </a>
                    </div>
                </div>

                {{-- Последние рецепты --}}
                @if($recentRecipes && $recentRecipes->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i data-lucide="clock" class="w-5 h-5 text-blue-500"></i>
                                Новые рецепты
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($recentRecipes->take(4) as $recipe)
                                <a href="{{ route('recipes.show', $recipe) }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 transition">
                                    @if($recipe->image)
                                        <img src="{{ Storage::url($recipe->image) }}" alt="{{ $recipe->title }}" class="w-14 h-14 rounded-xl object-cover">
                                    @else
                                        <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center">
                                            <i data-lucide="image" class="w-6 h-6 text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-gray-900 truncate">{{ $recipe->title }}</h4>
                                        <div class="flex items-center gap-3 text-sm text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="flame" class="w-4 h-4 text-orange-400"></i>
                                                {{ $recipe->calories ?? 0 }} ккал
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="timer" class="w-4 h-4 text-blue-400"></i>
                                                {{ $recipe->cooking_time ?? 0 }} мин
                                            </span>
                                        </div>
                                    </div>
                                    <i data-lucide="chevron-right" class="w-5 h-5 text-gray-400 flex-shrink-0"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Боковая панель --}}
            <div class="space-y-6">
                {{-- Статистика --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-lucide="bar-chart-3" class="w-5 h-5 text-indigo-500"></i>
                        Статистика
                    </h3>
                    <div class="space-y-4">
                        @php
                            $totalHours = auth()->user()->created_at->diffInHours(now());
                            $daysWithUs = floor($totalHours / 24);
                            $hoursWithUs = $totalHours % 24;
                        @endphp
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="calendar-check" class="w-5 h-5 text-blue-600"></i>
                                </div>
                                <span class="text-gray-600">С нами</span>
                            </div>
                            <span class="text-xl font-bold text-gray-900">{{ $daysWithUs }} д. {{ $hoursWithUs }} ч.</span>
                        </div>
                        @if($activeSubscription)
                            @php
                                $subHours = $activeSubscription->started_at->diffInHours(now());
                                $subDays = floor($subHours / 24);
                                $subHoursRem = $subHours % 24;
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i data-lucide="zap" class="w-5 h-5 text-green-600"></i>
                                    </div>
                                    <span class="text-gray-600">Подписка</span>
                                </div>
                                <span class="text-xl font-bold text-gray-900">{{ $subDays }} д. {{ $subHoursRem }} ч.</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="heart" class="w-5 h-5 text-red-500"></i>
                                </div>
                                <span class="text-gray-600">Избранное</span>
                            </div>
                            <span class="text-xl font-bold text-gray-900">{{ auth()->user()->favoriteRecipes()->count() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Быстрые ссылки --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-lucide="link" class="w-5 h-5 text-gray-500"></i>
                        Быстрые ссылки
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition text-gray-700 hover:text-gray-900">
                            <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                            <span>Мой профиль</span>
                        </a>
                        <a href="{{ route('payment.history') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition text-gray-700 hover:text-gray-900">
                            <i data-lucide="receipt" class="w-5 h-5 text-gray-400"></i>
                            <span>История платежей</span>
                        </a>
                        <a href="{{ route('dashboard.personal-plans') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition text-gray-700 hover:text-gray-900">
                            <i data-lucide="clipboard-list" class="w-5 h-5 text-gray-400"></i>
                            <span>Персональные планы</span>
                        </a>
                    </div>
                </div>

                {{-- Поддержка --}}
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                            <i data-lucide="headphones" class="w-5 h-5 text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Нужна помощь?</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">
                        Наша команда поддержки готова помочь вам с любыми вопросами
                    </p>
                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-medium transition">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                        Написать нам
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
