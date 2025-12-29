{{-- Avatar Settings Tab --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-pink-100 dark:bg-pink-900 rounded-lg flex items-center justify-center">
                <i class="fas fa-camera text-pink-600 dark:text-pink-400"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('ui.avatar') }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.avatar_description') }}</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        {{-- Current Avatar Display --}}
        <div class="mb-6">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">{{ __('ui.current_avatar') }}</h3>
            <div class="flex items-center gap-4">
                <img src="{{ $user->avatar_url }}" alt="{{ __('ui.avatar') }}"
                    class="w-20 h-20 rounded-full border-4 border-gray-200 dark:border-gray-700">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        @if($user->avatar_source === 'twitch')
                            {{ __('ui.avatar_from_twitch') }}
                        @elseif($user->avatar_source === 'custom')
                            {{ __('ui.avatar_custom') }}
                        @else
                            {{ __('ui.avatar_default') }}
                        @endif
                    </p>
                    @if($user->isAvatarDisabled())
                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ __('ui.avatar_disabled') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Avatar Actions --}}
        <div class="space-y-4">
            {{-- Upload Custom Avatar --}}
            <div>
                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('ui.upload_custom_avatar') }}
                </label>
                <form method="POST" action="{{ route('settings.avatar.upload') }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div class="flex items-center gap-3">
                        <input type="file" id="avatar" name="avatar" accept="image/*"
                            class="block w-full text-sm text-gray-500 dark:text-gray-400
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-lg file:border-0
                                   file:text-sm file:font-medium
                                   file:bg-indigo-50 dark:file:bg-indigo-900 file:text-indigo-700 dark:file:text-indigo-300
                                   hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800">
                        <x-button type="submit" variant="secondary" size="sm">
                            <i class="fas fa-upload mr-2"></i>
                            {{ __('ui.upload') }}
                        </x-button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.avatar_upload_help') }}</p>
                </form>
            </div>

            {{-- Avatar Management Actions --}}
            <div class="border-t border-gray-200 dark:border-gray-800 pt-4">
                <div class="flex flex-wrap gap-3">
                    {{-- Restore from Twitch --}}
                    @if($user->isTwitchConnected())
                        <form method="POST" action="{{ route('settings.avatar.update') }}" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="restore">
                            <x-button type="submit" variant="outline" size="sm">
                                <i class="fab fa-twitch mr-2"></i>
                                {{ __('ui.restore_from_twitch') }}
                            </x-button>
                        </form>
                    @endif

                    {{-- Remove Avatar --}}
                    <form method="POST" action="{{ route('settings.avatar.update') }}" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="remove">
                        <x-button type="submit" variant="outline" size="sm" class="text-red-600 dark:text-red-400 border-red-300 dark:border-red-700 hover:bg-red-50 dark:hover:bg-red-950">
                            <i class="fas fa-trash mr-2"></i>
                            {{ __('ui.remove_avatar') }}
                        </x-button>
                    </form>
                </div>

                @if(!$user->isTwitchConnected())
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ __('ui.connect_twitch_for_restore') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>