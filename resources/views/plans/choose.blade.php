@extends('layouts.public')

@section('title', 'Оформление подписки: ' . $plan->name . ' — RawPlan')
@section('description', 'Оформление подписки на тариф ' . $plan->name)

@section('content')
<!-- Hero -->
<section class="hero-gradient py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-2">Оформление подписки</h1>
        <p class="text-lg text-green-100">Тариф «{{ $plan->name }}»</p>
    </div>
</section>

<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-5 gap-8">
            {{-- Детали заказа --}}
            <div class="md:col-span-3">
                <div class="bg-gray-50 rounded-2xl p-6 sm:p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Детали заказа</h2>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Тариф</span>
                            <span class="font-semibold text-gray-900">{{ $plan->name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Период</span>
                            <span class="font-semibold text-gray-900">{{ $plan->duration_days }} дней</span>
                        </div>
                        @if($plan->type === 'yearly')
                            <div class="flex justify-between items-center text-green-600">
                                <span>Скидка 25%</span>
                                <span class="font-semibold">Включена</span>
                            </div>
                        @endif
                        <div class="pt-4 border-t border-gray-200 flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-900">Итого</span>
                            <span class="text-2xl font-extrabold text-gray-900">{{ number_format($plan->price, 0, ',', ' ') }} ₽</span>
                        </div>
                    </div>

                    {{-- Промокод --}}
                    <div class="mb-6">
                        <label for="coupon" class="block text-sm font-medium text-gray-700 mb-1">Промокод</label>
                        <div class="flex gap-2">
                            <input type="text" id="coupon" placeholder="Введите промокод" class="flex-1 rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
                            <button type="button" onclick="applyCoupon()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-sm font-medium transition">
                                Применить
                            </button>
                        </div>
                        <p id="couponMessage" class="mt-1 text-sm hidden"></p>
                    </div>

                    {{-- Кнопка оплаты --}}
                    <form action="{{ route('subscriptions.create', $plan) }}" method="POST">
                        @csrf
                        <input type="hidden" name="coupon_code" id="coupon_code" value="">

                        <button type="submit" class="w-full py-4 px-6 bg-green-500 text-white rounded-xl font-semibold text-lg hover:bg-green-600 transition shadow-lg shadow-green-500/25 flex items-center justify-center gap-2">
                            <i data-lucide="credit-card" class="w-5 h-5"></i>
                            Оплатить {{ number_format($plan->price, 0, ',', ' ') }} ₽
                        </button>
                    </form>

                    <p class="text-xs text-gray-500 text-center mt-4">
                        Нажимая «Оплатить», вы соглашаетесь с
                        <a href="{{ route('offer') }}" class="text-green-600 hover:underline">офертой</a> и
                        <a href="{{ route('privacy') }}" class="text-green-600 hover:underline">политикой конфиденциальности</a>
                    </p>
                </div>
            </div>

            {{-- Что включено --}}
            <div class="md:col-span-2">
                <div class="sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Что включено</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2.5">
                            <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-green-600"></i>
                            </div>
                            <span class="text-sm text-gray-700">Меню на каждый день</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-green-600"></i>
                            </div>
                            <span class="text-sm text-gray-700">Рецепты с КБЖУ</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-green-600"></i>
                            </div>
                            <span class="text-sm text-gray-700">Списки покупок</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-green-600"></i>
                            </div>
                            <span class="text-sm text-gray-700">Экспорт в PDF</span>
                        </li>
                        @if($plan->type === 'yearly' || $plan->type === 'personal')
                            <li class="flex items-start gap-2.5">
                                <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i data-lucide="check" class="w-3 h-3 text-green-600"></i>
                                </div>
                                <span class="text-sm text-gray-700">Доступ к архиву меню</span>
                            </li>
                        @endif
                        @if($plan->type === 'personal')
                            <li class="flex items-start gap-2.5">
                                <div class="w-5 h-5 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i data-lucide="star" class="w-3 h-3 text-purple-600"></i>
                                </div>
                                <span class="text-sm text-gray-700">Персональный план питания</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <div class="w-5 h-5 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i data-lucide="star" class="w-3 h-3 text-purple-600"></i>
                                </div>
                                <span class="text-sm text-gray-700">Консультация нутрициолога</span>
                            </li>
                        @endif
                    </ul>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <ul class="space-y-2 text-xs text-gray-500">
                            <li class="flex items-center gap-2">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-green-500"></i>
                                Безопасная оплата
                            </li>
                            <li class="flex items-center gap-2">
                                <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-green-500"></i>
                                Отмена в любой момент
                            </li>
                            <li class="flex items-center gap-2">
                                <i data-lucide="credit-card" class="w-3.5 h-3.5 text-green-500"></i>
                                Visa, MasterCard, МИР
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function applyCoupon() {
        const code = document.getElementById('coupon').value.trim();
        const msg = document.getElementById('couponMessage');
        if (!code) return;

        document.getElementById('coupon_code').value = code;
        msg.textContent = 'Промокод «' + code + '» будет применён при оплате';
        msg.className = 'mt-1 text-sm text-green-600';
    }
</script>
@endsection
