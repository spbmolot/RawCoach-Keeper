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
    class="fixed bottom-0 left-0 right-0 z-50 p-4 md:p-6"
    style="display: none;"
>
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <i data-lucide="cookie" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 mb-1">Мы используем cookies</h3>
                <p class="text-sm text-gray-600">
                    Мы используем файлы cookie для улучшения работы сайта и персонализации контента. 
                    Продолжая использовать сайт, вы соглашаетесь с нашей 
                    <a href="{{ route('privacy') }}" class="text-green-600 hover:underline">политикой конфиденциальности</a>.
                </p>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <button 
                    @click="decline()" 
                    class="flex-1 md:flex-none px-4 py-2 text-gray-600 hover:text-gray-900 font-medium transition"
                >
                    Отклонить
                </button>
                <button 
                    @click="accept()" 
                    class="flex-1 md:flex-none px-6 py-2.5 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25"
                >
                    Принять
                </button>
            </div>
        </div>
    </div>
</div>
