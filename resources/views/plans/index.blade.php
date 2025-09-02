<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Планы питания') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Hero секция -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6 lg:p-8 bg-gradient-to-r from-amber-500 to-orange-500 text-white">
                    <h1 class="text-3xl font-bold mb-4">Выберите свой план питания</h1>
                    <p class="text-lg opacity-90">Готовые планы питания для похудения с подробными рецептами и граммовкой</p>
                </div>
            </div>

            <!-- Планы -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($plans as $plan)
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg {{ $plan->is_popular ? 'ring-2 ring-amber-500' : '' }}">
                        @if($plan->is_popular)
                            <div class="bg-amber-500 text-white text-center py-2 text-sm font-semibold">
                                ПОПУЛЯРНЫЙ
                            </div>
                        @endif
                        
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                                <p class="text-gray-600 mb-4">{{ $plan->description }}</p>
                                
                                <div class="mb-4">
                                    <span class="text-4xl font-bold text-gray-900">₽{{ number_format($plan->price, 0, ',', ' ') }}</span>
                                    @if($plan->type === 'yearly')
                                        <span class="text-sm text-gray-500 line-through ml-2">₽{{ number_format($plan->price * 1.25, 0, ',', ' ') }}</span>
                                        <div class="text-green-600 text-sm font-semibold">Экономия 20%</div>
                                    @endif
                                    <div class="text-gray-500 text-sm">
                                        @if($plan->type === 'monthly')
                                            в месяц
                                        @elseif($plan->type === 'yearly')
                                            в год
                                        @else
                                            индивидуально
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Особенности плана -->
                            <ul class="space-y-3 mb-6">
                                @if($plan->limits)
                                    @if($plan->limits['current_access'] ?? true)
                                        <li class="flex items-center">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Доступ к текущим планам
                                        </li>
                                    @endif
                                    
                                    @if($plan->limits['archive_access'] ?? false)
                                        <li class="flex items-center">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Архив всех планов
                                        </li>
                                    @endif
                                    
                                    @if($plan->limits['early_access'] ?? false)
                                        <li class="flex items-center">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Ранний доступ к новинкам
                                        </li>
                                    @endif
                                    
                                    @if($plan->limits['personal_plans'] ?? false)
                                        <li class="flex items-center">
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Персональные планы питания
                                        </li>
                                    @endif
                                @endif
                                
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Подробные рецепты с граммовкой
                                </li>
                                
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Калорийность 1200-1400 ккал/день
                                </li>
                                
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Списки покупок
                                </li>
                            </ul>

                            <!-- Кнопка подписки -->
                            @auth
                                @if(auth()->user()->hasActivePlan($plan->id))
                                    <button class="w-full bg-green-100 text-green-800 py-3 px-6 rounded-lg font-semibold cursor-not-allowed">
                                        Активная подписка
                                    </button>
                                @else
                                    <form action="{{ route('subscriptions.create') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white py-3 px-6 rounded-lg font-semibold transition duration-200">
                                            Выбрать план
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('register') }}" class="block w-full bg-amber-500 hover:bg-amber-600 text-white py-3 px-6 rounded-lg font-semibold text-center transition duration-200">
                                    Начать сейчас
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Дополнительная информация -->
            <div class="mt-12 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Почему выбирают RawPlan?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-lg mb-2">Проверенные рецепты</h4>
                            <p class="text-gray-600">Все рецепты разработаны профессиональными нутрициологами</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-lg mb-2">Поддержка 24/7</h4>
                            <p class="text-gray-600">Наши специалисты всегда готовы помочь с вопросами по питанию</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-lg mb-2">Гарантия результата</h4>
                            <p class="text-gray-600">Возврат средств в течение 7 дней, если план не подошел</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
