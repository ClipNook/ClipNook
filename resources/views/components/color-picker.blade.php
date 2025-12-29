{{-- Color Picker Component --}}
<div x-data="colorPicker()" 
     x-init="init()"
     class="relative inline-block"
     @keydown.escape.window="open = false">
    
    {{-- Trigger Button --}}
    <button 
        @click="toggle()" 
        type="button"
        :aria-label="labels.change_color.replace(':color', currentColor.name)"
        :aria-expanded="open"
        aria-haspopup="listbox"
        class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-gray-400 dark:hover:border-gray-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        :class="{ 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20': open }">
        
        {{-- Color Preview --}}
        <span class="w-4 h-4 rounded-full border border-gray-300 dark:border-gray-600 shadow-sm" 
              :style="`background-color: ${currentColor.value}`"></span>
        
        {{-- Label --}}
        <span class="hidden sm:inline">{{ __('ui.color') }}</span>
        
        {{-- Chevron Icon --}}
        <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform duration-200" 
           :class="{ 'rotate-180': open }"></i>
    </button>

    {{-- Dropdown Menu --}}
    <div
        x-show="open"
        x-cloak
        @click.outside="close()"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        :class="dropdownPosition"
        class="absolute bottom-full mb-2 w-64 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-xl z-50 overflow-hidden"
        style="display: none;">
        
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                    {{ __('ui.accent_color') }}
                </h3>
                <button 
                    type="button"
                    @click="resetColor()"
                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                    aria-label="{{ __('ui.reset_color') }}">
                    {{ __('ui.reset') }}
                </button>
            </div>
        </div>

        {{-- Color Grid --}}
        <div class="p-4">
            <div role="listbox" 
                 :aria-label="labels.color_options"
                 class="grid grid-cols-5 gap-2">
                
                <template x-for="(color, index) in colors" :key="color.key">
                    <button
                        @click="setColor(color.key)"
                        @keydown.enter.prevent="setColor(color.key)"
                        @keydown.space.prevent="setColor(color.key)"
                        type="button"
                        role="option"
                        :aria-selected="currentKey === color.key"
                        :aria-label="color.name"
                        :tabindex="open ? '0' : '-1'"
                        class="group relative w-10 h-10 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400"
                        :style="`background-color: ${color.value}`"
                        :class="{
                            'ring-2 ring-offset-2 ring-gray-400 scale-110': currentKey === color.key,
                            'hover:scale-110 hover:shadow-lg': currentKey !== color.key,
                            'border-2 border-white dark:border-gray-800': currentKey === color.key
                        }">
                        
                        {{-- Checkmark for selected color --}}
                        <div class="absolute inset-0 flex items-center justify-center"
                             :class="currentKey === color.key ? 'opacity-100' : 'opacity-0 group-hover:opacity-50'">
                            <i class="fas fa-check text-sm"
                               :class="color.dark ? 'text-gray-900' : 'text-white'"></i>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        {{-- Current Color Info --}}
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300" 
                          x-text="currentColor.name"></span>
                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400" 
                          x-text="currentKey"></span>
                </div>
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
// Color Picker Alpine.js Component
document.addEventListener('alpine:init', () => {
    Alpine.data('colorPicker', () => ({
        // State
        open: false,
        currentKey: 'purple', // Default
        dropdownPosition: 'right-0', // Default position
        
        // i18n labels
        labels: @js([
            'change_color' => __('ui.change_color'),
            'color_options' => __('ui.color_options'),
            'accent_color' => __('ui.accent_color'),
            'reset' => __('ui.reset'),
        ]),
        
        // Color definitions
        colors: [
            { key: 'purple', name: 'Purple', value: 'rgb(147, 51, 234)', dark: false },
            { key: 'blue', name: 'Blue', value: 'rgb(59, 130, 246)', dark: false },
            { key: 'green', name: 'Green', value: 'rgb(34, 197, 94)', dark: false },
            { key: 'red', name: 'Red', value: 'rgb(239, 68, 68)', dark: false },
            { key: 'orange', name: 'Orange', value: 'rgb(249, 115, 22)', dark: false },
            { key: 'pink', name: 'Pink', value: 'rgb(236, 72, 153)', dark: false },
            { key: 'indigo', name: 'Indigo', value: 'rgb(99, 102, 241)', dark: false },
            { key: 'teal', name: 'Teal', value: 'rgb(20, 184, 166)', dark: false },
            { key: 'amber', name: 'Amber', value: 'rgb(245, 158, 11)', dark: false },
            { key: 'slate', name: 'Slate', value: 'rgb(100, 116, 139)', dark: true }
        ],
        
        // Color mapping for CSS variables (HSL format)
        colorMap: {
            'purple': { h: 252, s: 83, l: 65 },
            'blue':   { h: 221, s: 83, l: 65 },
            'green':  { h: 142, s: 76, l: 45 },
            'red':    { h: 0,   s: 84, l: 53 },
            'orange': { h: 25,  s: 95, l: 47 },
            'pink':   { h: 330, s: 81, l: 60 },
            'indigo': { h: 238, s: 75, l: 59 },
            'teal':   { h: 173, s: 80, l: 36 },
            'amber':  { h: 38,  s: 92, l: 45 },
            'slate':  { h: 215, s: 13, l: 55 }
        },

        // Computed properties
        get currentColor() {
            return this.colors.find(c => c.key === this.currentKey) || this.colors[0];
        },

        // Methods
        calculateDropdownPosition() {
            const button = this.$el.querySelector('button');
            if (!button) {
                this.dropdownPosition = 'right-0';
                return;
            }

            const rect = button.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            const dropdownWidth = 256; // w-64 = 16rem = 256px

            // Check if there's enough space on the right
            const spaceOnRight = viewportWidth - rect.right;
            // Check if there's enough space on the left
            const spaceOnLeft = rect.left;

            // If not enough space on right but enough on left, position left
            if (spaceOnRight < dropdownWidth && spaceOnLeft >= dropdownWidth) {
                this.dropdownPosition = 'left-0';
            } else {
                this.dropdownPosition = 'right-0';
            }
        },

        // Initialize component
        init() {
            this.loadSavedColor();

            // Listen for color changes from other tabs
            window.addEventListener('storage', (event) => {
                if (event.key === 'accentColor' && event.newValue !== this.currentKey) {
                    this.currentKey = event.newValue || 'purple';
                    this.applyColor(this.currentKey);
                }
            });

            // Listen for window resize to recalculate position
            window.addEventListener('resize', () => {
                if (this.open) {
                    this.calculateDropdownPosition();
                }
            });
        },

        // Load saved color from localStorage or cookie
        loadSavedColor() {
            try {
                // Try localStorage first
                const saved = localStorage.getItem('accentColor');
                if (saved && this.colors.some(c => c.key === saved)) {
                    this.currentKey = saved;
                }
                
                // Fallback to cookie
                if (!saved) {
                    const cookieMatch = document.cookie.match(/(?:^|;\s*)accentColor=([^;]*)/);
                    if (cookieMatch && this.colors.some(c => c.key === cookieMatch[1])) {
                        this.currentKey = decodeURIComponent(cookieMatch[1]);
                    }
                }
                
                // Apply the loaded color
                this.applyColor(this.currentKey);
                
            } catch (error) {
                console.debug('Could not load color preference:', error);
            }
        },

        // Toggle dropdown
        toggle() {
            this.open = !this.open;
            if (this.open) {
                // Calculate optimal position when opening
                this.calculateDropdownPosition();

                // Focus first color button when opening
                this.$nextTick(() => {
                    const firstButton = this.$el.querySelector('[role="option"]');
                    if (firstButton) firstButton.focus();
                });
            }
        },

        // Close dropdown
        close() {
            this.open = false;
        },

        // Set new color
        setColor(key) {
            if (!this.colors.some(c => c.key === key)) return;
            
            this.currentKey = key;
            this.applyColor(key);
            this.saveColor(key);
            this.close();
            
            // Dispatch custom event for other components
            window.dispatchEvent(new CustomEvent('accent-color-changed', {
                detail: { color: key }
            }));
        },

        // Reset to default color
        resetColor() {
            this.setColor('purple');
        },

        // Apply color to CSS variables
        applyColor(key) {
            const colorConfig = this.colorMap[key];
            if (!colorConfig) return;
            
            const { h, s, l } = colorConfig;
            const root = document.documentElement;
            
            // Calculate dark mode variant (slightly lighter)
            const darkL = Math.min(l + 10, 85);
            
            // Set HSL variables
            root.style.setProperty('--accent-hue', h.toString());
            root.style.setProperty('--accent-saturation', `${s}%`);
            root.style.setProperty('--accent-lightness', `${l}%`);
            
            // Set color values
            root.style.setProperty('--accent-bg', `hsl(${h}, ${s}%, ${l}%)`);
            root.style.setProperty('--accent-border', `hsl(${h}, ${s}%, ${l}%)`);
            root.style.setProperty('--accent-bgLight', `hsl(${h}, 83%, 96%)`);
            
            // Dark mode variants
            root.style.setProperty('--accent-bg-dark', `hsl(${h}, ${s}%, ${darkL}%)`);
            root.style.setProperty('--accent-border-dark', `hsl(${h}, ${s}%, ${darkL}%)`);
            root.style.setProperty('--accent-bgLight-dark', `hsl(${h}, 83%, 18%)`);
        },

        // Save color preference
        saveColor(key) {
            try {
                // Save to localStorage
                localStorage.setItem('accentColor', key);
                
                // Save to cookie (for server-side rendering)
                const expires = new Date();
                expires.setFullYear(expires.getFullYear() + 1);
                
                document.cookie = `accentColor=${encodeURIComponent(key)}; expires=${expires.toUTCString()}; path=/; SameSite=Lax${window.location.protocol === 'https:' ? '; Secure' : ''}`;
                
                // Trigger storage event for other tabs
                window.dispatchEvent(new StorageEvent('storage', {
                    key: 'accentColor',
                    newValue: key,
                    storageArea: localStorage
                }));
                
                // Send to server if user is authenticated
                this.saveToServer(key);
                
            } catch (error) {
                console.debug('Could not save color preference:', error);
            }
        },

        // Save color to server via AJAX
        async saveToServer(color) {
            // Check if user is authenticated via meta tag
            const userIdMeta = document.querySelector('meta[name="user-id"]');
            if (!userIdMeta) return;
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) return;
                
                const response = await fetch('/settings/accent-color', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ accent_color: color })
                });
                
                if (!response.ok) {
                    throw new Error(`Server responded with ${response.status}`);
                }
                
            } catch (error) {
                // Silent fail - client-side storage is sufficient
                console.debug('Failed to save color to server:', error.message);
            }
        }
    }));
});

