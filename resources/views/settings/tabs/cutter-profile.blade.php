{{-- Cutter Profile Settings --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                <i class="fas fa-cut text-green-600 dark:text-green-400"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('ui.cutter_profile') }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.cutter_profile_description') }}</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <form method="POST" action="{{ route('settings.roles.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Include role checkboxes (hidden) --}}
            <input type="hidden" name="is_streamer" value="{{ $user->is_streamer ? 1 : 0 }}">
            <input type="hidden" name="is_cutter" value="1">

            {{-- Hourly Rate --}}
            <div>
                <label for="hourly_rate" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.hourly_rate') }}
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ __('ui.optional') }})</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                    </div>
                    <input type="number" id="hourly_rate" name="hourly_rate" min="0" max="1000" step="0.01"
                        class="pl-7 w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                        placeholder="0.00"
                        value="{{ old('hourly_rate', $user->cutterProfile?->hourly_rate) }}">
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.hourly_rate_help') }}</p>
            </div>

            {{-- Response Time --}}
            <div>
                <label for="response_time" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.response_time') }}
                </label>
                <select id="response_time" name="response_time"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors">
                    <option value="24" {{ ($user->cutterProfile?->response_time ?? '24') === '24' ? 'selected' : '' }}>
                        {{ __('ui.response_time_24h') }}
                    </option>
                    <option value="48" {{ ($user->cutterProfile?->response_time ?? '24') === '48' ? 'selected' : '' }}>
                        {{ __('ui.response_time_48h') }}
                    </option>
                    <option value="72" {{ ($user->cutterProfile?->response_time ?? '24') === '72' ? 'selected' : '' }}>
                        {{ __('ui.response_time_72h') }}
                    </option>
                    <option value="168" {{ ($user->cutterProfile?->response_time ?? '24') === '168' ? 'selected' : '' }}>
                        {{ __('ui.response_time_1w') }}
                    </option>
                </select>
            </div>

            {{-- Skills --}}
            <div>
                <label for="skills" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.skills') }}
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ __('ui.optional') }})</span>
                </label>
                <textarea id="skills" name="skills" rows="3" maxlength="1000"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                    placeholder="{{ __('ui.skills_placeholder') }}">{{ old('skills', is_array($user->cutterProfile?->skills) ? json_encode($user->cutterProfile->skills, JSON_PRETTY_PRINT) : $user->cutterProfile?->skills) }}</textarea>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.skills_help') }}</p>
            </div>

            {{-- Portfolio URL --}}
            <div>
                <label for="portfolio_url" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.portfolio_url') }}
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ __('ui.optional') }})</span>
                </label>
                <input type="url" id="portfolio_url" name="portfolio_url" maxlength="255"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                    placeholder="{{ __('ui.portfolio_url_placeholder') }}"
                    value="{{ old('portfolio_url', $user->cutterProfile?->portfolio_url) }}">
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.portfolio_url_help') }}</p>
            </div>

            {{-- Experience Years --}}
            <div>
                <label for="experience_years" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.experience_years') }}
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ __('ui.optional') }})</span>
                </label>
                <input type="number" id="experience_years" name="experience_years" min="0" max="50"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                    placeholder="0"
                    value="{{ old('experience_years', $user->cutterProfile?->experience_years) }}">
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.experience_years_help') }}</p>
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