{{-- ============================================= --}}
{{-- GUEST LAYOUT - Логин/Регистрация             --}}
{{-- Минимальный header, без footer               --}}
{{-- ============================================= --}}

@extends('layouts.base')

{{-- Скрываем footer для страниц авторизации --}}
@php
    $hideFooter = true;
@endphp

{{-- Минимальный header только с логотипом --}}
@section('header')
<header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl hero-gradient flex items-center justify-center">
                    <i data-lucide="salad" class="w-6 h-6 text-white"></i>
                </div>
                <span class="text-xl font-bold text-gray-900">RawPlan</span>
            </a>
        </div>
    </div>
</header>
@endsection

{{-- Ассеты для guest layout --}}
@section('head-assets')
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Styles -->
    @livewireStyles
@endsection

@section('body')
    <div class="font-sans text-gray-900 antialiased min-h-screen bg-gray-50">
        {{ $slot }}
    </div>

    @livewireScripts
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
