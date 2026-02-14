@props(['locked' => false])

@if($locked)
    <div class="relative">
        {{-- Размытый контент --}}
        <div class="blur-sm select-none pointer-events-none" aria-hidden="true">
            {{ $slot }}
        </div>

        {{-- Оверлей с CTA --}}
        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-t from-white/95 via-white/80 to-white/40 rounded-2xl">
            <div class="text-center px-6 py-8 max-w-sm">
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="lock" class="w-8 h-8 text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Доступно по подписке</h3>
                <p class="text-gray-500 text-sm mb-6">Оформите подписку, чтобы получить полный доступ к рецептам, меню и спискам покупок</p>
                <a href="{{ route('plans.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition shadow-lg shadow-green-500/25">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                    Оформить подписку
                </a>
                @guest
                    <p class="mt-4 text-xs text-gray-400">
                        Уже есть аккаунт? <a href="{{ route('login') }}" class="text-green-600 hover:underline">Войти</a>
                    </p>
                @endguest
            </div>
        </div>
    </div>
@else
    {{ $slot }}
@endif
