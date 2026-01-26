@extends('layouts.public')

@section('title', 'Сравнение тарифов — RawPlan')
@section('description', 'Сравните тарифы RawPlan и выберите подходящий план питания для похудения.')
@section('keywords', 'сравнение тарифов, подписка, план питания, RawPlan')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-6">Сравнение тарифов</h1>
        <p class="text-xl text-green-100 max-w-2xl mx-auto">
            Выберите план, который подходит именно вам
        </p>
    </div>
</section>

<!-- Comparison Table -->
<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="text-left py-4 px-6 bg-gray-50 rounded-tl-2xl">Функция</th>
                        @foreach($plans as $plan)
                        <th class="text-center py-4 px-6 bg-gray-50 {{ $loop->last ? 'rounded-tr-2xl' : '' }}">
                            <div class="font-bold text-gray-900">{{ $plan->name }}</div>
                            <div class="text-2xl font-bold text-green-600 mt-1">
                                {{ number_format($plan->price, 0, ',', ' ') }} ₽
                            </div>
                            <div class="text-sm text-gray-500">/ {{ $plan->duration_days }} дней</div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="py-4 px-6 text-gray-700">Меню на месяц</td>
                        @foreach($plans as $plan)
                        <td class="py-4 px-6 text-center">
                            <i data-lucide="check" class="w-5 h-5 text-green-500 mx-auto"></i>
                        </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="py-4 px-6 text-gray-700">Рецепты с КБЖУ</td>
                        @foreach($plans as $plan)
                        <td class="py-4 px-6 text-center">
                            <i data-lucide="check" class="w-5 h-5 text-green-500 mx-auto"></i>
                        </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="py-4 px-6 text-gray-700">Списки покупок</td>
                        @foreach($plans as $plan)
                        <td class="py-4 px-6 text-center">
                            <i data-lucide="check" class="w-5 h-5 text-green-500 mx-auto"></i>
                        </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="py-4 px-6 text-gray-700">Экспорт в PDF</td>
                        @foreach($plans as $plan)
                        <td class="py-4 px-6 text-center">
                            <i data-lucide="check" class="w-5 h-5 text-green-500 mx-auto"></i>
                        </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="py-4 px-6 text-gray-700">Доступ к архиву меню</td>
                        @foreach($plans as $plan)
                        <td class="py-4 px-6 text-center">
                            @if($plan->type === 'yearly' || $plan->type === 'personal')
                                <i data-lucide="check" class="w-5 h-5 text-green-500 mx-auto"></i>
                            @else
                                <i data-lucide="x" class="w-5 h-5 text-gray-300 mx-auto"></i>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="py-4 px-6 text-gray-700">Ранний доступ к новинкам</td>
                        @foreach($plans as $plan)
                        <td class="py-4 px-6 text-center">
                            @if($plan->type === 'yearly' || $plan->type === 'personal')
                                <i data-lucide="check" class="w-5 h-5 text-green-500 mx-auto"></i>
                            @else
                                <i data-lucide="x" class="w-5 h-5 text-gray-300 mx-auto"></i>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="py-4 px-6 text-gray-700">Приоритетная поддержка</td>
                        @foreach($plans as $plan)
                        <td class="py-4 px-6 text-center">
                            @if($plan->type === 'yearly' || $plan->type === 'personal')
                                <i data-lucide="check" class="w-5 h-5 text-green-500 mx-auto"></i>
                            @else
                                <i data-lucide="x" class="w-5 h-5 text-gray-300 mx-auto"></i>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="py-4 px-6 text-gray-700">Персональный план питания</td>
                        @foreach($plans as $plan)
                        <td class="py-4 px-6 text-center">
                            @if($plan->type === 'personal')
                                <i data-lucide="check" class="w-5 h-5 text-green-500 mx-auto"></i>
                            @else
                                <i data-lucide="x" class="w-5 h-5 text-gray-300 mx-auto"></i>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="py-4 px-6 text-gray-700">Консультация нутрициолога</td>
                        @foreach($plans as $plan)
                        <td class="py-4 px-6 text-center">
                            @if($plan->type === 'personal')
                                <i data-lucide="check" class="w-5 h-5 text-green-500 mx-auto"></i>
                            @else
                                <i data-lucide="x" class="w-5 h-5 text-gray-300 mx-auto"></i>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="py-6 px-6 bg-gray-50 rounded-bl-2xl"></td>
                        @foreach($plans as $plan)
                        <td class="py-6 px-6 bg-gray-50 text-center {{ $loop->last ? 'rounded-br-2xl' : '' }}">
                            <a href="{{ route('plans.show', $plan) }}" class="inline-block px-6 py-3 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition">
                                Выбрать
                            </a>
                        </td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Не можете определиться?</h2>
        <p class="text-gray-600 mb-6">
            Начните с бесплатного пробного периода 7 дней и оцените все возможности сервиса.
        </p>
        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
            <i data-lucide="rocket" class="w-5 h-5"></i>
            Попробовать бесплатно
        </a>
    </div>
</section>
@endsection
