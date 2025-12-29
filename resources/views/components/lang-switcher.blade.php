{{-- Language Switcher Component --}}
<div x-data="langSwitcher" 
     x-init="init()"
     class="relative">
    
    {{-- Toggle Button --}}
    <button 
        @click="toggle()"
        type="button"
        :aria-label="labels.change_language.replace(':lang', currentLabel)"
        :aria-expanded="open"
        aria-haspopup="true"
        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400"
        :class="{ 'bg-gray-100 dark:bg-gray-800': open }">
        
        <i class="fas fa-globe text-[14px]" aria-hidden="true"></i>
        <span class="sr-only">{{ __('ui.change_language') }}</span>
    </button>

    {{-- Dropdown Menu --}}
    <div 
        x-show="open"
        x-cloak
        @click.outside="close()"
        @keydown.escape.window="close()"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-xl z-50 overflow-hidden"
        style="display: none;">
        
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                {{ __('ui.language') }}
            </h3>
        </div>

        {{-- Language List --}}
        <div class="py-1" role="menu" aria-label="{{ __('ui.language_options') }}">
            <template x-for="(label, key) in locales" :key="key">
                <button
                    @click="select(key)"
                    @keydown.enter.prevent="select(key)"
                    @keydown.space.prevent="select(key)"
                    type="button"
                    role="menuitemradio"
                    :aria-checked="current === key"
                    :tabindex="open ? '0' : '-1'"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium transition-colors hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-800"
                    :class="{
                        'bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white': current === key,
                        'text-gray-700 dark:text-gray-300': current !== key
                    }">
                    
                    <div class="flex items-center gap-3">
                        <span :class="'fi fi-' + getCountryCode(key)"></span>
                        <span x-text="label"></span>
                    </div>
                    
                    <i x-show="current === key" 
                       class="fas fa-check text-xs" 
                       data-accent="text" 
                       aria-hidden="true"></i>
                </button>
            </template>
        </div>

        {{-- Footer with current language --}}
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-600 dark:text-gray-400">
                    {{ __('ui.current_language') }}:
                    <span class="font-semibold" x-text="currentLabel"></span>
                </span>
                <button 
                    type="button"
                    @click="close()"
                    class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                    aria-label="{{ __('ui.close') }}">
                    {{ __('ui.close') }}
                </button>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts_footer')
<script>
// Language Switcher Alpine.js Component
document.addEventListener('alpine:init', () => {
    Alpine.data('langSwitcher', () => ({
        // State
        open: false,
        current: @json(app()->getLocale()),
        locales: @json(config('app.locales', [])),
        isSubmitting: false,
        labels: @js([
            'change_language' => __('ui.change_language'),
        ]),
        
        // Computed
        get currentLabel() {
            return this.locales[this.current] || this.current;
        },

        // Initialize
        init() {
            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!this.$el.contains(e.target)) {
                    this.close();
                }
            });
        },

        // Toggle dropdown
        toggle() {
            this.open = !this.open;
            if (this.open) {
                // Focus first item when opening
                this.$nextTick(() => {
                    const firstButton = this.$el.querySelector('[role="menuitemradio"]');
                    if (firstButton) firstButton.focus();
                });
            }
        },

        // Close dropdown
        close() {
            this.open = false;
        },

        // Select language
        async select(locale) {
            if (this.isSubmitting || this.current === locale) {
                this.close();
                return;
            }

            this.isSubmitting = true;
            
            try {
                // Get CSRF token
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                
                // Send request to change language
                const response = await fetch('/lang', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ locale: locale })
                });
                
                if (response.ok) {
                    // Update current language
                    this.current = locale;
                    
                    // Reload page to apply new language
                    setTimeout(() => {
                        window.location.reload();
                    }, 100);
                } else {
                    // Fallback to GET method
                    window.location.href = '/lang/' + encodeURIComponent(locale);
                }
            } catch (error) {
                // Fallback to GET method on network error
                window.location.href = '/lang/' + encodeURIComponent(locale);
            } finally {
                this.isSubmitting = false;
                this.close();
            }
        },

        // Get country code for flag emoji (simplified mapping)
        getCountryCode(locale) {
            const map = {
                'en': 'us',
                'de': 'de',
                'fr': 'fr',
                'es': 'es',
                'it': 'it',
                'nl': 'nl',
                'pl': 'pl',
                'ru': 'ru',
                'zh': 'cn',
                'ja': 'jp',
                'ko': 'kr',
                'pt': 'pt',
                'ar': 'sa',
                'tr': 'tr',
                'vi': 'vn',
                'th': 'th',
                'id': 'id',
                'ms': 'my',
            };
            return map[locale] || locale;
        }
    }));
});
</script>
@endpush
@endonce