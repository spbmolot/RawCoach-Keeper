<button 
    x-data="{ show: false }"
    x-on:scroll.window="show = window.scrollY > 500"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-75"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-75"
    @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
    class="fixed bottom-20 right-4 sm:bottom-6 sm:right-6 z-[60] w-12 h-12 bg-green-500 text-white rounded-full shadow-xl shadow-green-500/40 hover:bg-green-600 hover:scale-110 active:scale-95 transition-all flex items-center justify-center"
    title="Наверх"
    aria-label="Прокрутить наверх"
    style="display: none;"
>
    <i data-lucide="chevron-up" class="w-6 h-6"></i>
</button>
