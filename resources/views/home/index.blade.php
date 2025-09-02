@extends('layouts.app')

@section('title', 'RawPlan - Планы питания для похудения')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-600 to-blue-600 text-white">
        <div class="container mx-auto px-4 py-16">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Планы питания для <span class="text-yellow-300">похудения</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                    Готовые планы питания с подробными рецептами, граммовкой и калорийностью. 
                    1200-1400 ккал в день для эффективного похудения.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('plans.index') }}" class="bg-yellow-400 text-gray-900 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-yellow-300 transition">
                        Выбрать план
                    </a>
                    <a href="{{ route('home.demo') }}" class="border-2 border-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-gray-900 transition">
                        Демо доступ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Почему выбирают RawPlan?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Готовые рецепты</h3>
                    <p class="text-gray-600">Подробные рецепты с граммовкой ингредиентов и пошаговыми инструкциями</p>
                </div>
                <div class="text-center p-6">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Точная калорийность</h3>
                    <p class="text-gray-600">1200-1400 ккал в день с подсчетом БЖУ для эффективного похудения</p>
                </div>
                <div class="text-center p-6">
                    <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Экономия времени</h3>
                    <p class="text-gray-600">Готовые списки покупок и планы на каждый день месяца</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Plans Section -->
    @if($plans->count() > 0)
    <div class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Выберите свой план</h2>
            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @foreach($plans as $plan)
                <div class="border rounded-lg p-6 {{ $plan->is_popular ? 'border-green-500 relative' : 'border-gray-200' }}">
                    @if($plan->is_popular)
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                        <span class="bg-green-500 text-white px-4 py-1 rounded-full text-sm font-semibold">Популярный</span>
                    </div>
                    @endif
                    <div class="text-center">
                        <h3 class="text-2xl font-bold mb-2">{{ $plan->name }}</h3>
                        <div class="text-3xl font-bold text-green-600 mb-4">
                            {{ number_format($plan->price, 0, ',', ' ') }} ₽
                            <span class="text-sm text-gray-500 font-normal">/{{ $plan->duration_days }} дней</span>
                        </div>
                        <p class="text-gray-600 mb-6">{{ $plan->description }}</p>
                        <a href="{{ route('plans.show', $plan) }}" class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition inline-block">
                            Подробнее
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Featured Recipes -->
    @if($featuredRecipes->count() > 0)
    <div class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Популярные рецепты</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($featuredRecipes as $recipe)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    @if($recipe->image)
                    <img src="{{ Storage::url($recipe->image) }}" alt="{{ $recipe->name }}" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">{{ $recipe->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($recipe->description, 100) }}</p>
                        <div class="flex justify-between text-sm text-gray-500 mb-4">
                            <span>{{ $recipe->calories }} ккал</span>
                            <span>{{ $recipe->cooking_time }} мин</span>
                        </div>
                        <a href="{{ route('recipes.show', $recipe) }}" class="text-green-600 font-semibold hover:text-green-700">
                            Читать рецепт →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- CTA Section -->
    <div class="py-16 bg-green-600 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4">Начните худеть уже сегодня!</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">
                Присоединяйтесь к тысячам довольных клиентов, которые уже достигли своих целей с RawPlan
            </p>
            <a href="{{ route('plans.index') }}" class="bg-yellow-400 text-gray-900 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-yellow-300 transition">
                Выбрать план питания
            </a>
        </div>
    </div>
</div>
@endsection
