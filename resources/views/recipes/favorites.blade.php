@extends('layouts.public')

@section('title', 'Избранные рецепты — RawPlan')
@section('description', 'Ваши избранные рецепты для похудения.')

@php $activeNav = 'recipes'; @endphp

@push('styles')
<style>
    .card-hover { transition: all 0.3s ease; }
    .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }
</style>
@endpush

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Избранные рецепты</h1>
            <p class="text-xl text-green-100 max-w-2xl mx-auto">Ваша коллекция любимых блюд</p>
        </div>
    </section>

    <!-- Back Link -->
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <a href="{{ route('recipes.index') }}" class="inline-flex items-center text-green-600 hover:text-green-700 font-medium">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Все рецепты
            </a>
        </div>
    </div>

    <!-- Recipes Grid -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($favoriteRecipes->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($favoriteRecipes as $recipe)
                    <a href="{{ route('recipes.show', $recipe) }}" class="bg-white rounded-2xl shadow-sm overflow-hidden card-hover group">
                        @if($recipe->image)
                            <img src="{{ Storage::url($recipe->image) }}" alt="{{ $recipe->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                                <i data-lucide="chef-hat" class="w-16 h-16 text-green-400"></i>
                            </div>
                        @endif
                        
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-md text-xs font-medium">
                                    @switch($recipe->category)
                                        @case('breakfast') Завтрак @break
                                        @case('lunch') Обед @break
                                        @case('dinner') Ужин @break
                                        @case('snack') Перекус @break
                                        @default {{ $recipe->category }}
                                    @endswitch
                                </span>
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-md text-xs font-medium">
                                    {{ $recipe->prep_time + $recipe->cook_time }} мин
                                </span>
                                <span class="px-2 py-1 bg-red-100 text-red-600 rounded-md text-xs font-medium">
                                    <i data-lucide="heart" class="w-3 h-3 inline"></i>
                                </span>
                            </div>
                            
                            <h3 class="font-bold text-gray-900 mb-2 group-hover:text-green-600 transition line-clamp-2">{{ $recipe->title }}</h3>
                            
                            <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $recipe->description }}</p>
                            
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-1 text-orange-500">
                                    <i data-lucide="flame" class="w-4 h-4"></i>
                                    <span class="font-semibold">{{ $recipe->calories }}</span>
                                    <span class="text-gray-400 text-sm">ккал</span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-gray-500">
                                    <span>Б: {{ $recipe->proteins }}г</span>
                                    <span>Ж: {{ $recipe->fats }}г</span>
                                    <span>У: {{ $recipe->carbs }}г</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($favoriteRecipes->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $favoriteRecipes->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <i data-lucide="heart" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">У вас пока нет избранных рецептов</h3>
                <p class="text-gray-500 mb-6">Добавляйте понравившиеся рецепты в избранное, чтобы быстро находить их</p>
                <a href="{{ route('recipes.index') }}" class="inline-flex items-center px-6 py-3 bg-green-500 text-white font-semibold rounded-xl hover:bg-green-600 transition">
                    <i data-lucide="search" class="w-5 h-5 mr-2"></i>
                    Смотреть рецепты
                </a>
            </div>
        @endif
    </main>

@endsection
