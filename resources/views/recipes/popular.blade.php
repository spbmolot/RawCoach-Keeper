@extends('layouts.public')

@section('title', 'Популярные рецепты — RawPlan')
@section('description', 'Самые популярные рецепты для похудения на RawPlan. Завтраки, обеды, ужины и перекусы с КБЖУ.')

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
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-2 sm:mb-4">Популярные рецепты</h1>
            <p class="text-base sm:text-xl text-green-100 max-w-2xl mx-auto">Самые просматриваемые рецепты на платформе</p>
        </div>
    </section>

    <!-- Navigation -->
    <div class="bg-white border-b border-gray-100 sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-4">
            <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                <a href="{{ route('recipes.index') }}" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    Все рецепты
                </a>
                <span class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium bg-green-500 text-white">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5 inline-block mr-1"></i> Популярные
                </span>
                <a href="{{ route('recipes.favorites') }}" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    Избранное
                </a>
            </div>
        </div>
    </div>

    <!-- Recipes Grid -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-12">
        @if($recipes->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6">
                @foreach($recipes as $index => $recipe)
                    <a href="{{ route('recipes.show', $recipe) }}" class="bg-white rounded-xl sm:rounded-2xl shadow-sm overflow-hidden card-hover group relative">
                        @if($index < 3)
                            <div class="absolute top-2 left-2 z-10">
                                <span class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full text-xs sm:text-sm font-bold shadow-lg {{ $index === 0 ? 'bg-yellow-400 text-yellow-900' : ($index === 1 ? 'bg-gray-300 text-gray-700' : 'bg-amber-600 text-white') }}">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                        @endif

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
                                    @switch($recipe->category)
                                        @case('breakfast') Завтрак @break
                                        @case('lunch') Обед @break
                                        @case('dinner') Ужин @break
                                        @case('snack') Перекус @break
                                        @default {{ $recipe->category }}
                                    @endswitch
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
                <i data-lucide="trending-up" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Пока нет популярных рецептов</h3>
                <p class="text-gray-500 mb-6">Статистика просмотров ещё не накопилась</p>
                <a href="{{ route('recipes.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-full font-semibold transition">
                    <i data-lucide="book-open" class="w-5 h-5"></i> Все рецепты
                </a>
            </div>
        @endif
    </main>
@endsection
