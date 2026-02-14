@extends('layouts.app')

@section('title', 'Демо доступ - RawPlan')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4">Демо доступ</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Попробуйте наши рецепты бесплатно! Ограниченный набор рецептов для знакомства с платформой.
            </p>
        </div>

        @if($demoRecipes->count() > 0)
        <div class="grid md:grid-cols-3 gap-8 mb-12">
            @foreach($demoRecipes as $recipe)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                @if($recipe->image)
                <img src="{{ Storage::url($recipe->image) }}" alt="{{ $recipe->title }}" class="w-full h-48 object-cover">
                @endif
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2">{{ $recipe->title }}</h3>
                    <p class="text-gray-600 mb-4">{{ Str::limit($recipe->description, 100) }}</p>
                    <div class="flex justify-between text-sm text-gray-500 mb-4">
                        <span>{{ $recipe->calories }} ккал</span>
                        <span>{{ $recipe->cooking_time }} мин</span>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg text-center">
                        <p class="text-sm text-gray-700 mb-2">Полный рецепт доступен по подписке</p>
                        <a href="{{ route('plans.index') }}" class="text-green-600 font-semibold hover:text-green-700">
                            Оформить подписку →
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-600 mb-6">Демо рецепты временно недоступны</p>
            <a href="{{ route('plans.index') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                Посмотреть планы
            </a>
        </div>
        @endif

        <div class="text-center">
            <div class="bg-white rounded-lg shadow-md p-8 max-w-2xl mx-auto">
                <h2 class="text-2xl font-bold mb-4">Хотите больше рецептов?</h2>
                <p class="text-gray-600 mb-6">
                    Получите доступ к полной библиотеке рецептов, планам питания и спискам покупок
                </p>
                <a href="{{ route('plans.index') }}" class="bg-green-600 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-green-700 transition">
                    Выбрать план подписки
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
