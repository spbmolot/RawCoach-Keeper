<button 
    x-data="{ show: false }"
    x-on:scroll.window="show = window.scrollY > 500"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
    class="fixed bottom-6 right-6 z-50 w-12 h-12 bg-green-500 text-white rounded-full shadow-lg shadow-green-500/30 hover:bg-green-600 transition flex items-center justify-center"
    title="Наверх"
    style="display: none;"
>
    <i data-lucide="chevron-up" class="w-6 h-6"></i>
</button>
