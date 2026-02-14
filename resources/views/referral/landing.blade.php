@extends('layouts.base')

@section('title', 'Приглашение в RawPlan — скидка 15% на подписку')
@section('meta_description', 'Присоединяйтесь к RawPlan по приглашению и получите скидку 15% на первую подписку. Здоровое питание, рецепты, меню и списки покупок.')

@section('head-assets')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
@endsection

@section('body-attributes')class="font-sans antialiased bg-gradient-to-b from-green-50 to-white min-h-screen"@endsection

@section('body')
    <div class="max-w-2xl mx-auto px-4 py-8 sm:py-16">
        {{-- Логотип --}}
        <div class="text-center mb-8 sm:mb-12">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-lg">R</span>
                </div>
                <span class="text-xl font-bold text-gray-900">RawPlan</span>
            </a>
        </div>

        {{-- Главная карточка --}}
        <div class="bg-white rounded-3xl shadow-2xl shadow-green-500/10 overflow-hidden">
            {{-- Баннер --}}
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6 sm:p-10 text-white text-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-6 backdrop-blur-sm">
                    <i data-lucide="gift" class="w-8 h-8 sm:w-10 sm:h-10"></i>
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold mb-3">
                    Скидка {{ $discount }}% для вас!
                </h1>
                <p class="text-green-100 text-sm sm:text-lg max-w-md mx-auto">
                    {{ $referrer->name }} приглашает вас в <strong>RawPlan</strong> — сервис здорового питания с персональными меню, рецептами и списками покупок
                </p>
            </div>

            {{-- Что получите --}}
            <div class="p-6 sm:p-10">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 text-center">Что вы получите</h2>
                
                <div class="space-y-4 mb-6 sm:mb-8">
                    <div class="flex gap-3 sm:gap-4 items-start">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="salad" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Персональные меню</h3>
                            <p class="text-sm text-gray-500">Сбалансированное питание с учётом ваших предпочтений</p>
                        </div>
                    </div>
                    <div class="flex gap-3 sm:gap-4 items-start">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="chef-hat" class="w-5 h-5 text-amber-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Сотни рецептов</h3>
                            <p class="text-sm text-gray-500">Пошаговые рецепты с расчётом КБЖУ на каждую порцию</p>
                        </div>
                    </div>
                    <div class="flex gap-3 sm:gap-4 items-start">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="shopping-cart" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Умные списки покупок</h3>
                            <p class="text-sm text-gray-500">Автоматические списки на неделю с возможностью поделиться</p>
                        </div>
                    </div>
                    <div class="flex gap-3 sm:gap-4 items-start">
                        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="trending-up" class="w-5 h-5 text-orange-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Трекинг прогресса</h3>
                            <p class="text-sm text-gray-500">Дневник питания, график веса и достижения</p>
                        </div>
                    </div>
                </div>

                {{-- Скидка --}}
                <div class="bg-green-50 rounded-2xl p-4 sm:p-6 mb-6 sm:mb-8 text-center border border-green-200">
                    <div class="text-3xl sm:text-4xl font-extrabold text-green-600 mb-1">−{{ $discount }}%</div>
                    <p class="text-green-700 font-medium">на первую подписку по приглашению</p>
                </div>

                {{-- CTA --}}
                <div class="space-y-3">
                    <a href="{{ route('register') }}" 
                       class="block w-full py-4 bg-green-500 text-white text-center rounded-2xl font-bold text-lg hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                        Зарегистрироваться со скидкой
                    </a>
                    <a href="{{ route('login') }}" 
                       class="block w-full py-3 text-center text-gray-500 text-sm hover:text-gray-700 transition">
                        Уже есть аккаунт? Войти
                    </a>
                </div>
            </div>
        </div>

        {{-- Футер --}}
        <p class="text-center text-xs text-gray-400 mt-6 sm:mt-8">
            Реферальный код: <span class="font-mono font-bold">{{ $code }}</span> · 
            <a href="{{ route('home') }}" class="underline hover:text-gray-600">rawplan.ru</a>
        </p>
    </div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
