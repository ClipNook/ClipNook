{{-- Color Picker Component --}}
<div x-data="colorPicker()" class="relative">
    <button 
        @click="toggle()" 
        type="button"
        :aria-label="'{{ __('ui.color') }}: ' + (colors[currentColor] ? colors[currentColor].name : '')"
        :aria-expanded="open"
        aria-haspopup="true"
        class="inline-flex items-center gap-1.5 text-[11px] font-medium text-gray-600 dark:text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
        <i class="fas fa-palette text-[13px]"></i>
        <span class="hidden sm:inline">{{ __('ui.color') }}</span>
    </button>

    <div 
        x-show="open"
        x-cloak
        @click.outside="close"
        @keydown.escape="close"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="absolute bottom-full left-0 mb-2 w-60 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow overflow-hidden z-50"
        style="display:none;">
        
        <div class="p-2.5 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-500">{{ __('ui.color_choose') }}</h3>
        </div>

        <div class="p-2.5 grid grid-cols-5 gap-1.5">
            <template x-for="(color, key) in colors" :key="key">
                <button
                    @click="setColor(key)"
                    type="button"
                    :aria-label="color.name"
                    :aria-pressed="currentColor === key"
                    class="group relative w-9 h-9 rounded border-2 hover:opacity-80 focus:outline-none focus-visible:border focus-visible:border-indigo-500 transition-opacity"
                    :class="currentColor === key ? color.border : 'border-transparent'"
                    :style="currentColor === key ? 'border-color: currentColor' : ''"
                    :data-accent="currentColor === key ? 'border' : ''">
                    <div 
                        class="w-full h-full rounded"
                        :class="color.bg"></div>
                    <i x-show="currentColor === key" 
                       class="fas fa-check absolute inset-0 m-auto text-white text-xs"
                       :class="key === 'slate' ? 'text-gray-900' : 'text-white'"></i>
                </button>
            </template>
        </div>
    </div>
</div>

