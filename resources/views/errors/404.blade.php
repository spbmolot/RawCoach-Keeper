@extends('layouts.public')

@section('title', 'Страница не найдена — RawPlan')
@section('description', 'Запрашиваемая страница не найдена.')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center py-20">
    <div class="text-center px-4">
        <div class="w-32 h-32 mx-auto mb-8 rounded-3xl hero-gradient flex items-center justify-center shadow-2xl shadow-green-500/30">
            <i data-lucide="search-x" class="w-16 h-16 text-white"></i>
        </div>
        
        <h1 class="text-6xl font-extrabold text-gray-900 mb-4">404</h1>
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Страница не найдена</h2>
        <p class="text-gray-500 max-w-md mx-auto mb-8">
            К сожалению, запрашиваемая страница не существует или была перемещена.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                <i data-lucide="home" class="w-5 h-5"></i>
                На главную
            </a>
            <a href="{{ route('menus.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition border border-gray-200">
                <i data-lucide="utensils" class="w-5 h-5"></i>
                Смотреть меню
            </a>
        </div>
        
        <div class="mt-12 pt-8 border-t border-gray-100">
            <p class="text-sm text-gray-400">
                Нужна помощь? <a href="{{ route('contact') }}" class="text-green-600 hover:underline">Свяжитесь с нами</a>
            </p>
        </div>
    </div>
</div>
@endsection
