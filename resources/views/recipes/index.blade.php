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
    <section class="hero-gradient py-10 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-2 sm:mb-4">Рецепты</h1>
            <p class="text-base sm:text-xl text-green-100 max-w-2xl mx-auto">Вкусные и полезные блюда с подсчитанными калориями и БЖУ</p>
        </div>
    </section>

    <!-- Filters -->
    <div class="bg-white border-b border-gray-100 sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-4">
            <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                <span class="text-gray-500 font-medium text-sm sm:text-base hidden sm:inline">Фильтры:</span>
                <a href="{{ route('recipes.index') }}" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium {{ !request('category') ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                    Все
                </a>
                <a href="{{ route('recipes.index', ['category' => 'breakfast']) }}" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium {{ request('category') == 'breakfast' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                    Завтраки
                </a>
                <a href="{{ route('recipes.index', ['category' => 'lunch']) }}" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium {{ request('category') == 'lunch' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                    Обеды
                </a>
                <a href="{{ route('recipes.index', ['category' => 'dinner']) }}" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium {{ request('category') == 'dinner' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                    Ужины
                </a>
                <a href="{{ route('recipes.index', ['category' => 'snack']) }}" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium {{ request('category') == 'snack' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                    Перекусы
                </a>
            </div>
        </div>
    </div>

    @php
        $userHasAccess = auth()->check() && auth()->user()->canAccessContent();
    @endphp

    <!-- Recipes Grid -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-12">
        @if($recipes->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6">
                @foreach($recipes as $recipe)
                    <a href="{{ route('recipes.show', $recipe) }}" class="bg-white rounded-xl sm:rounded-2xl shadow-sm overflow-hidden card-hover group relative">
                        {{-- Бейдж доступа --}}
                        @if($recipe->is_free)
                            <div class="absolute top-2 left-2 z-10 px-2 py-0.5 bg-green-500 text-white rounded-full text-[10px] sm:text-xs font-bold shadow-lg flex items-center gap-1">
                                <i data-lucide="lock-open" class="w-3 h-3"></i>
                                <span class="hidden sm:inline">Бесплатно</span>
                                <span class="sm:hidden">Free</span>
                            </div>
                        @elseif(!$userHasAccess)
                            <div class="absolute top-2 right-2 z-10 w-7 h-7 sm:w-8 sm:h-8 bg-black/40 backdrop-blur-sm text-white rounded-full flex items-center justify-center">
                                <i data-lucide="lock" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
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
                            
                            <p class="text-xs sm:text-sm text-gray-500 mb-2 sm:mb-3 line-clamp-2 hidden sm:block">{{ $recipe->description }}</p>
                            
                            @if($recipe->ratings_count > 0)
                            <div class="flex items-center gap-1 mb-2 sm:mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 {{ $i <= round($recipe->rating) ? 'text-yellow-400 fill-yellow-400' : 'text-gray-200 fill-gray-200' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                @endfor
                                <span class="text-[10px] sm:text-xs text-gray-400 ml-0.5">{{ number_format($recipe->rating, 1) }}</span>
                            </div>
                            @endif
                            
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
