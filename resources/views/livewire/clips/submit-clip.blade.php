<div class="max-w-4xl mx-auto bg-gray-900 text-white rounded-lg shadow-md p-6 border border-gray-700">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-white mb-2 flex items-center justify-center">
            <i class="fas fa-video mr-3 text-blue-400"></i>
            {{ __('clips.ui_title') }}
        </h2>
        <p class="text-lg text-gray-300 text-center">{{ __('clips.ui_description') }}</p>
    </div>

    <!-- Step Indicator -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-4">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center {{ !$clipInfo ? 'bg-blue-600 text-white' : 'bg-gray-600 text-gray-300' }}">
                    <i class="fas fa-search text-sm"></i>
                </div>
                <span class="ml-2 text-sm font-medium {{ !$clipInfo ? 'text-blue-400' : 'text-gray-400' }}">{{ __('clips.step_check') }}</span>
            </div>
            <div class="w-8 h-0.5 {{ $clipInfo ? 'bg-blue-600' : 'bg-gray-600' }}"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $clipInfo && !$showPlayer ? 'bg-blue-600 text-white' : ($showPlayer ? 'bg-green-600 text-white' : 'bg-gray-600 text-gray-300') }}">
                    <i class="fas fa-info-circle text-sm"></i>
                </div>
                <span class="ml-2 text-sm font-medium {{ $clipInfo && !$showPlayer ? 'text-blue-400' : ($showPlayer ? 'text-green-400' : 'text-gray-400') }}">{{ __('clips.step_info') }}</span>
            </div>
            <div class="w-8 h-0.5 {{ $showPlayer ? 'bg-green-600' : 'bg-gray-600' }}"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $showPlayer ? 'bg-green-600 text-white' : 'bg-gray-600 text-gray-300' }}">
                    <i class="fas fa-paper-plane text-sm"></i>
                </div>
                <span class="ml-2 text-sm font-medium {{ $showPlayer ? 'text-green-400' : 'text-gray-400' }}">{{ __('clips.step_submit') }}</span>
            </div>
        </div>
    </div>

    <!-- Step 1: Clip ID Input -->
    @if(!$clipInfo)
    <div class="bg-gray-800 rounded-lg p-8 border border-gray-600 mb-6">
        <form wire:submit.prevent="checkClip" class="space-y-6">
            <!-- Clip ID Input -->
            <div>
                <label for="twitchClipId" class="block text-sm font-medium text-gray-200 mb-2 flex items-center">
                    <i class="fas fa-link mr-2 text-gray-400"></i>
                    {{ __('clips.clip_id_label') }}
                </label>
                <div class="relative">
                    <input
                        type="text"
                        id="twitchClipId"
                        wire:model="twitchClipId"
                        placeholder="{{ __('clips.clip_id_placeholder') }}"
                        class="w-full px-4 py-4 pl-12 border border-gray-600 rounded-lg bg-gray-800 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-lg @error('twitchClipId') border-red-500 @enderror"
                        autocomplete="off"
                    >
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                @error('twitchClipId')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-400">
                    {{ __('clips.clip_id_help', ['example' => 'PluckyInventiveCarrotPastaThat']) }}
                </p>
            </div>

            <!-- Check Button -->
            <div class="flex items-center justify-between pt-4">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="inline-flex items-center px-8 py-4 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-lg"
                >
                    <i wire:loading class="fas fa-spinner fa-spin mr-3"></i>
                    <i wire:loading.remove class="fas fa-search mr-3"></i>
                    <span wire:loading.remove>{{ __('clips.check_clip_button') }}</span>
                    <span wire:loading>{{ __('clips.checking_button') }}</span>
                </button>

                <div class="text-sm text-gray-400">
                    <span class="inline-flex items-center">
                        <i class="fas fa-shield-alt mr-1 text-green-400"></i>
                        {{ __('clips.secure_private') }}
                    </span>
                </div>
            </div>
        </form>
    </div>
    @endif

    <!-- Step 2: Clip Info Display -->
    @if($clipInfo && !$showPlayer)
    <div class="space-y-6">
        <div class="bg-gray-800 rounded-lg p-8 border border-gray-600">
            <h3 class="text-2xl font-semibold text-white mb-6 flex items-center">
                <i class="fas fa-info-circle mr-3 text-blue-400"></i>
                {{ __('clips.clip_info_title') }}
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-700 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('clips.title_label') }}</label>
                    <p class="text-white text-lg font-medium">{{ $clipInfo['title'] }}</p>
                </div>
                <div class="bg-gray-700 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('clips.broadcaster_label') }}</label>
                    <p class="text-white text-lg">{{ $clipInfo['broadcasterName'] }}</p>
                </div>
                <div class="bg-gray-700 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('clips.view_count_label') }}</label>
                    <p class="text-white text-lg">{{ number_format($clipInfo['viewCount']) }}</p>
                </div>
                <div class="bg-gray-700 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('clips.duration_label') }}</label>
                    <p class="text-white text-lg">{{ round($clipInfo['duration'], 1) }}s</p>
                </div>
                <div class="md:col-span-2 bg-gray-700 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('clips.created_at_label') }}</label>
                    <p class="text-white text-lg">{{ \Carbon\Carbon::parse($clipInfo['createdAt'])->format('M j, Y \a\t g:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- GDPR Warning and Load Player -->
        <div class="bg-yellow-900 border border-yellow-700 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-2xl mr-4"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-lg font-medium text-yellow-200 mb-2">{{ __('clips.gdpr_warning') }}</h4>
                    <div class="mt-4">
                        <button
                            wire:click="loadPlayer"
                            class="inline-flex items-center px-6 py-3 bg-yellow-600 text-white font-medium rounded-lg hover:bg-yellow-700 focus:ring-2 focus:ring-yellow-500 transition-colors text-lg"
                        >
                            <i class="fas fa-play mr-3"></i>
                            {{ __('clips.load_player_button') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reset Button -->
        <div class="flex justify-start">
            <button
                wire:click="resetClip"
                class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 transition-colors"
            >
                <i class="fas fa-arrow-left mr-3"></i>
                {{ __('clips.reset_button') }}
            </button>
        </div>
    </div>
    @endif

    <!-- Step 3: Player and Submit -->
    @if($showPlayer)
    <div class="space-y-6">
        <!-- Player -->
        <div class="bg-gray-800 rounded-lg p-8 border border-gray-600">
            <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-play-circle mr-3 text-green-400"></i>
                {{ __('clips.clip_preview') }}
            </h3>
            <iframe
                src="{{ $clipInfo['embedUrl'] }}&parent={{ request()->getHost() }}"
                height="480"
                width="100%"
                allowfullscreen
                class="rounded-lg border border-gray-600"
            ></iframe>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between bg-gray-800 rounded-lg p-6 border border-gray-600">
            <button
                wire:click="submit"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-not-allowed"
                class="inline-flex items-center px-8 py-4 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-lg"
            >
                <i wire:loading class="fas fa-spinner fa-spin mr-3"></i>
                <i wire:loading.remove class="fas fa-paper-plane mr-3"></i>
                <span wire:loading.remove>{{ __('clips.submit_clip_button') }}</span>
                <span wire:loading>{{ __('clips.submitting_button') }}</span>
            </button>

            <button
                wire:click="resetClip"
                class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 transition-colors"
            >
                <i class="fas fa-arrow-left mr-3"></i>
                {{ __('clips.reset_button') }}
            </button>
        </div>
    </div>
    @endif

    <!-- Success Message -->
    @if($successMessage)
        <div class="mt-8 p-6 bg-green-900 border border-green-700 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400 text-3xl mr-4"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-green-200 mb-1">{{ __('clips.success_title') }}</h3>
                    <p class="text-sm font-medium text-green-200">{{ $successMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Message -->
    @if($errorMessage)
        <div class="mt-8 p-6 bg-red-900 border border-red-700 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400 text-3xl mr-4"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-red-200 mb-1">{{ __('clips.error_title') }}</h3>
                    <p class="text-sm font-medium text-red-200">{{ $errorMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Help Section -->
    @if(!$clipInfo)
    <div class="mt-12 border-t border-gray-600 pt-8">
        <h3 class="text-xl font-medium text-gray-200 mb-4 flex items-center justify-center">
            <i class="fas fa-question-circle mr-3 text-blue-400"></i>
            {{ __('clips.help_title') }}
        </h3>
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-600">
            <ol class="list-decimal list-inside space-y-3 text-gray-300 text-lg">
                <li>{{ __('clips.help_step_1', ['example_url' => 'https://clips.twitch.tv/PluckyInventiveCarrotPastaThat']) }}</li>
                <li>{{ __('clips.help_step_2', ['example_id' => 'PluckyInventiveCarrotPastaThat']) }}</li>
                <li>{{ __('clips.help_step_3') }}</li>
            </ol>
        </div>
    </div>
    @endif
</div>