{{-- ============================================= --}}
{{-- PUBLIC LAYOUT - Публичные страницы           --}}
{{-- Использует единые header и footer            --}}
{{-- ============================================= --}}

@extends('layouts.base')

{{-- Передаём параметры в base layout --}}
@php
    $headerVariant = 'public';
@endphp

{{-- Schema.org для публичных страниц --}}
@push('schema')
    <x-schema-organization />
    <x-schema-website />
@endpush

{{-- Ассеты для публичного layout --}}
@section('head-assets')
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
@endsection

@section('body-attributes')class="antialiased text-gray-900 bg-white"@endsection

@section('body')
    <!-- Page Content -->
    <main class="flex-1">
        @yield('content')
    </main>
    
    {{-- Scroll to Top Button --}}
    <x-scroll-to-top />
    
    {{-- Cookie Consent --}}
    <x-cookie-consent />
    
    {{-- Toast Notifications --}}
    <x-toast />
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
