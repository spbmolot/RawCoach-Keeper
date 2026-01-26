@extends('layouts.public')

@section('title', 'Меню — RawPlan')
@section('description', 'Готовые планы питания на месяц. Меню 1200-1400 ккал для похудения.')

@php $activeNav = 'menus'; @endphp

@section('content')
    <!-- Page Header -->
    <section class="hero-gradient py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Меню на месяц</h1>
            <p class="text-xl text-green-100 max-w-2xl mx-auto">Готовые планы питания с рецептами и списками покупок</p>
        </div>
    </section>

    <!-- Menus Grid -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($menus->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($menus as $menu)
                    <a href="{{ route('menus.show', $menu) }}" class="bg-white rounded-2xl shadow-sm overflow-hidden card-hover group">
                        @if($menu->cover_image)
                            <img src="{{ Storage::url($menu->cover_image) }}" alt="{{ $menu->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <i data-lucide="calendar" class="w-12 h-12 mx-auto mb-2"></i>
                                    <div class="text-3xl font-bold">{{ $menu->getMonthName() ?? 'Меню' }}</div>
                                    <div class="text-lg opacity-80">{{ $menu->year }}</div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-green-600 transition">{{ $menu->title }}</h3>
                            
                            <p class="text-gray-500 mb-4 line-clamp-2">{{ $menu->description }}</p>
                            
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="calendar-days" class="w-4 h-4"></i>
                                        {{ $menu->days->count() }} дней
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="flame" class="w-4 h-4 text-orange-500"></i>
                                        ~{{ $menu->total_calories ?? 1300 }} ккал/день
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
