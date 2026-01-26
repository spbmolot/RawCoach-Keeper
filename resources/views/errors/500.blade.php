@extends('layouts.public')

@section('title', 'Ошибка сервера — RawPlan')
@section('description', 'Произошла ошибка на сервере.')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center py-20">
    <div class="text-center px-4">
        <div class="w-32 h-32 mx-auto mb-8 rounded-3xl bg-red-500 flex items-center justify-center shadow-2xl shadow-red-500/30">
            <i data-lucide="alert-triangle" class="w-16 h-16 text-white"></i>
        </div>
        
        <h1 class="text-6xl font-extrabold text-gray-900 mb-4">500</h1>
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Ошибка сервера</h2>
        <p class="text-gray-500 max-w-md mx-auto mb-8">
            Что-то пошло не так. Мы уже работаем над исправлением. Попробуйте обновить страницу.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                <i data-lucide="home" class="w-5 h-5"></i>
                На главную
            </a>
            <button onclick="location.reload()" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition border border-gray-200">
                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                Обновить
            </button>
        </div>
    </div>
</div>
@endsection
