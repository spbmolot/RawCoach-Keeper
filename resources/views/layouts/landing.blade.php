{{-- ============================================= --}}
{{-- LANDING LAYOUT - Главная страница (лендинг)  --}}
{{-- Использует единые header и footer            --}}
{{-- variant='landing' для якорной навигации      --}}
{{-- ============================================= --}}

@extends('layouts.base')

{{-- Передаём параметры в base layout --}}
@php
    $headerVariant = 'landing';
@endphp

{{-- Schema.org для лендинга --}}
@push('schema')
    <x-schema-organization />
    <x-schema-website />
    @stack('landing-schema')
@endpush

{{-- Ассеты для лендинга --}}
@section('head-assets')
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .feature-icon {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        }
        .pulse-dot {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .stats-card {
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        /* Адаптивные стили для маленьких экранов */
        @media (max-width: 360px) {
            .hero-title { font-size: 1.75rem !important; line-height: 1.2; }
            .hero-subtitle { font-size: 0.95rem !important; }
            .hero-badge { font-size: 0.7rem !important; padding: 0.375rem 0.75rem !important; }
            .hero-btn { padding: 0.75rem 1.25rem !important; font-size: 0.875rem !important; }
            .hero-stats { gap: 0.5rem !important; }
            .hero-stats > div { padding: 0.5rem 0.625rem !important; }
            .hero-stats .stat-value { font-size: 1.25rem !important; }
            .hero-stats .stat-label { font-size: 0.65rem !important; }
            .section-title { font-size: 1.375rem !important; }
            .section-subtitle { font-size: 0.875rem !important; }
            .price-card { padding: 1rem !important; }
            .price-value { font-size: 1.75rem !important; }
            .price-btn { padding: 0.625rem 1rem !important; font-size: 0.8rem !important; }
            .feature-list li { font-size: 0.8rem !important; }
            .faq-question { font-size: 0.9rem !important; }
            .faq-answer { font-size: 0.8rem !important; }
        }
        
        @media (max-width: 320px) {
            .hero-title { font-size: 1.5rem !important; }
            .hero-stats { flex-wrap: wrap; justify-content: center; }
            .hero-stats > div { flex: 0 0 auto; }
            .price-card { padding: 0.875rem !important; }
            .price-value { font-size: 1.5rem !important; }
        }
    </style>
    
    @stack('landing-styles')
@endsection

@section('body-attributes')class="antialiased text-gray-900 bg-white"@endsection

@section('body')
    <!-- Page Content -->
    <main>
        @yield('content')
    </main>
    
    {{-- Scroll to Top Button --}}
    <x-scroll-to-top />
    
    {{-- Cookie Consent --}}
    <x-cookie-consent />
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    lucide.createIcons();
    
    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true,
        offset: 50
    });
</script>
<!-- Alpine.js (на страницах без Livewire, загружается после Lucide) -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
@stack('landing-scripts')
@endpush
