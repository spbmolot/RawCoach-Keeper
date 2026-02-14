@extends('layouts.app')

@section('title', 'Моя подписка — RawPlan')

@php $activeNav = 'subscription'; @endphp

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-8">Моя подписка</h1>

    @if($subscription)
        {{-- Активная подписка --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 mb-8">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-xl font-bold text-gray-900">{{ $subscription->plan->name }}</h2>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Активна</span>
                    </div>
                    <p class="text-gray-500">{{ $subscription->plan->description ?? '' }}</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($subscription->plan->price, 0, ',', ' ') }} ₽</div>
                    <div class="text-sm text-gray-500">
                        @if($subscription->plan->type === 'monthly') / месяц
                        @elseif($subscription->plan->type === 'yearly') / год
                        @elseif($subscription->plan->type === 'trial') пробный период
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="text-sm text-gray-500 mb-1">Начало</div>
                    <div class="font-semibold text-gray-900">{{ $subscription->started_at?->format('d.m.Y') ?? '—' }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="text-sm text-gray-500 mb-1">Действует до</div>
                    <div class="font-semibold text-gray-900">{{ $subscription->ends_at?->format('d.m.Y') ?? '—' }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="text-sm text-gray-500 mb-1">Автопродление</div>
                    <div class="font-semibold {{ $subscription->auto_renew ? 'text-green-600' : 'text-red-500' }}">
                        {{ $subscription->auto_renew ? 'Включено' : 'Отключено' }}
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                @if($subscription->auto_renew)
                    <form action="{{ route('subscriptions.toggle-renewal', $subscription) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">
                            Отключить автопродление
                        </button>
                    </form>
                @else
                    <form action="{{ route('subscriptions.toggle-renewal', $subscription) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-xl text-sm font-medium transition">
                            Включить автопродление
                        </button>
                    </form>
                @endif

                @if($subscription->plan->type !== 'trial')
                    <a href="{{ route('plans.upgrade') }}" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-medium transition">
                        Улучшить план
                    </a>
                @endif

                @if(!$subscription->cancelled_at)
                    <form action="{{ route('subscriptions.cancel', $subscription) }}" method="POST" 
                          onsubmit="return confirm('Вы уверены? Подписка будет активна до {{ $subscription->ends_at?->format('d.m.Y') }}')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-sm font-medium transition">
                            Отменить подписку
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @else
        {{-- Нет подписки --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center mb-8">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="credit-card" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">У вас нет активной подписки</h2>
            <p class="text-gray-500 mb-6">Выберите план и получите доступ к меню, рецептам и спискам покупок</p>
            <a href="{{ route('plans.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition">
                <i data-lucide="rocket" class="w-5 h-5"></i>
                Выбрать план
            </a>
        </div>
    @endif

    {{-- История платежей --}}
    @if($payments->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4">История платежей</h2>
            <div class="divide-y divide-gray-100">
                @foreach($payments as $payment)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <div class="font-medium text-gray-900">{{ $payment->description ?? 'Платёж' }}</div>
                            <div class="text-sm text-gray-500">{{ $payment->created_at->format('d.m.Y H:i') }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-gray-900">{{ number_format($payment->amount, 0, ',', ' ') }} ₽</div>
                            <div class="text-sm {{ $payment->status === 'paid' ? 'text-green-600' : ($payment->status === 'failed' ? 'text-red-500' : 'text-yellow-600') }}">
                                @switch($payment->status)
                                    @case('paid') Оплачен @break
                                    @case('pending') Ожидает @break
                                    @case('failed') Отклонён @break
                                    @case('cancelled') Отменён @break
                                    @default {{ $payment->status }}
                                @endswitch
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($payments->count() >= 10)
                <div class="mt-4 text-center">
                    <a href="{{ route('payment.history') }}" class="text-green-600 hover:text-green-700 font-medium text-sm">
                        Показать все платежи →
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
