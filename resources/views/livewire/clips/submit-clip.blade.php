<div class="min-h-screen bg-gray-950 text-white">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2 flex items-center justify-center gap-3">
                <i class="fas fa-video text-purple-400"></i>
                {{ __('clips.ui_title') }}
            </h1>
            <p class="text-gray-400 text-lg">{{ __('clips.ui_description') }}</p>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-center">
                <div class="flex items-center space-x-2">
                    <!-- Step 1 -->
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 {{ !$clipInfo ? 'border-purple-400 bg-purple-400 text-white' : 'border-green-500 bg-green-500 text-white' }}">
                            <i class="fas fa-search text-sm"></i>
                        </div>
                        <span class="text-xs mt-2 text-center {{ !$clipInfo ? 'text-purple-400 font-medium' : 'text-green-500' }}">{{ __('clips.step_check') }}</span>
                    </div>

                    <!-- Connector -->
                    <div class="w-12 h-0.5 {{ $clipInfo ? 'bg-green-500' : 'bg-gray-600' }}"></div>

                    <!-- Step 2 -->
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 {{ $clipInfo && !$showPlayer ? 'border-purple-400 bg-purple-400 text-white' : ($showPlayer ? 'border-green-500 bg-green-500 text-white' : 'border-gray-600 text-gray-400') }}">
                            <i class="fas fa-info-circle text-sm"></i>
                        </div>
                        <span class="text-xs mt-2 text-center {{ $clipInfo && !$showPlayer ? 'text-purple-400 font-medium' : ($showPlayer ? 'text-green-500' : 'text-gray-400') }}">{{ __('clips.step_info') }}</span>
                    </div>

                    <!-- Connector -->
                    <div class="w-12 h-0.5 {{ $showPlayer ? 'bg-green-500' : 'bg-gray-600' }}"></div>

                    <!-- Step 3 -->
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 {{ $showPlayer ? 'border-purple-400 bg-purple-400 text-white' : 'border-gray-600 text-gray-400' }}">
                            <i class="fas fa-paper-plane text-sm"></i>
                        </div>
                        <span class="text-xs mt-2 text-center {{ $showPlayer ? 'text-purple-400 font-medium' : 'text-gray-400' }}">{{ __('clips.step_submit') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 1: Clip ID Input -->
        @if(!$clipInfo)
        <div class="bg-gray-900 rounded-lg p-6 border border-gray-800">
            <form wire:submit.prevent="checkClip" class="space-y-6">
                <div>
                    <label for="twitchClipId" class="block text-sm font-medium text-gray-300 mb-2 flex items-center gap-2">
                        <i class="fas fa-link text-purple-400"></i>
                        {{ __('clips.clip_id_label') }}
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            id="twitchClipId"
                            wire:model="twitchClipId"
                            placeholder="{{ __('clips.clip_id_placeholder') }}"
                            class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-md text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent @error('twitchClipId') border-red-500 @enderror"
                            autocomplete="off"
                            aria-describedby="twitchClipId-help"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-search text-gray-500"></i>
                        </div>
                    </div>
                    @error('twitchClipId')
                        <p class="mt-1 text-sm text-red-400 flex items-center gap-2" role="alert">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p id="twitchClipId-help" class="mt-2 text-sm text-gray-400">
                        {{ __('clips.clip_id_help', ['example' => 'PluckyInventiveCarrotPastaThat']) }}
                    </p>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i wire:loading class="fas fa-spinner fa-spin mr-2"></i>
                        <i wire:loading.remove class="fas fa-search mr-2"></i>
                        <span wire:loading.remove>{{ __('clips.check_clip_button') }}</span>
                        <span wire:loading>{{ __('clips.checking_button') }}</span>
                    </button>

                    <div class="text-sm text-gray-400 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-green-400"></i>
                        {{ __('clips.secure_private') }}
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="mt-8 bg-gray-900 rounded-lg p-6 border border-gray-800">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-question-circle text-purple-400"></i>
                {{ __('clips.help_title') }}
            </h3>
            <ol class="space-y-3 text-gray-300">
                <li class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-purple-600 text-white text-xs rounded-full flex items-center justify-center font-medium">1</span>
                    <span>{{ __('clips.help_step_1', ['example_url' => 'https://clips.twitch.tv/PluckyInventiveCarrotPastaThat']) }}</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-purple-600 text-white text-xs rounded-full flex items-center justify-center font-medium">2</span>
                    <span>{{ __('clips.help_step_2', ['example_id' => 'PluckyInventiveCarrotPastaThat']) }}</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-purple-600 text-white text-xs rounded-full flex items-center justify-center font-medium">3</span>
                    <span>{{ __('clips.help_step_3') }}</span>
                </li>
            </ol>
        </div>
        @endif

        <!-- Step 2: Clip Info Display -->
        @if($clipInfo && !$showPlayer)
        <div class="space-y-6">
            <!-- Clip Info Card -->
            <div class="bg-gray-900 rounded-lg p-6 border border-gray-800">
                <h3 class="text-lg font-semibold text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-info-circle text-purple-400"></i>
                    {{ __('clips.clip_info_title') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-800 rounded-md p-4 border border-gray-700">
                        <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('clips.title_label') }}</label>
                        <p class="text-white font-medium">{{ $clipInfo['title'] }}</p>
                    </div>
                    <div class="bg-gray-800 rounded-md p-4 border border-gray-700">
                        <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('clips.broadcaster_label') }}</label>
                        <p class="text-white font-medium">{{ $clipInfo['broadcasterName'] }}</p>
                    </div>
                    <div class="bg-gray-800 rounded-md p-4 border border-gray-700">
                        <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('clips.view_count_label') }}</label>
                        <p class="text-white font-medium">{{ number_format($clipInfo['viewCount']) }}</p>
                    </div>
                    <div class="bg-gray-800 rounded-md p-4 border border-gray-700">
                        <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('clips.duration_label') }}</label>
                        <p class="text-white font-medium">{{ round($clipInfo['duration'], 1) }}s</p>
                    </div>
                    <div class="bg-gray-800 rounded-md p-4 border border-gray-700 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('clips.created_at_label') }}</label>
                        <p class="text-white font-medium">{{ \Carbon\Carbon::parse($clipInfo['createdAt'])->format('M j, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- GDPR Warning -->
            <div class="bg-yellow-900/20 border border-yellow-600/30 rounded-lg p-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
                    <div class="flex-1">
                        <h4 class="text-yellow-400 font-medium mb-3">{{ __('clips.gdpr_warning') }}</h4>
                        <p class="text-sm text-gray-300 mb-4">{{ __('clips.gdpr_explanation') }}</p>
                        <div class="flex gap-3">
                            <button
                                wire:click="confirmAndSubmit"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 focus:ring-offset-gray-950 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <i wire:loading class="fas fa-spinner fa-spin mr-2"></i>
                                <i wire:loading.remove class="fas fa-paper-plane mr-2"></i>
                                <span wire:loading.remove>{{ __('clips.submit_clip_button') }}</span>
                                <span wire:loading>{{ __('clips.submitting_button') }}</span>
                            </button>
                            <button
                                wire:click="loadPlayer"
                                class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 focus:ring-offset-gray-950"
                            >
                                <i class="fas fa-play mr-2"></i>
                                {{ __('clips.load_player_button') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-start">
                <button
                    wire:click="resetClip"
                    class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 hover:text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-gray-950"
                >
                    <i class="fas fa-arrow-left mr-2"></i>
                    {{ __('clips.reset_button') }}
                </button>
            </div>
        </div>
        @endif

        <!-- Step 3: Player and Submit -->
        @if($showPlayer)
        <div class="space-y-6">
            <!-- Player Card -->
            <div class="bg-gray-900 rounded-lg p-6 border border-gray-800">
                <h3 class="text-lg font-semibold text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-play-circle text-purple-400"></i>
                    {{ __('clips.clip_preview') }}
                </h3>
                <div class="aspect-video bg-gray-800 rounded-md overflow-hidden">
                    <iframe
                        src="{{ $clipInfo['embedUrl'] }}&parent={{ request()->getHost() }}"
                        height="100%"
                        width="100%"
                        allowfullscreen
                        class="w-full h-full"
                        title="Twitch Clip Preview"
                    ></iframe>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-gray-900 rounded-lg p-6 border border-gray-800">
                <div class="flex items-center justify-between">
                    <button
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i wire:loading class="fas fa-spinner fa-spin mr-2"></i>
                        <i wire:loading.remove class="fas fa-paper-plane mr-2"></i>
                        <span wire:loading.remove>{{ __('clips.submit_clip_button') }}</span>
                        <span wire:loading>{{ __('clips.submitting_button') }}</span>
                    </button>

                    <button
                        wire:click="resetClip"
                        class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 hover:text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-gray-950"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>
                        {{ __('clips.reset_button') }}
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Success Message -->
        @if($successMessage)
        <div class="bg-green-900/20 border border-green-600/30 rounded-lg p-6" role="alert">
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-400 mt-1"></i>
                <div class="flex-1">
                    <h3 class="text-green-400 font-medium mb-1">{{ __('clips.success_title') }}</h3>
                    <p class="text-green-100">{{ $successMessage }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Error Message -->
        @if($errorMessage)
        <div class="bg-red-900/20 border border-red-600/30 rounded-lg p-6" role="alert">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-red-400 mt-1"></i>
                <div class="flex-1">
                    <h3 class="text-red-400 font-medium mb-1">{{ __('clips.error_title') }}</h3>
                    <p class="text-red-100">{{ $errorMessage }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>