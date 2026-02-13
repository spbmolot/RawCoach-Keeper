{{-- ============================================= --}}
{{-- GUEST LAYOUT - Логин/Регистрация             --}}
{{-- Минимальный header, без footer               --}}
{{-- ============================================= --}}

@extends('layouts.base')

{{-- Скрываем footer для страниц авторизации --}}
@php
    $hideFooter = true;
@endphp

{{-- Без header для страниц авторизации (логотип в карточке) --}}
@section('header')
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
