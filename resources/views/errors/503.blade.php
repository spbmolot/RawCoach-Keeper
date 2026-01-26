@extends('layouts.public')

@section('title', 'Технические работы — RawPlan')
@section('description', 'Сайт временно недоступен.')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center py-20">
    <div class="text-center px-4">
        <div class="w-32 h-32 mx-auto mb-8 rounded-3xl bg-amber-500 flex items-center justify-center shadow-2xl shadow-amber-500/30">
            <i data-lucide="wrench" class="w-16 h-16 text-white"></i>
        </div>
        
        <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Технические работы</h1>
        <p class="text-gray-500 max-w-md mx-auto mb-8">
            Мы проводим плановое обслуживание. Сайт скоро будет доступен. Спасибо за терпение!
        </p>
        
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 max-w-sm mx-auto">
            <p class="text-amber-800 text-sm">
                <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                Ожидаемое время: ~15 минут
            </p>
        </div>
    </div>
</div>
@endsection
