<div class="max-w-3xl mx-auto space-y-6">
    <!-- Step 1: Clip URL/ID eingeben -->
    @if(!$clipInfo)
        <div class="bg-neutral-900 rounded-md border border-neutral-800 p-6">
            <h2 class="text-lg font-semibold text-neutral-100 mb-4">{{ __('clips.clip_id_label') }}</h2>

            <form wire:submit.prevent="checkClip" class="space-y-4">
                <div>
                    <input
                        type="text"
                        id="twitchClipId"
                        wire:model="twitchClipId"
                        placeholder="{{ __('clips.clip_id_placeholder') }}"
                        class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-md text-white placeholder-neutral-500 focus:border-purple-500 focus:outline-none transition-colors"
                        autocomplete="off"
                    >

                    @error('twitchClipId')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror

                    <p class="mt-2 text-sm text-neutral-500">
                        {{ __('clips.clip_id_help', ['example' => 'PluckyInventiveCarrotPastaThat']) }}
                    </p>
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full sm:w-auto px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors disabled:opacity-50"
                >
                    <span wire:loading.remove>{{ __('clips.check_clip_button') }}</span>
                    <span wire:loading>{{ __('clips.checking_button') }}</span>
                </button>
            </form>
        </div>

        <!-- Hilfe-Info -->
        <div class="bg-neutral-900 rounded-md border border-neutral-800 p-6">
            <h3 class="text-base font-medium text-neutral-100 mb-3">{{ __('clips.help_title') }}</h3>
            <div class="space-y-2 text-sm text-neutral-400">
                <p>{{ __('clips.help_step_1', ['example_url' => 'https://clips.twitch.tv/PluckyInventiveCarrotPastaThat']) }}</p>
                <p>{{ __('clips.help_step_2', ['example_id' => 'PluckyInventiveCarrotPastaThat']) }}</p>
            </div>
        </div>
    @endif

    <!-- Step 2: Clip-Info & Preview -->
    @if($clipInfo)
        <!-- Clip-Informationen -->
        <div class="bg-neutral-900 rounded-md border border-neutral-800 p-6">
            <h2 class="text-lg font-semibold text-neutral-100 mb-4">{{ __('clips.clip_info_title') }}</h2>

            <div class="space-y-3">
                <div>
                    <div class="text-xs text-neutral-500 uppercase mb-1">{{ __('clips.title_label') }}</div>
                    <div class="text-white">{{ e($clipInfo['title']) }}</div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <div class="text-xs text-neutral-500 uppercase mb-1">{{ __('clips.broadcaster_label') }}</div>
                        <div class="text-white">{{ e($clipInfo['broadcasterName']) }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-neutral-500 uppercase mb-1">{{ __('clips.view_count_label') }}</div>
                        <div class="text-white">{{ number_format($clipInfo['viewCount']) }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-neutral-500 uppercase mb-1">{{ __('clips.duration_label') }}</div>
                        <div class="text-white">{{ round($clipInfo['duration'], 1) }}s</div>
                    </div>

                    <div>
                        <div class="text-xs text-neutral-500 uppercase mb-1">{{ __('clips.created_at_label') }}</div>
                        <div class="text-white">{{ \Carbon\Carbon::parse($clipInfo['createdAt'])->format('M j, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clip-Vorschau -->
        <div class="bg-neutral-900 rounded-md border border-neutral-800 p-6">
            <livewire:twitch-player-consent :clip-info="$clipInfo" />
        </div>

        <!-- Submit Button -->
        <div class="flex gap-3">
            <button
                wire:click="submit"
                wire:loading.attr="disabled"
                class="flex-1 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors disabled:opacity-50"
            >
                <span wire:loading.remove>{{ __('clips.submit_clip_button') }}</span>
                <span wire:loading>{{ __('clips.submitting_button') }}</span>
            </button>

            <button
                wire:click="$set('clipInfo', null)"
                class="px-6 py-3 bg-neutral-800 hover:bg-neutral-700 text-white font-medium rounded-md transition-colors"
            >
                {{ __('clips.cancel_button') }}
            </button>
        </div>
    @endif

    <!-- Messages -->
    @if($successMessage)
        <div class="bg-green-900/50 border border-green-700 rounded-md p-4">
            <p class="text-green-200">{{ e($successMessage) }}</p>
        </div>
    @endif

    @if($errorMessage)
        <div class="bg-red-900/50 border border-red-700 rounded-md p-4">
            <p class="text-red-200">{{ $errorMessage }}</p>
        </div>
    @endif
</div>