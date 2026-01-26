@extends('layouts.public')

@section('title', 'О нас — RawPlan')
@section('description', 'RawPlan — сервис готовых планов питания для похудения. Узнайте о нашей миссии, команде и подходе к здоровому питанию.')
@section('keywords', 'о нас, RawPlan, команда, миссия, здоровое питание, похудение')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-6">О нас</h1>
        <p class="text-xl text-green-100 max-w-2xl mx-auto">
            Мы помогаем людям худеть вкусно и без стресса с 2023 года
        </p>
    </div>
</section>

<!-- Mission Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="text-green-600 font-semibold text-sm uppercase tracking-wider">Наша миссия</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3 mb-6">
                    Сделать здоровое питание доступным каждому
                </h2>
                <p class="text-lg text-gray-600 mb-6">
                    Мы верим, что правильное питание не должно быть сложным. Поэтому создали RawPlan — сервис, 
                    который берёт на себя всю рутину: подсчёт калорий, планирование меню, составление списков покупок.
                </p>
                <p class="text-lg text-gray-600 mb-6">
                    Наши планы питания разработаны профессиональными нутрициологами и содержат 1200–1400 ккал в день — 
                    оптимальный дефицит для здорового похудения без голодания.
                </p>
                <div class="flex items-center gap-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">10 000+</div>
                        <div class="text-gray-500 text-sm">пользователей</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">1 200+</div>
                        <div class="text-gray-500 text-sm">рецептов</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">50+</div>
                        <div class="text-gray-500 text-sm">меню</div>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-3xl p-8 lg:p-12">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-white rounded-2xl p-6 shadow-sm">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                                <i data-lucide="heart" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Забота о здоровье</h3>
                            <p class="text-sm text-gray-600">Сбалансированное питание без экстремальных диет</p>
                        </div>
                        <div class="bg-white rounded-2xl p-6 shadow-sm">
                            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                                <i data-lucide="clock" class="w-6 h-6 text-orange-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Экономия времени</h3>
                            <p class="text-sm text-gray-600">Готовые меню и списки покупок</p>
                        </div>
                        <div class="bg-white rounded-2xl p-6 shadow-sm">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                                <i data-lucide="calculator" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Точный расчёт</h3>
                            <p class="text-sm text-gray-600">КБЖУ посчитаны для каждого блюда</p>
                        </div>
                        <div class="bg-white rounded-2xl p-6 shadow-sm">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                                <i data-lucide="sparkles" class="w-6 h-6 text-purple-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Простота</h3>
                            <p class="text-sm text-gray-600">Рецепты за 30 минут из доступных продуктов</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-green-600 font-semibold text-sm uppercase tracking-wider">Наши ценности</span>
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3 mb-4">Что для нас важно</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-green-100 flex items-center justify-center">
                    <i data-lucide="shield-check" class="w-8 h-8 text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Научный подход</h3>
                <p class="text-gray-600">
                    Все наши рекомендации основаны на современных исследованиях в области нутрициологии и диетологии.
                </p>
            </div>
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-orange-100 flex items-center justify-center">
                    <i data-lucide="users" class="w-8 h-8 text-orange-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Забота о клиентах</h3>
                <p class="text-gray-600">
                    Мы всегда на связи и готовы помочь. Ваш успех — наша главная цель.
                </p>
            </div>
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-blue-100 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-8 h-8 text-blue-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Постоянное развитие</h3>
                <p class="text-gray-600">
                    Мы регулярно обновляем меню, добавляем новые рецепты и улучшаем сервис на основе ваших отзывов.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 hero-gradient">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">Готовы начать?</h2>
        <p class="text-xl text-green-100 mb-10">
            Присоединяйтесь к тысячам людей, которые уже изменили свою жизнь с RawPlan
        </p>
        @auth
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-10 py-4 bg-white text-green-700 rounded-xl font-bold text-lg hover:bg-green-50 transition shadow-xl">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                Перейти в кабинет
            </a>
        @else
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-10 py-4 bg-white text-green-700 rounded-xl font-bold text-lg hover:bg-green-50 transition shadow-xl">
                <i data-lucide="rocket" class="w-5 h-5"></i>
                Попробовать бесплатно
            </a>
        @endauth
    </div>
</section>
@endsection
