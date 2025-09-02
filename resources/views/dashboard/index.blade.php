<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Личный кабинет') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Статус подписки -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6 lg:p-8">
                    @if($activeSubscription)
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $activeSubscription->plan->name }}</h3>
                                <p class="text-gray-600">Активна до {{ $activeSubscription->ends_at->format('d.m.Y') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    Активна
                                </span>
                                @if($activeSubscription->ends_at->diffInDays(now()) <= 7)
                                    <div class="text-orange-600 text-sm mt-1">
                                        Истекает через {{ $activeSubscription->ends_at->diffInDays(now()) }} дн.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Прогресс подписки -->
                        <div class="mb-6">
                            @php
                                $totalDays = $activeSubscription->starts_at->diffInDays($activeSubscription->ends_at);
                                $passedDays = $activeSubscription->starts_at->diffInDays(now());
                                $progress = $totalDays > 0 ? min(100, ($passedDays / $totalDays) * 100) : 0;
                            @endphp
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>Прогресс подписки</span>
                                <span>{{ round($progress) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <!-- Действия с подпиской -->
                        <div class="flex space-x-4">
                            @if(!$activeSubscription->auto_renewal)
                                <form action="{{ route('subscriptions.toggle-renewal', $activeSubscription) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                        Включить автопродление
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('subscriptions.toggle-renewal', $activeSubscription) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                        Отключить автопродление
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('plans.index') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                Изменить план
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">У вас нет активной подписки</h3>
                            <p class="text-gray-600 mb-4">Выберите план питания, чтобы начать свой путь к здоровому образу жизни</p>
                            <a href="{{ route('plans.index') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-3 rounded-lg font-medium">
                                Выбрать план
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Быстрые действия -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Быстрые действия</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <a href="{{ route('menus.today') }}" class="flex flex-col items-center p-4 bg-amber-50 rounded-lg hover:bg-amber-100 transition">
                                    <svg class="w-8 h-8 text-amber-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Сегодня</span>
                                </a>
                                
                                <a href="{{ route('menus.week') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                    <svg class="w-8 h-8 text-blue-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Неделя</span>
                                </a>
                                
                                <a href="{{ route('shopping-lists.index') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                                    <svg class="w-8 h-8 text-green-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Покупки</span>
                                </a>
                                
                                <a href="{{ route('recipes.favorites') }}" class="flex flex-col items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition">
                                    <svg class="w-8 h-8 text-red-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Избранное</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Последние рецепты -->
                    @if($recentRecipes && $recentRecipes->count() > 0)
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Недавние рецепты</h3>
                                <div class="space-y-4">
                                    @foreach($recentRecipes as $recipe)
                                        <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                            @if($recipe->image)
                                                <img src="{{ $recipe->image }}" alt="{{ $recipe->name }}" class="w-12 h-12 rounded-lg object-cover">
                                            @else
                                                <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-900">{{ $recipe->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $recipe->calories }} ккал • {{ $recipe->cooking_time }} мин</p>
                                            </div>
                                            <a href="{{ route('recipes.show', $recipe) }}" class="text-amber-500 hover:text-amber-600">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Боковая панель -->
                <div class="space-y-6">
                    <!-- Статистика -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ваша статистика</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Дней с нами:</span>
                                    <span class="font-semibold">{{ auth()->user()->created_at->diffInDays(now()) }}</span>
                                </div>
                                @if($activeSubscription)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Дней подписки:</span>
                                        <span class="font-semibold">{{ $activeSubscription->starts_at->diffInDays(now()) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Избранных рецептов:</span>
                                    <span class="font-semibold">{{ auth()->user()->favoriteRecipes()->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Поддержка -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Нужна помощь?</h3>
                            <div class="space-y-3">
                                <a href="{{ route('support.faq') }}" class="block text-amber-600 hover:text-amber-700">
                                    Часто задаваемые вопросы
                                </a>
                                <a href="{{ route('support.contact') }}" class="block text-amber-600 hover:text-amber-700">
                                    Связаться с поддержкой
                                </a>
                                <a href="{{ route('support.guides') }}" class="block text-amber-600 hover:text-amber-700">
                                    Руководства пользователя
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
