@extends('layouts.public')

@section('title', "{$pageTitle} — Рецепты — RawPlan")
@section('description', "Рецепты для категории «{$pageTitle}» с подсчитанными калориями и БЖУ.")

@php $activeNav = 'recipes'; @endphp

@push('styles')
<style>
    .card-hover { transition: all 0.3s ease; }
    .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }
</style>
@endpush

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient py-10 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-2 sm:mb-4">{{ $pageTitle }}</h1>
            <p class="text-base sm:text-xl text-green-100 max-w-2xl mx-auto">Рецепты с подсчитанными калориями и БЖУ</p>
        </div>
    </section>

    <!-- Meal Type Tabs -->
    <div class="bg-white border-b border-gray-100 sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-4">
            <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                <span class="text-gray-500 font-medium text-sm sm:text-base hidden sm:inline">Тип:</span>
                <a href="{{ route('recipes.index') }}" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    Все
                </a>
                @foreach(['breakfast' => 'Завтраки', 'lunch' => 'Обеды', 'dinner' => 'Ужины', 'snack' => 'Перекусы'] as $type => $label)
                    <a href="{{ route('recipes.by-meal-type', $type) }}" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium {{ $mealType === $type ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recipes Grid -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-12">
        @if($recipes->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6">
                @foreach($recipes as $recipe)
                    <a href="{{ route('recipes.show', $recipe) }}" class="bg-white rounded-xl sm:rounded-2xl shadow-sm overflow-hidden card-hover group">
                        @if($recipe->image)
                            <img src="{{ Storage::url($recipe->image) }}" alt="{{ $recipe->title }}" class="w-full h-32 sm:h-48 object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-32 sm:h-48 bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                                <i data-lucide="chef-hat" class="w-10 h-10 sm:w-16 sm:h-16 text-green-400"></i>
                            </div>
                        @endif

                        <div class="p-3 sm:p-5">
                            <div class="flex flex-wrap items-center gap-1 sm:gap-2 mb-2 sm:mb-3">
                                <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 bg-green-100 text-green-700 rounded text-[10px] sm:text-xs font-medium">
                                    {{ $pageTitle }}
                                </span>
                                <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 bg-gray-100 text-gray-600 rounded text-[10px] sm:text-xs font-medium">
                                    {{ $recipe->prep_time + $recipe->cook_time }} мин
                                </span>
                            </div>

                            <h3 class="font-bold text-gray-900 mb-1 sm:mb-2 group-hover:text-green-600 transition line-clamp-2 text-sm sm:text-base">{{ $recipe->title }}</h3>

                            <p class="text-xs sm:text-sm text-gray-500 mb-2 sm:mb-4 line-clamp-2 hidden sm:block">{{ $recipe->description }}</p>

                            <div class="flex items-center justify-between pt-2 sm:pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-1 text-orange-500">
                                    <i data-lucide="flame" class="w-3 h-3 sm:w-4 sm:h-4"></i>
                                    <span class="font-semibold text-xs sm:text-sm">{{ $recipe->calories }}</span>
                                    <span class="text-gray-400 text-[10px] sm:text-sm">ккал</span>
                                </div>
                                <div class="hidden sm:flex items-center gap-3 text-xs text-gray-500">
                                    <span>Б: {{ $recipe->proteins }}г</span>
                                    <span>Ж: {{ $recipe->fats }}г</span>
                                    <span>У: {{ $recipe->carbs }}г</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($recipes->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $recipes->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <i data-lucide="chef-hat" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Рецептов пока нет</h3>
                <p class="text-gray-500 mb-6">В этой категории пока нет рецептов</p>
                <a href="{{ route('recipes.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-full font-semibold transition">
                    <i data-lucide="book-open" class="w-5 h-5"></i> Все рецепты
                </a>
            </div>
        @endif
    </main>
@endsection
