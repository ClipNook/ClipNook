{{-- Theme Switcher Component --}}
<div x-data="themeSwitcher()" class="relative">
    {{-- Toggle Button --}}
    <x-button variant="icon" type="button" @click="toggle()" @keydown.enter.prevent="toggle()" @keydown.space.prevent="toggle()" x-bind:aria-label="labels.aria_label.replace(':mode', currentModeLabel)" x-bind:aria-pressed="theme !== 'system'" aria-haspopup="true" x-bind:aria-expanded="open">

        {{-- Icon --}}
        <i class="fas text-[13px]"
            x-bind:class="{
                'fa-circle-half-stroke': effective === 'system',
                'fa-sun': effective === 'light',
                'fa-moon': effective === 'dark'
            }"
            aria-hidden="true"></i>
    </x-button>

    {{-- Dropdown Menu --}}
    <div x-show="open" x-cloak @click.outside="close" @keydown.escape="close"
        x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" role="menu"
        x-ref="menu"
        class="absolute right-0 mt-2 w-40 origin-top-right rounded-lg bg-white border border-gray-200 dark:border-gray-800 shadow py-1 z-50 dark:bg-gray-900">

        {{-- Menu Items --}}
        @foreach (['system', 'light', 'dark'] as $mode)
            <button @click="setTheme('{{ $mode }}')" @keydown.enter.prevent="setTheme('{{ $mode }}')"
                @keydown.space.prevent="setTheme('{{ $mode }}')" type="button" role="menuitemradio"
                x-bind:aria-checked="theme === '{{ $mode }}'" x-ref="menuitem"
                class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium text-gray-700 hover:bg-gray-50 focus:bg-gray-50 focus:outline-none dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:bg-gray-800 transition-colors">

                <span class="flex items-center gap-2.5">
                    @if($mode === 'system')
                        <i class="fas w-3.5 text-[11px] text-gray-400 dark:text-gray-600 fa-circle-half-stroke" aria-hidden="true"></i>
                    @elseif($mode === 'light')
                        <i class="fas w-3.5 text-[11px] text-gray-400 dark:text-gray-600 fa-sun" aria-hidden="true"></i>
                    @else
                        <i class="fas w-3.5 text-[11px] text-gray-400 dark:text-gray-600 fa-moon" aria-hidden="true"></i>
                    @endif
                    <span>{{ __("theme.$mode") }}</span>
                </span>

                <i x-show="theme === '{{ $mode }}'" class="fas fa-check text-[11px]" data-accent="text"
                    aria-hidden="true"></i>
            </button>
        @endforeach
    </div>

    {{-- Screen Reader Announcements --}}
    <div class="sr-only" aria-live="polite" x-text="announcement"></div>
</div>

@once
    @push('scripts_footer')
        <script>
            (function registerThemeSwitcher(){
                const factory = () => ({
                    // State
                    open: false,
                    theme: (() => {
                        try {
                            const saved = localStorage.getItem('theme');
                            return ['dark', 'light', 'system'].includes(saved) ? saved : 'system';
                        } catch {
                            return 'system';
                        }
                    })(),
                    announcement: '',

                    // i18n labels
                    labels: @js([
                        'system' => __( 'theme.system' ),
                        'light' => __( 'theme.light' ),
                        'dark' => __( 'theme.dark' ),
                        'changed' => __( 'theme.changed' ),
                        'aria_label' => __( 'theme.aria_label' ),
                    ]),

                    // Computed: effective theme (what's actually displayed)
                    get effective() {
                        if (this.theme === 'system') {
                            return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
                        }
                        return this.theme;
                    },

                    // Computed: current mode label for display
                    get currentModeLabel() {
                        return this.labels[this.effective] || this.effective;
                    },

                    // Initialize
                    init() {
                        // Sync theme across tabs
                        window.addEventListener('storage', (e) => {
                            if (e.key === 'theme') {
                                this.theme = localStorage.getItem('theme') || 'system';
                                this.announce();
                            }
                        });

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

                        // Keyboard navigation
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
                    toggle() { this.open = !this.open; },
                    close() { this.open = false; },

                    setTheme(mode) {
                        if (typeof window.setTheme === 'function') {
                            window.setTheme(mode);
                        }
                        this.theme = mode;
                        this.announce();
                        this.close();
                    },

                    announce() {
                        const modeLabel = this.labels[this.effective] || this.effective;
                        const template = this.labels.changed || 'Theme changed: :mode';
                        this.announcement = template.replace(':mode', modeLabel);
                        setTimeout(() => this.announcement = '', 1200);
                    },

                    // Helpers
                    getMenuItems() {
                        return Array.from(this.$refs.menu?.querySelectorAll('button[role="menuitemradio"]') || []);
                    }
                });

                function register(){
                    if (window.Alpine && typeof window.Alpine.data === 'function') {
                        Alpine.data('themeSwitcher', factory);
                    } else {
                        document.addEventListener('alpine:init', () => Alpine.data('themeSwitcher', factory));
                    }
                }

                register();
            })();
        </script>
    @endpush
@endonce
