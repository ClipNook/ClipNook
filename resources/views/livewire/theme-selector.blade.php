<div x-data="themeSelector()" x-init="init()" x-cloak class="relative">
    <!-- Theme Selector Button -->
    <button
        @click="toggleSelector()"
        class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-zinc-300 hover:text-white transition-all duration-200 rounded-lg hover:bg-zinc-800/80 backdrop-blur-sm border border-zinc-700/50 hover:border-zinc-600 focus:outline-none focus:ring-2 focus:ring-[var(--color-accent-500)] focus:ring-offset-2 focus:ring-offset-zinc-900"
        :class="{ 'bg-zinc-800/90 border-zinc-600': isOpen }"
        aria-expanded="isOpen"
        aria-haspopup="true"
        :aria-label="'Current theme: ' + currentThemeName + '. Open theme selector'"
    >
        <i class="fas fa-palette text-base" :class="'text-' + currentThemeData.primary"></i>
        <span class="hidden sm:inline" x-text="currentThemeName"></span>
        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': isOpen }"></i>
    </button>

    <!-- Theme Selector Dropdown -->
    <div
        x-show="isOpen"
        @click.away="closeSelector()"
        @keydown.escape.window="closeSelector()"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50"
        :class="dropdownPosition"
        role="dialog"
        aria-modal="true"
        aria-label="Theme selection"
        x-ref="dropdown"
        x-trap.noscroll.inert="isOpen"
    >
        <div class="w-64 max-w-[calc(100vw-2rem)] bg-zinc-900/95 backdrop-blur-xl border border-zinc-700/80 rounded-xl shadow-2xl ring-1 ring-zinc-600/20 overflow-hidden">
            <!-- Header -->
            <div class="p-4 border-b border-zinc-700/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded bg-[var(--color-accent-500)] flex items-center justify-center">
                            <i class="fas fa-palette text-xs text-white"></i>
                        </div>
                        <span class="text-sm font-semibold text-zinc-200">Theme</span>
                    </div>
                    <button 
                        @click="closeSelector()"
                        class="text-zinc-400 hover:text-zinc-200 transition-colors p-1 rounded-lg hover:bg-zinc-800 focus:outline-none focus:ring-1 focus:ring-zinc-600"
                        aria-label="Close theme selector"
                    >
                        <i class="fas fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Themes Grid -->
            <div class="p-4">
                <div class="grid grid-cols-2 gap-2" role="radiogroup" :aria-label="'Available themes. Current theme: ' + currentThemeName">
                    <template x-for="(theme, key) in availableThemes" :key="key">
                        <button
                            @click="setTheme(key)"
                            class="group relative p-3 rounded-lg border-2 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-current focus:ring-offset-2 focus:ring-offset-zinc-900"
                            :class="{
                                'border-zinc-700 bg-zinc-800/50 hover:bg-zinc-700/50': key !== currentTheme,
                                'border-[var(--color-accent-500)] bg-zinc-800/80 shadow-lg shadow-[var(--color-accent-500)]/10': key === currentTheme
                            }"
                            :style="key === currentTheme ? '--color-accent-500: var(--color-accent-500)' : ''"
                            role="radio"
                            :aria-checked="key === currentTheme"
                            :aria-label="theme.name + ' theme'"
                        >
                            <!-- Selected Indicator -->
                            <div x-show="key === currentTheme" class="absolute -top-2 -right-2 z-10">
                                <div class="w-6 h-6 rounded-full bg-[var(--color-accent-500)] flex items-center justify-center shadow-lg border border-zinc-800">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                            </div>

                            <!-- Color Preview -->
                            <div class="relative mb-2">
                                <div class="flex items-center justify-center gap-1.5">
                                    <div class="w-3 h-3 rounded-full border border-zinc-600" :class="theme.bg"></div>
                                    <div class="w-3 h-3 rounded-full border border-zinc-600" :class="theme.bgSecondary"></div>
                                </div>
                            </div>

                            <!-- Theme Name -->
                            <div class="text-center">
                                <span class="text-xs font-medium text-zinc-200 group-hover:text-white transition-colors" x-text="theme.name"></span>
                            </div>

                            <!-- Hover Effect -->
                            <div class="absolute inset-0 rounded-lg bg-gradient-to-br from-transparent via-transparent to-zinc-900/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Current Theme Indicator -->
            <div x-show="currentThemeName" class="px-4 py-3 border-t border-zinc-700/50 bg-zinc-900/50">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-zinc-400">Selected:</span>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1.5">
                            <div class="w-2.5 h-2.5 rounded-full" :class="currentThemeData.bg"></div>
                            <div class="w-2.5 h-2.5 rounded-full" :class="currentThemeData.bgSecondary"></div>
                        </div>
                        <span class="text-sm font-medium text-zinc-200" x-text="currentThemeName"></span>
                    </div>
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
        dropdownPosition: 'bottom-full mb-2 right-0',
        
        init() {
            // Check if dropdown would go off-screen and adjust position
            this.$watch('isOpen', (value) => {
                if (value) {
                    this.adjustDropdownPosition();
                    // Prevent body scroll when dropdown is open
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });

            // Load saved theme from localStorage
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
            this.closeSelector();
        },

        adjustDropdownPosition() {
            this.$nextTick(() => {
                const button = this.$el.querySelector('button[aria-expanded]');
                const dropdown = this.$refs.dropdown;
                
                if (!button || !dropdown) return;

                const buttonRect = button.getBoundingClientRect();
                const dropdownRect = dropdown.getBoundingClientRect();
                const viewportHeight = window.innerHeight;
                const viewportWidth = window.innerWidth;
                
                // Check vertical position
                if (buttonRect.top < dropdownRect.height) {
                    // Not enough space above, position below
                    this.dropdownPosition = 'top-full mt-2 right-0';
                } else {
                    // Enough space, position above (default)
                    this.dropdownPosition = 'bottom-full mb-2 right-0';
                }
                
                // Check horizontal position
                this.$nextTick(() => {
                    const updatedDropdownRect = dropdown.getBoundingClientRect();
                    
                    if (updatedDropdownRect.right > viewportWidth) {
                        dropdown.style.right = '0';
                        dropdown.style.left = 'auto';
                    }
                    
                    if (updatedDropdownRect.left < 0) {
                        dropdown.style.left = '0';
                        dropdown.style.right = 'auto';
                    }
                });
            });
        },

        saveTheme(key) {
            try {
                localStorage.setItem('clipnook-theme', key);
            } catch (e) {
                console.warn('Could not save theme to localStorage:', e);
            }
        },

        loadSavedTheme() {
            try {
                const savedTheme = localStorage.getItem('clipnook-theme');
                const themeKeys = Object.keys(this.availableThemes);
                
                if (savedTheme && themeKeys.includes(savedTheme) && savedTheme !== this.currentTheme) {
                    // Don't auto-set, just show in UI if different
                    // User can click to apply
                }
            } catch (e) {
                console.warn('Could not load theme from localStorage:', e);
            }
        }
    }
}
</script>