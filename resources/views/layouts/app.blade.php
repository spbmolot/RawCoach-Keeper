{{-- ============================================= --}}
{{-- APP LAYOUT - Личный кабинет (Jetstream)      --}}
{{-- Использует Livewire navigation-menu          --}}
{{-- Header и footer скрыты (свой интерфейс ЛК)   --}}
{{-- ============================================= --}}

@extends('layouts.base')

{{-- Скрываем стандартные header/footer для ЛК --}}
@php
    $hideFooter = true;
@endphp

{{-- Используем единый header RawPlan с variant='app' --}}
{{-- Header берётся из base.blade.php через x-header --}}

{{-- Ассеты для app layout (ЛК) --}}
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

@section('body-attributes')class="font-sans antialiased"@endsection

@php
    $headerVariant = 'app';
@endphp

@section('body')
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="px-3 sm:px-6 pb-12 pt-4 sm:pt-6">
            @if(isset($slot) && $slot->isNotEmpty())
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </main>
    </div>

    @stack('modals')

    @livewireScripts
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
