<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        {{-- SEO Meta Tags --}}
        <title>@yield('title', config('app.name', 'RawPlan') . ' - Планы питания для похудения')</title>
        <meta name="description" content="@yield('description', 'RawPlan - готовые планы питания на 1200-1400 ккал для похудения. Рецепты с КБЖУ, списки покупок, меню на месяц.')">
        <meta name="keywords" content="@yield('keywords', 'план питания, похудение, рецепты, КБЖУ, меню на месяц, здоровое питание, диета')">
        <meta name="author" content="RawPlan">
        <link rel="canonical" href="{{ url()->current() }}">
        
        {{-- Open Graph --}}
        <meta property="og:title" content="@yield('og_title', config('app.name'))">
        <meta property="og:description" content="@yield('og_description', 'Готовые планы питания для похудения')">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="RawPlan">
        <meta property="og:locale" content="ru_RU">
        
        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="@yield('twitter_title', config('app.name'))">
        <meta name="twitter:description" content="@yield('twitter_description', 'Готовые планы питания для похудения')">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        
        {{-- Analytics --}}
        @include('components.analytics')
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
