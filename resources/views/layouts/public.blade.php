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
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        :root {
            --color-primary: #22c55e;
            --color-primary-dark: #16a34a;
            --gradient-primary: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            --gradient-hero: linear-gradient(135deg, #065f46 0%, #047857 50%, #10b981 100%);
        }
        body { font-family: 'Inter', system-ui, sans-serif; }
        .gradient-text {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-gradient {
            background: var(--gradient-hero);
        }
    </style>
    
    @stack('styles')
</head>
<body class="antialiased text-gray-900 bg-white">
<div class="flex flex-col min-h-screen">

    {{-- Header Component --}}
    <x-public-header :activeNav="$activeNav ?? null" />

    <!-- Page Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer Component --}}
    <x-public-footer />
</div>

<script>
    lucide.createIcons();
</script>
@stack('scripts')
</body>
</html>
