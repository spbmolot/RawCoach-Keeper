@extends('layouts.public')

@section('title', 'Обновление тарифа — RawPlan')
@section('description', 'Обновите ваш тариф RawPlan и получите больше возможностей.')
@section('keywords', 'обновление тарифа, апгрейд, подписка, RawPlan')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-6">Обновление тарифа</h1>
        <p class="text-xl text-green-100 max-w-2xl mx-auto">
            Получите больше возможностей с новым тарифом
        </p>
    </div>
</section>

<!-- Current Plan -->
<section class="py-12 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gray-50 rounded-2xl p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500 mb-1">Ваш текущий тариф</div>
                    <div class="text-xl font-bold text-gray-900">{{ $currentSubscription->plan->name }}</div>
                    <div class="text-gray-600">
                        {{ number_format($currentSubscription->plan->price, 0, ',', ' ') }} ₽ / {{ $currentSubscription->plan->duration_days }} дней
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500 mb-1">Действует до</div>
                    <div class="font-medium text-gray-900">
                        {{ $currentSubscription->ends_at->format('d.m.Y') }}
                    </div>
                </div>
            </div>
        </div>

        @if($availablePlans->count() > 0)
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Доступные тарифы для обновления</h2>
            <div class="space-y-4">
                @foreach($availablePlans as $plan)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 hover:border-green-300 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                            <p class="text-gray-600 mt-1">{{ $plan->description }}</p>
                            <div class="mt-2">
                                <span class="text-2xl font-bold text-gray-900">
                                    {{ number_format($plan->price, 0, ',', ' ') }} ₽
                                </span>
                                <span class="text-gray-500">/ {{ $plan->duration_days }} дней</span>
                            </div>
                        </div>
                        <form action="{{ route('subscriptions.upgrade', $plan) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                                Перейти
                            </button>
                        </form>
                    </div>
                    
                    @if($plan->type === 'yearly')
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2 text-sm text-green-600">
                            <i data-lucide="percent" class="w-4 h-4"></i>
                            Экономия 25% по сравнению с месячной подпиской
                        </div>
                    </div>
                    @endif
                    
                    @if($plan->type === 'personal')
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex flex-wrap gap-3">
                            <span class="inline-flex items-center gap-1 text-sm text-purple-600 bg-purple-50 px-3 py-1 rounded-full">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                Персональный план
                            </span>
                            <span class="inline-flex items-center gap-1 text-sm text-purple-600 bg-purple-50 px-3 py-1 rounded-full">
                                <i data-lucide="message-circle" class="w-4 h-4"></i>
                                Консультация нутрициолога
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                    <i data-lucide="crown" class="w-8 h-8 text-green-600"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">У вас максимальный тариф!</h2>
                <p class="text-gray-600 mb-6">
                    Вы уже используете лучший доступный тариф. Спасибо, что вы с нами!
                </p>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    Вернуться в кабинет
                </a>
            </div>
        @endif
    </div>
</section>

<!-- FAQ -->
<section class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Часто задаваемые вопросы</h2>
        <div class="space-y-4">
            <details class="bg-white rounded-xl p-5 group">
                <summary class="flex items-center justify-between cursor-pointer list-none">
                    <span class="font-medium text-gray-900">Как происходит переход на новый тариф?</span>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform"></i>
                </summary>
                <p class="mt-3 text-gray-600">
                    При переходе на новый тариф вам будет начислена разница в стоимости с учётом оставшегося времени текущей подписки. Новый тариф активируется сразу после оплаты.
                </p>
            </details>
            <details class="bg-white rounded-xl p-5 group">
                <summary class="flex items-center justify-between cursor-pointer list-none">
                    <span class="font-medium text-gray-900">Можно ли вернуться на предыдущий тариф?</span>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform"></i>
                </summary>
                <p class="mt-3 text-gray-600">
                    Да, вы можете изменить тариф при следующем продлении подписки. Текущий тариф будет действовать до конца оплаченного периода.
                </p>
            </details>
        </div>
    </div>
</section>
@endsection
