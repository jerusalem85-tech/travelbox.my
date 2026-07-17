<div
    x-data="toastManager()"
    x-on:notify.window="add($event.detail)"
    class="fixed top-4 right-4 z-50 space-y-2 w-80"
    role="alert"
>
    <template x-for="(toast, index) in toasts" :key="index">
        <div
            x-show="toast.show"
            x-transition:enter="transform ease-out duration-300"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transform ease-in duration-200"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="flex items-start gap-3 px-4 py-3 rounded-lg shadow-lg border"
            :class="{
                'bg-green-50 border-green-200 text-green-800': toast.type === 'success',
                'bg-red-50 border-red-200 text-red-800': toast.type === 'error',
                'bg-yellow-50 border-yellow-200 text-yellow-800': toast.type === 'warning',
                'bg-blue-50 border-blue-200 text-blue-800': toast.type === 'info',
            }"
        >
            <template x-if="toast.type === 'success'">
                <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </template>
            <template x-if="toast.type === 'error'">
                <svg class="w-5 h-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </template>
            <template x-if="toast.type === 'warning'">
                <svg class="w-5 h-5 shrink-0 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
            </template>
            <template x-if="toast.type === 'info'">
                <svg class="w-5 h-5 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </template>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium" x-text="toast.title"></p>
                <p class="text-sm mt-0.5 opacity-80" x-text="toast.message" x-show="toast.message"></p>
            </div>
            <button @click="remove(index)" class="shrink-0 p-0.5 opacity-60 hover:opacity-100 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </template>
</div>

<script>
function toastManager() {
    return {
        toasts: [],
        add(detail) {
            const id = Date.now() + Math.random();
            this.toasts.push({
                id,
                type: detail.type || 'info',
                title: detail.title || '',
                message: detail.message || '',
                show: true,
            });
            setTimeout(() => {
                this.remove(this.toasts.findIndex(t => t.id === id));
            }, detail.duration || 4000);
        },
        remove(index) {
            if (index >= 0 && index < this.toasts.length) {
                this.toasts[index].show = false;
                setTimeout(() => {
                    this.toasts.splice(index, 1);
                }, 200);
            }
        },
    };
}
</script>
