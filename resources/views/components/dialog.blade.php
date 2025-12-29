{{-- resources/views/components/dialog.blade.php --}}
<div x-data="dialog" x-show="isOpen" x-cloak @keydown.escape.window="close"
    class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 transition-opacity duration-300" x-show="isOpen"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="close"></div>

    {{-- Dialog Panel --}}
    <div class="relative w-full max-w-md" @click.stop x-show="isOpen" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">

        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl overflow-hidden">
            {{-- Dialog Header --}}
            <div class="px-6 py-4 border-b"
                x-bind:class="{
                    'bg-red-50 dark:bg-red-950 border-red-200 dark:border-red-800': type === 'danger',
                    'border-gray-200 dark:border-gray-800': type !== 'danger'
                }">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold"
                        x-bind:class="{
                            'text-red-600 dark:text-red-400': type === 'danger',
                            'text-gray-900 dark:text-white': type !== 'danger'
                        }"
                        x-text="title"></h3>

                    <button type="button" @click="close"
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        aria-label="{{ __('ui.close') }}">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Dialog Content --}}
            <div class="px-6 py-4 space-y-4">
                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="message"></p>

                <template x-if="type === 'danger' && confirmText">
                    <div class="space-y-3">
                        <div class="p-3 rounded-lg" data-accent="bgLight">
                            <p class="text-sm font-medium" data-accent="text">
                                {{ __('ui.delete_confirmation_warning') }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                {{ __('ui.type_to_confirm') }}
                                <code class="ml-1 font-mono text-red-600 dark:text-red-400" x-text="confirmText"></code>
                            </label>
                            <input type="text" x-model="userInput" @input.debounce="checkConfirmation"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500"
                                x-bind:class="userInput === confirmText ? 'border-red-500' : 'border-gray-300 dark:border-gray-700'"
                                x-bind:placeholder="confirmText" autocomplete="off">
                        </div>
                    </div>
                </template>
            </div>

            {{-- Dialog Actions --}}
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 flex items-center justify-end gap-3">
                <button type="button" @click="close"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800">
                    {{ __('ui.cancel') }}
                </button>

                <button type="button" @click="submit"
                    x-bind:disabled="type === 'danger' && confirmText && userInput !== confirmText"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    x-bind:class="{
                        'bg-red-600 hover:bg-red-700 text-white': type === 'danger',
                        'text-white': type === 'danger' || type === 'primary'
                    }"
                    x-bind:data-accent="type === 'primary' ? 'bg' : (type === 'danger' ? null : 'border')">
                    <span x-text="confirmButtonText"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@once
    @push('scripts_footer')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('dialog', () => ({
                    isOpen: false,
                    title: '',
                    message: '',
                    type: 'primary',
                    confirmButtonText: '',
                    confirmText: null,
                    userInput: '',
                    action: null,
                    method: 'POST',

                    init() {
                        // listen for global open-dialog events
                        window.addEventListener('open-dialog', (e) => {
                            // e.detail is expected to be payload
                            this.open(e.detail || {});
                        });
                    },

                    open(payload = {}) {
                        this.title = payload.title || '';
                        this.message = payload.message || '';
                        this.type = payload.type || 'primary';
                        this.confirmButtonText = payload.confirmButtonText || '{{ __('ui.confirm') }}';
                        this.confirmText = payload.confirmText || null;
                        this.userInput = '';
                        this.action = payload.action || null;
                        // allow passing an explicit action payload name (e.g. 'remove', 'restore')
                        this.actionType = payload.actionType || payload.actionType || null;
                        this.method = payload.method || 'POST';
                        this.isOpen = true;
                    },


                    close() {
                        this.isOpen = false;
                        this.title = '';
                        this.message = '';
                        this.confirmButtonText = '';
                        this.confirmText = null;
                        this.userInput = '';
                        this.action = null;
                        this.method = 'POST';
                    },

                    submit() {
                        if (!this.action) {
                            this.close();
                            return;
                        }

                        // Create a simple form and submit it to perform action (supports POST/DELETE via _method)
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = this.action;
                        form.style.display = 'none';

                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                        const tokenField = document.createElement('input');
                        tokenField.type = 'hidden';
                        tokenField.name = '_token';
                        tokenField.value = token;
                        form.appendChild(tokenField);

                        if (this.method && this.method.toUpperCase() !== 'POST') {
                            const methodField = document.createElement('input');
                            methodField.type = 'hidden';
                            methodField.name = '_method';
                            methodField.value = this.method;
                            form.appendChild(methodField);
                        }

                        // Add action parameter if provided (normalize common variants)
                        if (this.actionType) {
                            const actionValue = (function(v) {
                                if (!v) return null;
                                v = v.toString().toLowerCase();
                                if (v.includes('remove')) return 'remove';
                                if (v.includes('restore') || v.includes('enable')) return 'restore';
                                return v;
                            })(this.actionType);

                            if (actionValue) {
                                const actionField = document.createElement('input');
                                actionField.type = 'hidden';
                                actionField.name = 'action';
                                actionField.value = actionValue;
                                form.appendChild(actionField);
                            }
                        }

                        document.body.appendChild(form);
                        form.submit();
                    }
                }));
            });
        </script>
    @endpush
@endonce