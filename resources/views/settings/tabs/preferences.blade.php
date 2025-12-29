{{-- Preferences Settings Tab --}}
<div class="space-y-6">
    {{-- Appearance Settings --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-palette text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('ui.appearance') ?? 'Appearance' }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.appearance_description') ?? 'Customize how the application looks and feels' }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('settings.preferences.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Theme Preference --}}
                <div>
                    <label for="theme_preference" class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                        <i class="fas fa-moon mr-2 text-gray-400"></i>{{ __('ui.theme_preference') }}
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(['system' => ['icon' => 'fas fa-desktop', 'label' => __('ui.theme_system')], 'light' => ['icon' => 'fas fa-sun', 'label' => __('ui.theme_light')], 'dark' => ['icon' => 'fas fa-moon', 'label' => __('ui.theme_dark')]] as $theme => $data)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="theme_preference" value="{{ $theme }}"
                                    {{ ($user->theme_preference ?? 'system') === $theme ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="flex flex-col items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all hover:border-gray-300 dark:hover:border-gray-600">
                                    <i class="{{ $data['icon'] }} text-2xl mb-2 {{ ($user->theme_preference ?? 'system') === $theme ? 'text-blue-600' : 'text-gray-400' }}"></i>
                                    <span class="text-sm font-medium {{ ($user->theme_preference ?? 'system') === $theme ? 'text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300' }}">{{ $data['label'] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.theme_help') }}</p>
                </div>

                {{-- Accent Color --}}
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                        <i class="fas fa-paint-brush mr-2 text-gray-400"></i>{{ __('ui.accent_color') }}
                    </label>
                    <div class="grid grid-cols-5 gap-3">
                        @foreach([
                            'purple' => ['hex' => '#8B5CF6', 'name' => 'Lila'],
                            'blue' => ['hex' => '#3B82F6', 'name' => 'Blau'],
                            'green' => ['hex' => '#10B981', 'name' => 'Grün'],
                            'red' => ['hex' => '#EF4444', 'name' => 'Rot'],
                            'orange' => ['hex' => '#F97316', 'name' => 'Orange'],
                            'pink' => ['hex' => '#EC4899', 'name' => 'Rosa'],
                            'indigo' => ['hex' => '#6366F1', 'name' => 'Indigo'],
                            'teal' => ['hex' => '#14B8A6', 'name' => 'Türkis'],
                            'amber' => ['hex' => '#F59E0B', 'name' => 'Bernstein'],
                            'slate' => ['hex' => '#64748B', 'name' => 'Grau']
                        ] as $color => $data)
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="accent_color" value="{{ $color }}"
                                    {{ ($user->accent_color ?? 'purple') === $color ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="relative w-full aspect-square rounded-lg border-2 border-gray-300 dark:border-gray-600 peer-checked:border-gray-900 dark:peer-checked:border-white transition-all hover:scale-105 group-hover:shadow-lg"
                                     style="background-color: {{ $data['hex'] }};">
                                    @if(($user->accent_color ?? 'purple') === $color)
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <i class="fas fa-check text-white text-lg drop-shadow-lg"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-center mt-1">
                                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $data['name'] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
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

    {{-- Localization Settings --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-globe text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('ui.localization') ?? 'Localization' }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.localization_description') ?? 'Set your language and regional preferences' }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('settings.preferences.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Language --}}
                <div>
                    <label for="locale" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-language mr-2 text-gray-400"></i>{{ __('ui.language') }}
                    </label>
                    <select id="locale" name="locale"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors">
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
                        <i class="fas fa-clock mr-2 text-gray-400"></i>{{ __('ui.timezone') }}
                    </label>
                    <select id="timezone" name="timezone"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors">
                        @foreach(timezone_identifiers_list() as $tz)
                            <option value="{{ $tz }}" {{ ($user->timezone ?? 'UTC') === $tz ? 'selected' : '' }}>
                                {{ $tz }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.timezone_help') }}</p>
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
</div>