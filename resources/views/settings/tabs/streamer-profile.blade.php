{{-- Streamer Profile Settings --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                <i class="fas fa-broadcast-tower text-purple-600 dark:text-purple-400"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('ui.streamer_profile') }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.streamer_profile_description') }}</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <form method="POST" action="{{ route('settings.roles.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Include role checkboxes (hidden) --}}
            <input type="hidden" name="is_streamer" value="1">
            <input type="hidden" name="is_cutter" value="{{ $user->is_cutter ? 1 : 0 }}">

            {{-- Stream Schedule --}}
            <div>
                <label for="stream_schedule" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.stream_schedule') }}
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ __('ui.optional') }})</span>
                </label>
                <input type="text" id="stream_schedule" name="stream_schedule" maxlength="255"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors"
                    placeholder="{{ __('ui.stream_schedule_placeholder') }}"
                    value="{{ old('stream_schedule', $user->streamerProfile?->stream_schedule) }}">
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.stream_schedule_help') }}</p>
            </div>

            {{-- Preferred Games --}}
            <div>
                <label for="preferred_games" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.preferred_games') }}
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ __('ui.optional') }})</span>
                </label>
                <input type="text" id="preferred_games" name="preferred_games" maxlength="255"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors"
                    placeholder="{{ __('ui.preferred_games_placeholder') }}"
                    value="{{ old('preferred_games', $user->streamerProfile?->preferred_games) }}">
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.preferred_games_help') }}</p>
            </div>

            {{-- Stream Quality --}}
            <div>
                <label for="stream_quality" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.stream_quality') }}
                </label>
                <select id="stream_quality" name="stream_quality"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors">
                    <option value="480p" {{ ($user->streamerProfile?->stream_quality ?? '720p') === '480p' ? 'selected' : '' }}>
                        480p
                    </option>
                    <option value="720p" {{ ($user->streamerProfile?->stream_quality ?? '720p') === '720p' ? 'selected' : '' }}>
                        720p
                    </option>
                    <option value="1080p" {{ ($user->streamerProfile?->stream_quality ?? '720p') === '1080p' ? 'selected' : '' }}>
                        1080p
                    </option>
                    <option value="1440p" {{ ($user->streamerProfile?->stream_quality ?? '720p') === '1440p' ? 'selected' : '' }}>
                        1440p
                    </option>
                    <option value="4k" {{ ($user->streamerProfile?->stream_quality ?? '720p') === '4k' ? 'selected' : '' }}>
                        4K
                    </option>
                </select>
            </div>

            {{-- Has Overlay --}}
            <div class="flex items-center">
                <input type="hidden" name="has_overlay" value="0">
                <input type="checkbox" id="has_overlay" name="has_overlay" value="1"
                    {{ old('has_overlay', $user->streamerProfile?->has_overlay) ? 'checked' : '' }}
                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                <label for="has_overlay" class="ml-2 block text-sm text-gray-900 dark:text-white">
                    {{ __('ui.has_overlay') }}
                </label>
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