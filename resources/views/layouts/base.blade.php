<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO Meta Tags --}}
    <title>@yield('title', config('app.name', 'RawPlan') . ' — Планы питания для похудения')</title>
    <meta name="description" content="@yield('description', 'RawPlan — готовые планы питания на 1200-1400 ккал для похудения. Рецепты с КБЖУ, списки покупок, меню на месяц.')">
    <meta name="keywords" content="@yield('keywords', 'план питания, похудение, рецепты, КБЖУ, меню на месяц, здоровое питание, диета')">
    <meta name="author" content="RawPlan">
    <link rel="canonical" href="{{ url()->current() }}">
    
    {{-- Favicon --}}
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/favicon.ico" sizes="32x32">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#22c55e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="RawPlan">
    <meta name="mobile-web-app-capable" content="yes">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', 'Готовые планы питания для похудения')">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="RawPlan">
    <meta property="og:locale" content="ru_RU">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name'))">
    <meta name="twitter:description" content="@yield('twitter_description', 'Готовые планы питания для похудения')">
    <meta name="twitter:image" content="{{ asset('images/og-image.jpg') }}">
    
    {{-- Schema.org (опционально через стек) --}}
    @stack('schema')

    {{-- ============================================= --}}
    {{-- ANALYTICS - Яндекс.Метрика и Google Analytics --}}
    {{-- Счётчики подключаются из одного места          --}}
    {{-- ============================================= --}}
    @include('components.analytics')
    
    {{-- Базовые стили и скрипты --}}
    @yield('head-assets')
    
    {{-- Общие стили для градиентов (используются в header/footer) --}}
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --color-primary: #22c55e;
            --color-primary-dark: #16a34a;
            --gradient-primary: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            --gradient-hero: linear-gradient(135deg, #065f46 0%, #047857 50%, #10b981 100%);
        }
        .hero-gradient {
            background: var(--gradient-hero);
        }
        .gradient-text {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
    
    {{-- Дополнительные стили страницы --}}
    @stack('styles')
</head>
<body @yield('body-attributes')>
    {{-- Шапка (единый компонент) --}}
    @hasSection('header')
        @yield('header')
    @else
        <x-header :variant="$headerVariant ?? 'public'" :activeNav="$activeNav ?? null" />
    @endif
    
    {{-- Flash-сообщение (logout и др.) --}}
    @if(session('logout_message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="fixed top-4 left-1/2 -translate-x-1/2 z-[9999] max-w-md w-full px-4">
            <div class="bg-white rounded-xl shadow-lg border border-green-200 p-4 flex items-center gap-3">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-700">{{ session('logout_message') }}</p>
                <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-600 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- Контент страницы --}}
    @yield('body')
    
    {{-- Футер (единый компонент) --}}
    @hasSection('footer')
        @yield('footer')
    @else
        @if(!($hideFooter ?? false))
            <x-footer />
        @endif
    @endif
    
    {{-- Дополнительные скрипты страницы --}}
    @stack('scripts')

    {{-- PWA: Service Worker --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
