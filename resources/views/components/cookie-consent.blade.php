<div 
    x-data="{ 
        show: false,
        init() {
            if (!localStorage.getItem('cookie_consent')) {
                setTimeout(() => this.show = true, 1500);
            }
        },
        accept() {
            localStorage.setItem('cookie_consent', 'accepted');
            this.show = false;
        },
        decline() {
            localStorage.setItem('cookie_consent', 'declined');
            this.show = false;
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-0 left-0 right-0 z-[70] p-3 sm:p-4 md:p-6"
    style="display: none;"
>
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl border border-gray-100 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
            <div class="hidden sm:block flex-shrink-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <i data-lucide="cookie" class="w-5 h-5 sm:w-6 sm:h-6 text-green-600"></i>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 text-sm sm:text-base mb-1">🍪 Мы используем cookies</h3>
                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                    Продолжая использовать сайт, вы соглашаетесь с 
                    <a href="{{ route('privacy') }}" class="text-green-600 hover:underline">политикой конфиденциальности</a>.
                </p>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto mt-2 sm:mt-0">
                <button 
                    @click="decline()" 
                    class="flex-1 sm:flex-none px-3 sm:px-4 py-2 text-gray-600 hover:text-gray-900 text-sm font-medium transition"
                >
                    Отклонить
                </button>
                <button 
                    @click="accept()" 
                    class="flex-1 sm:flex-none px-4 sm:px-6 py-2 sm:py-2.5 bg-green-500 text-white rounded-xl text-sm font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25"
                >
                    Принять
                </button>
            </div>
        </div>
    </div>
</div>
