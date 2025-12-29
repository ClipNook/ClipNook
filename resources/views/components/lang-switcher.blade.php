{{-- Accessible Language Switcher --}}
<div x-data="langSwitcher()" class="relative">
    {{-- Toggle Button --}}
    <button @click="toggle()" @keydown.enter.prevent="toggle()" @keydown.space.prevent="toggle()" type="button"
        :aria-label="labels.aria_label.replace(':lang', currentLabel)"
        :aria-pressed="current !== '{{ app()->getLocale() }}'" aria-haspopup="true" :aria-expanded="open"
        class="inline-flex items-center justify-center w-8 h-8 rounded text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">

        {{-- Icon --}}
        <i class="fas fa-globe text-[13px]" aria-hidden="true"></i>
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="open" x-cloak @click.outside="close" @keydown.escape="close"
        x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" role="menu"
        x-ref="menu"
        class="absolute right-0 mt-2 w-40 origin-top-right rounded-lg bg-white border border-gray-200 dark:border-gray-800 shadow py-1 z-50 dark:bg-gray-900">

        {{-- No-JS fallback --}}
        <noscript>
            @foreach (config('app.locales', []) as $key => $label)
                <a href="{{ url('/lang/' . $key) }}" role="menuitemradio"
                    aria-checked="{{ app()->getLocale() === $key ? 'true' : 'false' }}"
                    class="{{ app()->getLocale() === $key ? 'bg-gray-50 dark:bg-gray-800 ' : '' }} flex items-center justify-between px-3 py-2 text-[13px] font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800">
                    <span>{{ $label }}</span>
                    @if(app()->getLocale() === $key)
                        <i class="fas fa-check text-[11px]" data-accent="text" aria-hidden="true"></i>
                    @endif
                </a>
            @endforeach
        </noscript>

        {{-- JS-enhanced menu --}}
        <template x-for="(label, key) in locales" :key="key">
            <button @click.prevent="select(key)" @keydown.enter.prevent="select(key)"
                @keydown.space.prevent="select(key)" type="button" role="menuitemradio"
                :aria-checked="current === key" x-ref="menuitem"
                class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium text-gray-700 hover:bg-gray-50 focus:bg-gray-50 focus:outline-none dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:bg-gray-800 transition-colors">

                <span x-text="label"></span>
                <i x-show="current === key" class="fas fa-check text-[11px]" data-accent="text"
                    aria-hidden="true"></i>
            </button>
        </template>
    </div>

    {{-- Screen Reader Announcements --}}
    <div class="sr-only" aria-live="polite" x-text="announcement"></div>
</div>

@once
    @push('scripts_footer')
        <script>
            (function registerLangSwitcher() {
                const factory = () => ({
                    // State
                    open: false,
                    base: '{{ url('') }}',
                    locales: @js(config('app.locales', [])),
                    current: '{{ app()->getLocale() }}',
                    announcement: '',

                    // i18n labels
                    labels: @js([
                        'changed' => __('ui.language_changed'),
                        'aria_label' => __('ui.change_language', ['lang' => ':lang']),
                    ]),

                    // Computed: current label for display
                    get currentLabel() {
                        return this.locales[this.current] || this.current;
                    },

                    // Initialize
                    init() {
                        // Focus management when menu opens
                        this.$watch('open', (isOpen) => {
                            if (isOpen) {
                                this.$nextTick(() => {
                                    const items = this.getMenuItems();
                                    const selectedIndex = items.findIndex(
                                        el => el.getAttribute('aria-checked') === 'true'
                                    );
                                    items[selectedIndex >= 0 ? selectedIndex : 0]?.focus();
                                });
                            }
                        });

                        // Keyboard navigation inside the menu
                        this.$refs.menu?.addEventListener('keydown', (e) => {
                            const items = this.getMenuItems();
                            if (!items.length) return;

                            const currentIndex = items.indexOf(document.activeElement);
                            let nextIndex = currentIndex;

                            switch (e.key) {
                                case 'ArrowDown':
                                    e.preventDefault();
                                    nextIndex = (currentIndex + 1) % items.length;
                                    break;
                                case 'ArrowUp':
                                    e.preventDefault();
                                    nextIndex = (currentIndex - 1 + items.length) % items.length;
                                    break;
                                case 'Home':
                                    e.preventDefault();
                                    nextIndex = 0;
                                    break;
                                case 'End':
                                    e.preventDefault();
                                    nextIndex = items.length - 1;
                                    break;
                            }

                            items[nextIndex]?.focus();
                        });
                    },

                    // Actions
                    toggle() {
                        this.open = !this.open;
                    },
                    close() {
                        this.open = false;
                    },

                    async select(key) {
                        // Announce language change
                        const langLabel = this.locales[key] || key;
                        const template = this.labels.changed || 'Language changed: :lang';
                        this.announcement = template.replace(':lang', langLabel);

                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const url = (this.base || '') + '/lang';

                        try {
                            const res = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': token || ''
                                },
                                body: JSON.stringify({
                                    locale: key
                                })
                            });

                            if (res.ok) {
                                // Success - update current state and reload
                                this.current = key;
                                setTimeout(() => {
                                    window.location.reload();
                                }, 300);
                                return;
                            }

                            // Fallback to GET
                            window.location.href = (this.base || '') + '/lang/' + encodeURIComponent(key);
                        } catch (e) {
                            // Network error -> fallback to GET
                            window.location.href = (this.base || '') + '/lang/' + encodeURIComponent(key);
                        } finally {
                            setTimeout(() => this.announcement = '', 1200);
                        }
                    },

                    // Helper
                    getMenuItems() {
                        return Array.from(this.$refs.menu?.querySelectorAll('button[role="menuitemradio"]') || []);
                    }
                });

                function register() {
                    if (window.Alpine && typeof window.Alpine.data === 'function') {
                        Alpine.data('langSwitcher', factory);
                    } else {
                        document.addEventListener('alpine:init', () => Alpine.data('langSwitcher', factory));
                    }
                }

                register();
            })();
        </script>
    @endpush
@endonce