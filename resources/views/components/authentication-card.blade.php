<div class="min-h-screen flex flex-col lg:flex-row">
    <!-- Левая часть - градиентный баннер (скрыт на мобильных) -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-green-600 to-blue-600 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="relative z-10 flex flex-col justify-center items-center w-full p-12 text-white">
            <div class="max-w-md text-center">
                <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mx-auto mb-8">
                    <i data-lucide="salad" class="w-10 h-10 text-white"></i>
                </div>
                <h1 class="text-4xl font-bold mb-4">RawPlan</h1>
                <p class="text-xl text-white/90 mb-6">Планы питания для похудения</p>
                <div class="space-y-4 text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                        <span class="text-white/90">Готовые рецепты с граммовкой</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                        <span class="text-white/90">1200-1400 ккал в день</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                        <span class="text-white/90">Списки покупок на неделю</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Декоративные элементы -->
        <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full bg-white/10"></div>
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-white/10"></div>
    </div>

    <!-- Правая часть - форма -->
    <div class="flex-1 flex flex-col justify-center items-center min-h-screen bg-gray-50 px-4 py-8 sm:px-6 lg:px-8">
        <!-- Мобильный логотип -->
        <div class="lg:hidden mb-6 text-center">
            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-600 to-blue-600 flex items-center justify-center mx-auto mb-3">
                <i data-lucide="salad" class="w-7 h-7 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">RawPlan</h1>
            <p class="text-sm text-gray-500 mt-1">Планы питания для похудения</p>
        </div>

        <div class="w-full max-w-[400px] min-w-[280px]">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sm:p-8">
                {{ $slot }}
            </div>
            
            <!-- Футер -->
            <p class="text-center text-xs text-gray-400 mt-6">
                © {{ date('Y') }} RawPlan. Все права защищены.
            </p>
        </div>
    </div>
</div>
