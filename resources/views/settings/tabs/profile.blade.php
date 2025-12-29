{{-- Profile Settings Tab --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                <i class="fas fa-user text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('ui.profile_information') }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.profile_info_description') }}</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <form method="POST" action="{{ route('settings.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Twitch Information (Read-only) --}}
            <div class="grid md:grid-cols-2 gap-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('ui.display_name') }}
                    </label>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $user->display_name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('ui.username') }}
                    </label>
                    <p class="text-gray-900 dark:text-white">{{ $user->twitch_login }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('ui.email_address') }}
                    </label>
                    <p class="text-gray-900 dark:text-white">{{ $user->twitch_email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('ui.twitch_id') }}
                    </label>
                    <p class="text-gray-900 dark:text-white font-mono text-sm">{{ $user->twitch_id }}</p>
                </div>
            </div>

            {{-- Display Name --}}
            <div>
                <label for="twitch_display_name" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.display_name') }}
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ __('ui.optional') }})</span>
                </label>
                <input type="text" id="twitch_display_name" name="twitch_display_name" maxlength="255"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                    placeholder="{{ __('ui.display_name_placeholder') }}"
                    value="{{ old('twitch_display_name', $user->twitch_display_name) }}">
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.display_name_help') }}</p>
            </div>

            {{-- Email --}}
            <div>
                <label for="twitch_email" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.email_address') }}
                </label>
                <input type="email" id="twitch_email" name="twitch_email" required
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                    value="{{ old('twitch_email', $user->twitch_email) }}">
            </div>

            {{-- Biography --}}
            <div>
                <label for="intro" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.biography') }}
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ __('ui.optional') }})</span>
                </label>
                <textarea id="intro" name="intro" rows="4" maxlength="1000"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                    placeholder="{{ __('ui.bio_placeholder') }}">{{ old('intro', $user->intro) }}</textarea>
                <div class="mt-2 flex justify-between items-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.bio_help') }}</p>
                    <span class="text-xs text-gray-500 dark:text-gray-400" id="bio-counter">
                        {{ strlen(old('intro', $user->intro ?? '')) }}/1000
                    </span>
                </div>
            </div>

            {{-- Availability --}}
            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="hidden" name="available_for_jobs" value="0">
                    <input type="checkbox" id="available_for_jobs" name="available_for_jobs" value="1"
                        {{ old('available_for_jobs', $user->available_for_jobs) ? 'checked' : '' }}
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="available_for_jobs" class="ml-2 block text-sm text-gray-900 dark:text-white">
                        {{ __('ui.available_for_jobs') }}
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="hidden" name="allow_clip_sharing" value="0">
                    <input type="checkbox" id="allow_clip_sharing" name="allow_clip_sharing" value="1"
                        {{ old('allow_clip_sharing', $user->allow_clip_sharing) ? 'checked' : '' }}
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="allow_clip_sharing" class="ml-2 block text-sm text-gray-900 dark:text-white">
                        {{ __('ui.allow_clip_sharing') }}
                    </label>
                </div>
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