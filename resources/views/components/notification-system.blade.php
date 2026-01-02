<div
    x-data="notificationSystem()"
    class="fixed bottom-4 right-4 z-50 space-y-2"
>
    <template x-for="(notification, index) in notifications" :key="index">
        <div
            x-show="notification.show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full"
            @click="removeNotification(index)"
            :class="{
                'bg-green-500/90 border-green-400/50': notification.type === 'success',
                'bg-blue-500/90 border-blue-400/50': notification.type === 'info',
                'bg-yellow-500/90 border-yellow-400/50': notification.type === 'warning',
                'bg-red-500/90 border-red-400/50': notification.type === 'error'
            }"
            class="max-w-sm w-full bg-zinc-800 border border-zinc-700 rounded-lg shadow-lg backdrop-blur-sm cursor-pointer"
        >
            <div class="p-4">
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        <i
                            :class="{
                                'fa-solid fa-check-circle text-green-300': notification.type === 'success',
                                'fa-solid fa-info-circle text-blue-300': notification.type === 'info',
                                'fa-solid fa-exclamation-triangle text-yellow-300': notification.type === 'warning',
                                'fa-solid fa-times-circle text-red-300': notification.type === 'error'
                            }"
                            class="text-lg"
                        ></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white" x-text="notification.message"></p>
                    </div>
                    <div class="shrink-0">
                        <button
                            @click.stop="removeNotification(index)"
                            class="text-zinc-400 hover:text-white transition-colors"
                        >
                            <i class="fa-solid fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function notificationSystem() {
    return {
        notifications: [],

        init() {
            // Listen for Livewire notify events
            Livewire.on('notify', (data) => {
                this.addNotification(data.type, data.message);
            });
        },

        addNotification(type, message) {
            const notification = {
                type: type,
                message: message,
                show: true,
                id: Date.now() + Math.random()
            };

            this.notifications.push(notification);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                this.removeNotification(this.notifications.indexOf(notification));
            }, 5000);
        },

        removeNotification(index) {
            if (this.notifications[index]) {
                this.notifications[index].show = false;
                setTimeout(() => {
                    this.notifications.splice(index, 1);
                }, 300); // Wait for transition to complete
            }
        }
    }
}
</script>