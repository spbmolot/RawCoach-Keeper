@extends('layouts.public')

@section('title', 'Рецепты — RawPlan')
@section('description', 'Каталог рецептов для похудения. Завтраки, обеды, ужины и перекусы с КБЖУ.')

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
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Рецепты</h1>
            <p class="text-xl text-green-100 max-w-2xl mx-auto">Вкусные и полезные блюда с подсчитанными калориями и БЖУ</p>
        </div>
    </section>

    <!-- Filters -->
    <div class="bg-white border-b border-gray-100 sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-wrap items-center gap-4">
                <span class="text-gray-500 font-medium">Фильтры:</span>
                <a href="{{ route('recipes.index') }}" class="px-4 py-2 rounded-full text-sm font-medium {{ !request('category') ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                    Все
                </a>
                <a href="{{ route('recipes.index', ['category' => 'breakfast']) }}" class="px-4 py-2 rounded-full text-sm font-medium {{ request('category') == 'breakfast' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                    Завтраки
                </a>
                <a href="{{ route('recipes.index', ['category' => 'lunch']) }}" class="px-4 py-2 rounded-full text-sm font-medium {{ request('category') == 'lunch' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                    Обеды
                </a>
                <a href="{{ route('recipes.index', ['category' => 'dinner']) }}" class="px-4 py-2 rounded-full text-sm font-medium {{ request('category') == 'dinner' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                    Ужины
                </a>
                <a href="{{ route('recipes.index', ['category' => 'snack']) }}" class="px-4 py-2 rounded-full text-sm font-medium {{ request('category') == 'snack' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                    Перекусы
                </a>
            </div>
        </div>
    </div>

    <!-- Recipes Grid -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($recipes->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($recipes as $recipe)
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
            @if($recipes->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $recipes->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <i data-lucide="search-x" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Рецепты не найдены</h3>
                <p class="text-gray-500">Попробуйте изменить фильтры</p>
            </div>
        @endif
    </main>

@endsection
