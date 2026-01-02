<div class="space-y-8">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-zinc-100 mb-2">{{ __('Appearance Settings') }}</h2>
        <p class="text-zinc-400">{{ __('Customize how ClipNook looks and feels') }}</p>
    </div>

    <!-- Theme Selection -->
    <div class="space-y-6 p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <i class="fa-solid fa-palette text-(--color-accent-400) text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Theme') }}</h3>
                <p class="text-sm text-zinc-400 mb-4">{{ __('Choose your preferred color scheme') }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach($availableThemes as $themeKey => $themeName)
                        <label class="relative">
                            <input
                                type="radio"
                                wire:model.live="theme"
                                value="{{ $themeKey }}"
                                class="sr-only peer"
                            >
                            <div class="p-4 border-2 border-zinc-700 rounded-lg cursor-pointer transition-all peer-checked:border-(--color-accent-500) peer-checked:bg-(--color-accent-500)/10 hover:border-zinc-600">
                                <div class="text-center">
                                    <i class="fa-solid fa-{{ $themeKey === 'light' ? 'sun' : ($themeKey === 'dark' ? 'moon' : 'circle-half-stroke') }} text-2xl mb-2 text-zinc-300"></i>
                                    <div class="text-sm font-medium text-zinc-200">{{ $themeName }}</div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Display Options -->
    <div class="space-y-6 p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <i class="fa-solid fa-display text-(--color-accent-400) text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Display Options') }}</h3>
                <p class="text-sm text-zinc-400 mb-4">{{ __('Customize your viewing experience') }}</p>

                <div class="space-y-4">
                    <!-- Compact Mode -->
                    <label class="flex items-center justify-between p-4 bg-zinc-900/50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-zinc-200">{{ __('Compact Mode') }}</div>
                            <div class="text-xs text-zinc-400">{{ __('Show more content in less space') }}</div>
                        </div>
                        <input
                            type="checkbox"
                            wire:model.live="compact_mode"
                            class="size-5 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)"
                        >
                    </label>

                    <!-- Show Thumbnails -->
                    <label class="flex items-center justify-between p-4 bg-zinc-900/50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-zinc-200">{{ __('Show Thumbnails') }}</div>
                            <div class="text-xs text-zinc-400">{{ __('Display clip thumbnails in lists') }}</div>
                        </div>
                        <input
                            type="checkbox"
                            wire:model.live="show_thumbnails"
                            class="size-5 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)"
                        >
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="space-y-6 p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <i class="fa-solid fa-list text-(--color-accent-400) text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Pagination') }}</h3>
                <p class="text-sm text-zinc-400 mb-4">{{ __('Number of clips to show per page') }}</p>

                <div class="max-w-xs">
                    <select
                        wire:model.live="clips_per_page"
                        class="block w-full rounded-lg border-0 bg-zinc-800/50 px-4 py-3 text-zinc-200 shadow-sm ring-1 ring-zinc-700/50 focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900"
                    >
                        <option value="6">6 clips</option>
                        <option value="12">12 clips</option>
                        <option value="24">24 clips</option>
                        <option value="48">48 clips</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end pt-6 border-t border-zinc-700/50">
        <button
            wire:click="updateAppearance"
            wire:loading.attr="disabled"
            class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-(--color-accent-500) to-(--color-accent-400) px-8 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-(--color-accent-500)/25 hover:scale-105 disabled:opacity-50 disabled:hover:scale-100 focus:outline-none focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900"
        >
            <div wire:loading.remove wire:target="updateAppearance" class="flex items-center gap-2">
                <i class="fa-solid fa-save"></i>
                {{ __('Save Settings') }}
            </div>
            <div wire:loading wire:target="updateAppearance" class="flex items-center gap-2">
                <i class="fa-solid fa-spinner fa-spin"></i>
                {{ __('Saving...') }}
            </div>
        </button>
    </div>


</div>