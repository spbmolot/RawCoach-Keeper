@extends('layouts.public')

@section('title', 'Меню — RawPlan')
@section('description', 'Готовые планы питания на месяц. Меню 1200-1400 ккал для похудения.')

@php $activeNav = 'menus'; @endphp

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient py-10 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-2 sm:mb-4">Меню на месяц</h1>
            <p class="text-base sm:text-xl text-green-100 max-w-2xl mx-auto">Готовые планы питания с рецептами и списками покупок</p>
        </div>
    </section>

    <!-- Menus Grid -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-12">
        @if($menus->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-8">
                @foreach($menus as $menu)
                    <a href="{{ route('menus.show', $menu) }}" class="bg-white rounded-xl sm:rounded-2xl shadow-sm overflow-hidden card-hover group">
                        @if($menu->cover_image)
                            <img src="{{ Storage::url($menu->cover_image) }}" alt="{{ $menu->title }}" class="w-full h-36 sm:h-48 object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-36 sm:h-48 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <i data-lucide="calendar" class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-1 sm:mb-2"></i>
                                    <div class="text-2xl sm:text-3xl font-bold">{{ $menu->getMonthName() ?? 'Меню' }}</div>
                                    <div class="text-base sm:text-lg opacity-80">{{ $menu->year }}</div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="p-4 sm:p-6">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-1 sm:mb-2 group-hover:text-green-600 transition">{{ $menu->title }}</h3>
                            
                            <p class="text-gray-500 mb-3 sm:mb-4 line-clamp-2 text-sm sm:text-base">{{ $menu->description }}</p>
                            
                            <div class="flex items-center justify-between pt-3 sm:pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-3 sm:gap-4 text-xs sm:text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="calendar-days" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                                        {{ $menu->days->count() }} дней
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="flame" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-orange-500"></i>
                                        ~{{ $menu->total_calories ?? 1300 }} ккал
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <i data-lucide="calendar-x" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Меню пока нет</h3>
                <p class="text-gray-500">Скоро здесь появятся новые планы питания</p>
            </div>
        @endif
    </main>

@endsection