// Global function to set initial accent color (for head script)
function setInitialAccentFromStorage() {
    try {
        let key = localStorage.getItem('accentColor');
        if (!key) {
            // Check cookie as fallback
            const match = document.cookie.match(/(?:^|;\s*)accentColor=([^;]*)/);
            if (match) key = decodeURIComponent(match[1]);
        }
        
        if (!key) key = 'purple';
        
        // Color configuration (same as in Alpine component)
        const colorMap = {
            'purple': { h: 252, s: 83, l: 65 },
            'blue':   { h: 221, s: 83, l: 65 },
            'green':  { h: 142, s: 76, l: 45 },
            'red':    { h: 0,   s: 84, l: 53 },
            'orange': { h: 25,  s: 95, l: 47 },
            'pink':   { h: 330, s: 81, l: 60 },
            'indigo': { h: 238, s: 75, l: 59 },
            'teal':   { h: 173, s: 80, l: 36 },
            'amber':  { h: 38,  s: 92, l: 45 },
            'slate':  { h: 215, s: 13, l: 55 }
        };
        
        const color = colorMap[key] || colorMap.purple;
        const root = document.documentElement;
        
        root.style.setProperty('--accent-hue', color.h);
        root.style.setProperty('--accent-saturation', `${color.s}%`);
        root.style.setProperty('--accent-lightness', `${color.l}%`);
        
        const darkL = Math.min(color.l + 10, 85);
        
        root.style.setProperty('--accent-bg', `hsl(${color.h}, ${color.s}%, ${color.l}%)`);
        root.style.setProperty('--accent-border', `hsl(${color.h}, ${color.s}%, ${color.l}%)`);
        root.style.setProperty('--accent-bgLight', `hsl(${color.h}, 83%, 96%)`);
        root.style.setProperty('--accent-bg-dark', `hsl(${color.h}, ${color.s}%, ${darkL}%)`);
        root.style.setProperty('--accent-border-dark', `hsl(${color.h}, ${color.s}%, ${darkL}%)`);
        root.style.setProperty('--accent-bgLight-dark', `hsl(${color.h}, 83%, 18%)`);
        
    } catch (error) {
        console.debug('Accent loader failed:', error);
    }
}

// Call the function when DOM is loaded
document.addEventListener('DOMContentLoaded', setInitialAccentFromStorage);
</script>
@endpush
@endonce