{{-- Theme Switcher Component --}}
<div x-data="themeSwitcher" 
     x-init="init()"
     class="relative">
    
    {{-- Toggle Button --}}
    <button 
        @click="toggle()"
        type="button"
        :aria-label="labels.change_theme.replace(':theme', currentThemeLabel)"
        :aria-expanded="open"
        aria-haspopup="true"
        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400"
        :class="{ 'bg-gray-100 dark:bg-gray-800': open }">
        
        {{-- Dynamic Icon based on theme --}}
        <i class="fas text-[14px] transition-all duration-200"
           :class="{
               'fa-circle-half-stroke': theme === 'system',
               'fa-sun': theme === 'light',
               'fa-moon': theme === 'dark'
           }"
           aria-hidden="true"></i>
        <span class="sr-only">{{ __('theme.change_theme') }}</span>
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
                {{ __('theme.appearance') }}
            </h3>
        </div>

        {{-- Theme Options --}}
        <div class="py-1" role="menu" aria-label="{{ __('theme.theme_options') }}">
            @foreach (['system', 'light', 'dark'] as $theme)
                <button
                    @click="setTheme('{{ $theme }}')"
                    @keydown.enter.prevent="setTheme('{{ $theme }}')"
                    @keydown.space.prevent="setTheme('{{ $theme }}')"
                    type="button"
                    role="menuitemradio"
                    :aria-checked="theme === '{{ $theme }}'"
                    :tabindex="open ? '0' : '-1'"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium transition-colors hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-800"
                    :class="{
                        'bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white': theme === '{{ $theme }}',
                        'text-gray-700 dark:text-gray-300': theme !== '{{ $theme }}'
                    }">
                    
                    <div class="flex items-center gap-3">
                        @if($theme === 'system')
                            <i class="fas fa-circle-half-stroke text-gray-500 text-sm" aria-hidden="true"></i>
                        @elseif($theme === 'light')
                            <i class="fas fa-sun text-yellow-500 text-sm" aria-hidden="true"></i>
                        @else
                            <i class="fas fa-moon text-indigo-400 text-sm" aria-hidden="true"></i>
                        @endif
                        <span>{{ __("theme.{$theme}") }}</span>
                    </div>
                    
                    <i x-show="theme === '{{ $theme }}'" 
                       class="fas fa-check text-xs" 
                       data-accent="text" 
                       aria-hidden="true"></i>
                </button>
            @endforeach
        </div>

        {{-- Footer with current theme --}}
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-600 dark:text-gray-400">
                    {{ __('theme.current') }}:
                    <span class="font-semibold" x-text="currentThemeLabel"></span>
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
// Theme Switcher Alpine.js Component
document.addEventListener('alpine:init', () => {
    Alpine.data('themeSwitcher', () => ({
        // State
        open: false,
        theme: 'system', // Default
        labels: @js([
            'change_theme' => __('theme.change_theme'),
        ]),
        
        // Initialize
        init() {
            // Load saved theme preference
            this.loadTheme();
            
            // Listen for theme changes from other tabs
            window.addEventListener('storage', (event) => {
                if (event.key === 'theme') {
                    this.theme = event.newValue || 'system';
                    this.applyTheme(this.theme);
                }
            });
            
            // Listen for system preference changes
            this.detectSystemTheme();
            
            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!this.$el.contains(e.target)) {
                    this.close();
                }
            });
        },
        
        // Computed
        get currentThemeLabel() {
            const labels = {
                'system': '{{ __("theme.system") }}',
                'light': '{{ __("theme.light") }}',
                'dark': '{{ __("theme.dark") }}'
            };
            return labels[this.theme] || this.theme;
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
        
        // Set theme
        setTheme(mode) {
            if (!['system', 'light', 'dark'].includes(mode)) return;
            
            this.theme = mode;
            this.applyTheme(mode);
            this.saveTheme(mode);
            this.close();
            
            // Dispatch event for other components
            window.dispatchEvent(new CustomEvent('theme-changed', {
                detail: { theme: mode }
            }));
        },
        
        // Load saved theme
        loadTheme() {
            try {
                const saved = localStorage.getItem('theme');
                if (saved && ['system', 'light', 'dark'].includes(saved)) {
                    this.theme = saved;
                } else {
                    // Default to system theme
                    this.theme = 'system';
                }
                
                this.applyTheme(this.theme);
            } catch (error) {
                console.debug('Could not load theme preference:', error);
                this.theme = 'system';
                this.applyTheme('system');
            }
        },
        
        // Apply theme to document
        applyTheme(mode) {
            const html = document.documentElement;
            
            if (mode === 'dark') {
                html.classList.add('dark');
                html.setAttribute('data-theme', 'dark');
            } else if (mode === 'light') {
                html.classList.remove('dark');
                html.setAttribute('data-theme', 'light');
            } else {
                // System theme
                html.removeAttribute('data-theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (prefersDark) {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }
            }
        },
        
        // Save theme preference
        saveTheme(mode) {
            try {
                localStorage.setItem('theme', mode);
                
                // Trigger storage event for other tabs
                window.dispatchEvent(new StorageEvent('storage', {
                    key: 'theme',
                    newValue: mode,
                    storageArea: localStorage
                }));
                
                // Save to server if authenticated
                this.saveToServer(mode);
                
            } catch (error) {
                console.debug('Could not save theme preference:', error);
            }
        },
        
        // Detect system theme changes
        detectSystemTheme() {
            const darkModeMedia = window.matchMedia('(prefers-color-scheme: dark)');
            
            darkModeMedia.addEventListener('change', (e) => {
                if (this.theme === 'system') {
                    this.applyTheme('system');
                }
            });
        },
        
        // Save theme to server via AJAX
        async saveToServer(theme) {
            // Check if user is authenticated via meta tag
            const userIdMeta = document.querySelector('meta[name="user-id"]');
            if (!userIdMeta) return;
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) return;
                
                const response = await fetch('/settings/theme', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ theme: theme })
                });
                
                if (!response.ok) {
                    throw new Error(`Server responded with ${response.status}`);
                }
                
            } catch (error) {
                // Silent fail - client-side storage is sufficient
                console.debug('Failed to save theme to server:', error.message);
            }
        }
    }));
});
</script>
@endpush
@endonce