<div x-data="themeSelector()" x-init="init()" x-cloak>
    <!-- Theme Selector Button -->
    <button
        @click="toggleSelector()"
        class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-zinc-400 hover:text-zinc-200 transition-colors duration-150 rounded-md hover:bg-zinc-800 border border-zinc-700 hover:border-zinc-600 focus:outline-none focus:ring-1 focus:ring-zinc-600"
        :class="{ 'bg-zinc-800 border-zinc-600 text-zinc-200': isOpen }"
        aria-expanded="isOpen"
        aria-haspopup="true"
        :aria-label="'Current theme: ' + currentThemeName + '. Open theme selector'"
    >
        <i class="fas fa-swatchbook text-base" :class="isOpen ? 'text-(--color-accent-500)' : ''"></i>
        <span class="hidden sm:inline" x-text="currentThemeName"></span>
        <i class="fas fa-chevron-down text-xs transition-transform duration-150" :class="{ 'rotate-180': isOpen }"></i>
    </button>

    <!-- Modal Overlay -->
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-100 bg-black/60 backdrop-blur-sm"
        @click="closeSelector()"
        @keydown.escape.window="closeSelector()"
    ></div>

    <!-- Modal Content - Bottom Sheet on Mobile, Centered on Desktop -->
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed inset-x-0 bottom-0 z-101 sm:inset-0 sm:flex sm:items-center sm:justify-center"
        role="dialog"
        aria-modal="true"
        aria-label="Theme selection"
    >
        <div class="w-full sm:max-w-sm md:max-w-md lg:max-w-lg bg-zinc-900 border-t sm:border border-zinc-700 rounded-t-xl sm:rounded-xl overflow-hidden sm:mx-4">
            <!-- Mobile Handle -->
            <div class="flex justify-center py-3 sm:hidden">
                <div class="w-12 h-1.5 bg-zinc-600 rounded-full"></div>
            </div>

            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-700 bg-zinc-900/95 backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-zinc-800 rounded-lg">
                            <i class="fas fa-palette text-zinc-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-100">{{ __('theme.choose_theme') }}</h3>
                            <p class="text-sm text-zinc-500">{{ __('theme.pick_favorite') }}</p>
                        </div>
                    </div>
                    <button
                        @click="closeSelector()"
                        class="p-2 text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-zinc-600"
                        aria-label="{{ __('theme.close_selector') }}"
                    >
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6 max-h-[60vh] sm:max-h-[70vh] overflow-y-auto">
                <!-- Current Theme Highlight -->
                <div class="mb-6 p-4 bg-zinc-800/50 rounded-lg border border-zinc-700">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-(--color-accent-500)/10 rounded-lg">
                            <i class="fas fa-check-circle text-(--color-accent-500) text-lg"></i>
                        </div>
                        <div>
                            <div class="text-sm text-zinc-500">{{ __('theme.current_theme') }}</div>
                            <div class="text-base font-semibold text-zinc-100" x-text="currentThemeName"></div>
                        </div>
                    </div>
                </div>

                <!-- Theme Options - Single Column on Mobile, Grid on Larger Screens -->
                <div class="space-y-3 sm:grid sm:grid-cols-2 sm:gap-4 sm:space-y-0" role="radiogroup" :aria-label="'Available themes. Current theme: ' + currentThemeName">
                    <template x-for="(theme, key) in availableThemes" :key="key">
                        <button
                            @click="setTheme(key)"
                            class="w-full p-4 rounded-lg border-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 focus:ring-offset-zinc-900 text-left group"
                            :class="{
                                'border-zinc-700 bg-zinc-800/30 hover:bg-zinc-800/60 hover:border-zinc-600': key !== currentTheme,
                                'border-(--color-accent-500) bg-zinc-800/80 ring-1 ring-(--color-accent-500)/30': key === currentTheme
                            }"
                            role="radio"
                            :aria-checked="key === currentTheme"
                            :aria-label="theme.name + ' theme'"
                        >
                            <div class="flex items-center gap-4">
                                <!-- Theme Icon -->
                                <div class="shrink-0 p-3 rounded-lg bg-zinc-700/50 group-hover:bg-zinc-700/70 transition-colors" :class="key === currentTheme ? 'bg-(--color-accent-500)/10' : ''">
                                    <i class="text-2xl" :class="[getThemeIcon(key), key === currentTheme ? 'text-(--color-accent-500)' : 'text-zinc-400']"></i>
                                </div>

                                <!-- Theme Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="text-base font-semibold" :class="key === currentTheme ? 'text-zinc-100' : 'text-zinc-300'" x-text="theme.name"></div>
                                    <div x-show="key === currentTheme" class="text-sm text-(--color-accent-500) font-medium mt-0.5">{{ __('theme.active') }}</div>
                                </div>

                                <!-- Selection Indicator -->
                                <div x-show="key === currentTheme" class="shrink-0">
                                    <div class="w-6 h-6 bg-(--color-accent-500) rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Footer - Hidden on Mobile -->
            <div class="hidden sm:block px-6 py-4 border-t border-zinc-700 bg-zinc-900/50">
                <div class="flex items-center justify-center gap-2">
                    <i class="fas fa-circle text-sm text-(--color-accent-500)"></i>
                    <span class="text-sm text-zinc-400">{{ __('theme.changes_apply_instantly') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function themeSelector() {
    return {
        isOpen: @entangle('isOpen').live,
        currentTheme: @entangle('currentTheme').live,
        availableThemes: @json($availableThemes),
        
        init() {
            this.loadSavedTheme();
        },

        get currentThemeData() {
            return this.availableThemes[this.currentTheme] || this.availableThemes.violet;
        },

        get currentThemeName() {
            return this.currentThemeData?.name || 'Violet';
        },

        toggleSelector() {
            this.$wire.toggleSelector();
        },

        closeSelector() {
            if (this.isOpen) {
                this.$wire.toggleSelector();
            }
        },

        setTheme(key) {
            if (this.currentTheme !== key) {
                this.$wire.setTheme(key);
                this.saveTheme(key);
            }
            setTimeout(() => this.closeSelector(), 150);
        },

        getThemeIcon(key) {
            const icons = {
                violet: 'fas fa-gem',
                blue: 'fas fa-droplet',
                green: 'fas fa-leaf',
                red: 'fas fa-fire',
                orange: 'fas fa-sun',
                pink: 'fas fa-heart',
                cyan: 'fas fa-water',
                amber: 'fas fa-bolt'
            };
            return icons[key] || 'fas fa-circle';
        },

        saveTheme(key) {
            try {
                localStorage.setItem('clipnook-theme', key);
            } catch (e) {
                console.warn('Could not save theme:', e);
            }
        },

        loadSavedTheme() {
            try {
                const savedTheme = localStorage.getItem('clipnook-theme');
                const themeKeys = Object.keys(this.availableThemes);
                
                if (savedTheme && themeKeys.includes(savedTheme) && savedTheme !== this.currentTheme) {
                    this.$wire.setTheme(savedTheme);
                }
            } catch (e) {
                console.warn('Could not load theme:', e);
            }
        }
    }
}

document.addEventListener('livewire:init', () => {
    Livewire.on('theme-changed', (event) => {
        const theme = event.theme;
        document.body.className = document.body.className.replace(/theme-\w+/g, '' );
        document.body.classList.add(`theme-${theme}`);
        
        // Optional: Save to localStorage for persistence across page reloads
        localStorage.setItem('theme', theme);
    });
    
    // Load theme from localStorage on page load
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme && ['violet', 'blue', 'green', 'red'].includes(savedTheme)) {
        document.body.classList.add(`theme-${savedTheme}`);
    }
});
</script>