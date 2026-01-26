<div 
    x-data="toastNotification()"
    x-on:toast.window="show($event.detail)"
    class="fixed top-4 right-4 z-[100] space-y-3 pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div 
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-8"
            class="pointer-events-auto max-w-sm w-full bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden"
        >
            <div class="p-4 flex items-start gap-3">
                <div 
                    class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center"
                    :class="{
                        'bg-green-100': toast.type === 'success',
                        'bg-red-100': toast.type === 'error',
                        'bg-amber-100': toast.type === 'warning',
                        'bg-blue-100': toast.type === 'info'
                    }"
                >
                    <template x-if="toast.type === 'success'">
                        <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <i data-lucide="x" class="w-4 h-4 text-red-600"></i>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600"></i>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm" x-text="toast.title"></p>
                    <p class="text-gray-500 text-sm mt-0.5" x-text="toast.message" x-show="toast.message"></p>
                </div>
                <button 
                    @click="remove(toast.id)" 
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition"
                >
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div 
                class="h-1 transition-all duration-100"
                :class="{
                    'bg-green-500': toast.type === 'success',
                    'bg-red-500': toast.type === 'error',
                    'bg-amber-500': toast.type === 'warning',
                    'bg-blue-500': toast.type === 'info'
                }"
                :style="'width: ' + toast.progress + '%'"
            ></div>
        </div>
    </template>
</div>

<script>
function toastNotification() {
    return {
        toasts: [],
        show(detail) {
            const id = Date.now();
            const toast = {
                id,
                type: detail.type || 'success',
                title: detail.title || 'Успешно',
                message: detail.message || '',
                visible: true,
                progress: 100
            };
            
            this.toasts.push(toast);
            
            // Re-init Lucide icons
            this.$nextTick(() => lucide.createIcons());
            
            // Progress bar animation
            const duration = detail.duration || 4000;
            const interval = 50;
            const step = (100 / duration) * interval;
            
            const progressInterval = setInterval(() => {
                const t = this.toasts.find(t => t.id === id);
                if (t) {
                    t.progress -= step;
                    if (t.progress <= 0) {
                        clearInterval(progressInterval);
                        this.remove(id);
                    }
                } else {
                    clearInterval(progressInterval);
                }
            }, interval);
        },
        remove(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }
    }
}
</script>