@once
@push('scripts_footer')
<script>
(function registerColorPicker(){
    const factory = () => ({
        open: false,
        currentColor: (() => {
            try {
                return localStorage.getItem('accentColor') || 'purple';
            } catch {
                return 'purple';
            }
        })(),
        colors: {
            purple: { name: 'Lila', bg: 'bg-purple-600', border: 'border-purple-600', classes: { bg: 'bg-purple-600 dark:bg-purple-500 hover:bg-purple-700 dark:hover:bg-purple-600 focus:bg-purple-700 dark:focus:bg-purple-600', text: 'text-purple-600 dark:text-purple-400', border: 'border-purple-200 dark:border-purple-800', bgLight: 'bg-purple-50 dark:bg-purple-950' } },
            blue: { name: 'Blau', bg: 'bg-blue-600', border: 'border-blue-600', classes: { bg: 'bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600', text: 'text-blue-600 dark:text-blue-400', border: 'border-blue-200 dark:border-blue-800', bgLight: 'bg-blue-50 dark:bg-blue-950' } },
            green: { name: 'Grün', bg: 'bg-green-600', border: 'border-green-600', classes: { bg: 'bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 focus:bg-green-700 dark:focus:bg-green-600', text: 'text-green-600 dark:text-green-400', border: 'border-green-200 dark:border-green-800', bgLight: 'bg-green-50 dark:bg-green-950' } },
            red: { name: 'Rot', bg: 'bg-red-600', border: 'border-red-600', classes: { bg: 'bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 dark:focus:bg-red-600', text: 'text-red-600 dark:text-red-400', border: 'border-red-200 dark:border-red-800', bgLight: 'bg-red-50 dark:bg-red-950' } },
            orange: { name: 'Orange', bg: 'bg-orange-600', border: 'border-orange-600', classes: { bg: 'bg-orange-600 dark:bg-orange-500 hover:bg-orange-700 dark:hover:bg-orange-600 focus:bg-orange-700 dark:focus:bg-orange-600', text: 'text-orange-600 dark:text-orange-400', border: 'border-orange-200 dark:border-orange-800', bgLight: 'bg-orange-50 dark:bg-orange-950' } },
            pink: { name: 'Pink', bg: 'bg-pink-600', border: 'border-pink-600', classes: { bg: 'bg-pink-600 dark:bg-pink-500 hover:bg-pink-700 dark:hover:bg-pink-600 focus:bg-pink-700 dark:focus:bg-pink-600', text: 'text-pink-600 dark:text-pink-400', border: 'border-pink-200 dark:border-pink-800', bgLight: 'bg-pink-50 dark:bg-pink-950' } },
            indigo: { name: 'Indigo', bg: 'bg-indigo-600', border: 'border-indigo-600', classes: { bg: 'bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:bg-indigo-700 dark:focus:bg-indigo-600', text: 'text-indigo-600 dark:text-indigo-400', border: 'border-indigo-200 dark:border-indigo-800', bgLight: 'bg-indigo-50 dark:bg-indigo-950' } },
            teal: { name: 'Türkis', bg: 'bg-teal-600', border: 'border-teal-600', classes: { bg: 'bg-teal-600 dark:bg-teal-500 hover:bg-teal-700 dark:hover:bg-teal-600 focus:bg-teal-700 dark:focus:bg-teal-600', text: 'text-teal-600 dark:text-teal-400', border: 'border-teal-200 dark:border-teal-800', bgLight: 'bg-teal-50 dark:bg-teal-950' } },
            amber: { name: 'Bernstein', bg: 'bg-amber-600', border: 'border-amber-600', classes: { bg: 'bg-amber-600 dark:bg-amber-500 hover:bg-amber-700 dark:hover:bg-amber-600 focus:bg-amber-700 dark:focus:bg-amber-600', text: 'text-amber-600 dark:text-amber-400', border: 'border-amber-200 dark:border-amber-800', bgLight: 'bg-amber-50 dark:bg-amber-950' } },
            slate: { name: 'Grau', bg: 'bg-slate-600', border: 'border-slate-600', classes: { bg: 'bg-slate-700 dark:bg-slate-300 hover:bg-slate-800 dark:hover:bg-slate-200 focus:bg-slate-800 dark:focus:bg-slate-200', text: 'text-slate-600 dark:text-slate-400', border: 'border-slate-200 dark:border-slate-800', bgLight: 'bg-slate-50 dark:bg-slate-950' } }
        },

        init() {
            this.applyColor(this.currentColor);
            window.addEventListener('storage', (e) => {
                if (e.key === 'accentColor') {
                    this.currentColor = localStorage.getItem('accentColor') || 'purple';
                    this.applyColor(this.currentColor);
                }
            });
        },

        toggle() { this.open = !this.open; },
        close() { this.open = false; },

        setColor(color) {
            this.currentColor = color; this.applyColor(color);
            try { localStorage.setItem('accentColor', color); } catch (e) { console.error('Failed to save color preference:', e); }
            this.close();
        },

        applyColor(color) {
            const elements = document.querySelectorAll('[data-accent]');
            const colorClasses = this.colors[color].classes;
            elements.forEach(el => {
                const type = el.getAttribute('data-accent');
                const oldClasses = el.className.split(' ').filter(c => !c.includes('-purple-') && !c.includes('-blue-') && !c.includes('-green-') && !c.includes('-red-') && !c.includes('-orange-') && !c.includes('-pink-') && !c.includes('-indigo-') && !c.includes('-teal-') && !c.includes('-amber-') && !c.includes('-slate-'));
                el.className = oldClasses.join(' ') + ' ' + (colorClasses[type] || '');
            });
        }
    });

    function register(){
        if (window.Alpine && typeof window.Alpine.data === 'function') {
            Alpine.data('colorPicker', factory);
        } else {
            document.addEventListener('alpine:init', () => Alpine.data('colorPicker', factory));
        }
    }

    register();
})();
</script>
@endpush
@endonce