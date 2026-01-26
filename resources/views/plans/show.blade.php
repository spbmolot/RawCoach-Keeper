@extends('layouts.public')

@section('title', $plan->name . ' — RawPlan')
@section('description', $plan->description ?? 'Тариф ' . $plan->name . ' — ' . number_format($plan->price, 0, ',', ' ') . '₽. Готовые планы питания для похудения.')
@section('keywords', 'тариф, подписка, план питания, ' . $plan->name . ', RawPlan')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 rounded-full text-sm font-medium mb-6 backdrop-blur-sm border border-white/20 text-white">
            @if($plan->type === 'trial')
                <i data-lucide="gift" class="w-4 h-4"></i>
                Бесплатный пробный период
            @elseif($plan->type === 'yearly')
                <i data-lucide="star" class="w-4 h-4"></i>
                Популярный выбор
            @elseif($plan->type === 'personal')
                <i data-lucide="user" class="w-4 h-4"></i>
                Персональный подход
            @else
                <i data-lucide="zap" class="w-4 h-4"></i>
                Стандартный тариф
            @endif
        </div>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4">{{ $plan->name }}</h1>
        <div class="text-5xl font-extrabold text-white mb-2">
            {{ number_format($plan->price, 0, ',', ' ') }} ₽
            <span class="text-xl font-normal text-green-200">/ {{ $plan->duration_days }} дней</span>
        </div>
        @if($plan->description)
            <p class="text-xl text-green-100 max-w-2xl mx-auto mt-4">{{ $plan->description }}</p>
        @endif
    </div>
</section>

<!-- Plan Details -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12">
            <!-- Features -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Что включено</h2>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900">Меню на месяц</span>
                            <p class="text-sm text-gray-500">Готовые планы питания на каждый день</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900">Рецепты с КБЖУ</span>
                            <p class="text-sm text-gray-500">Калории, белки, жиры и углеводы рассчитаны</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900">Списки покупок</span>
                            <p class="text-sm text-gray-500">Автоматическая генерация на неделю</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900">Экспорт в PDF</span>
                            <p class="text-sm text-gray-500">Скачивайте меню и рецепты</p>
                        </div>
                    </li>
                    @if($plan->type === 'yearly' || $plan->type === 'personal')
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900">Доступ к архиву</span>
                            <p class="text-sm text-gray-500">Все прошлые меню в вашем распоряжении</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900">Приоритетная поддержка</span>
                            <p class="text-sm text-gray-500">Ответ в течение 24 часов</p>
                        </div>
                    </li>
                    @endif
                    @if($plan->type === 'personal')
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="star" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900">Персональный план</span>
                            <p class="text-sm text-gray-500">Меню под ваши цели и предпочтения</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="star" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900">Консультация нутрициолога</span>
                            <p class="text-sm text-gray-500">Индивидуальные рекомендации</p>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- CTA Card -->
            <div>
                <div class="bg-gray-50 rounded-2xl p-8 sticky top-24">
                    <div class="text-center mb-6">
                        <div class="text-3xl font-bold text-gray-900">
                            {{ number_format($plan->price, 0, ',', ' ') }} ₽
                        </div>
                        <div class="text-gray-500">за {{ $plan->duration_days }} дней</div>
                        @if($plan->type === 'yearly')
                            <div class="mt-2 text-sm text-green-600 font-medium">
                                Экономия 25% по сравнению с месячной подпиской
                            </div>
                        @endif
                    </div>

                    @auth
                        @if($currentSubscription && $currentSubscription->plan_id === $plan->id)
                            <div class="bg-green-100 text-green-700 rounded-xl p-4 text-center mb-4">
                                <i data-lucide="check-circle" class="w-5 h-5 inline-block mr-1"></i>
                                Это ваш текущий тариф
                            </div>
                        @elseif($currentSubscription)
                            <form action="{{ route('subscriptions.upgrade', $plan) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-4 px-6 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                                    Перейти на этот тариф
                                </button>
                            </form>
                        @else
                            <form action="{{ route('subscriptions.create', $plan) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-4 px-6 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                                    Оформить подписку
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="block w-full py-4 px-6 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25 text-center">
                            Начать бесплатно
                        </a>
                        <p class="text-center text-sm text-gray-500 mt-3">
                            7 дней бесплатного пробного периода
                        </p>
                    @endauth

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <i data-lucide="shield-check" class="w-4 h-4 text-green-500"></i>
                                Безопасная оплата
                            </li>
                            <li class="flex items-center gap-2">
                                <i data-lucide="refresh-cw" class="w-4 h-4 text-green-500"></i>
                                Отмена в любой момент
                            </li>
                            <li class="flex items-center gap-2">
                                <i data-lucide="credit-card" class="w-4 h-4 text-green-500"></i>
                                Visa, MasterCard, МИР
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Plans -->
@if($relatedPlans->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Другие тарифы</h2>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($relatedPlans as $relatedPlan)
            <a href="{{ route('plans.show', $relatedPlan) }}" class="bg-white rounded-2xl p-6 border border-gray-200 hover:border-green-300 hover:shadow-lg transition">
                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $relatedPlan->name }}</h3>
                <div class="text-2xl font-bold text-gray-900 mb-2">
                    {{ number_format($relatedPlan->price, 0, ',', ' ') }} ₽
                    <span class="text-sm font-normal text-gray-500">/ {{ $relatedPlan->duration_days }} дней</span>
                </div>
                <p class="text-sm text-gray-500">{{ Str::limit($relatedPlan->description, 80) }}</p>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
