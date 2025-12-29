{{-- Preferences Settings Tab --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                <i class="fas fa-palette text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('ui.preferences') }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.preferences_description') }}</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <form method="POST" action="{{ route('settings.preferences.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Theme Preference --}}
            <div>
                <label for="theme_preference" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.theme_preference') }}
                </label>
                <select id="theme_preference" name="theme_preference"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    <option value="system" {{ ($user->theme_preference ?? 'system') === 'system' ? 'selected' : '' }}>
                        {{ __('ui.theme_system') }}
                    </option>
                    <option value="light" {{ ($user->theme_preference ?? 'system') === 'light' ? 'selected' : '' }}>
                        {{ __('ui.theme_light') }}
                    </option>
                    <option value="dark" {{ ($user->theme_preference ?? 'system') === 'dark' ? 'selected' : '' }}>
                        {{ __('ui.theme_dark') }}
                    </option>
                </select>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.theme_help') }}</p>
            </div>

            {{-- Language --}}
            <div>
                <label for="locale" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.language') }}
                </label>
                <select id="locale" name="locale"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    @foreach(config('app.locales', ['en' => 'English', 'de' => 'Deutsch']) as $code => $name)
                        <option value="{{ $code }}" {{ ($user->locale ?? config('app.locale', 'en')) === $code ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.language_help') }}</p>
            </div>

            {{-- Timezone --}}
            <div>
                <label for="timezone" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.timezone') }}
                </label>
                <select id="timezone" name="timezone"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    @foreach(timezone_identifiers_list() as $tz)
                        <option value="{{ $tz }}" {{ ($user->timezone ?? 'UTC') === $tz ? 'selected' : '' }}>
                            {{ $tz }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.timezone_help') }}</p>
            </div>

            {{-- Accent Color --}}
            <div>
                <label for="accent_color" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.accent_color') }}
                </label>
                <select id="accent_color" name="accent_color"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    @foreach([
                        'purple' => 'Lila',
                        'blue' => 'Blau',
                        'green' => 'Grün',
                        'red' => 'Rot',
                        'orange' => 'Orange',
                        'pink' => 'Rosa',
                        'indigo' => 'Indigo',
                        'teal' => 'Türkis',
                        'amber' => 'Bernstein',
                        'slate' => 'Grau'
                    ] as $color => $name)
                        <option value="{{ $color }}" {{ ($user->accent_color ?? 'purple') === $color ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.accent_color_help') }}</p>
            </div>

            {{-- Submit Button --}}
            <div class="pt-4 border-t border-gray-200 dark:border-gray-800 flex justify-end">
                <x-button type="submit" variant="primary" accent="bg">
                    <i class="fas fa-save mr-2"></i>
                    {{ __('ui.save_changes') }}
                </x-button>
            </div>
        </form>
    </div>
</div>