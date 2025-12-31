<div class="space-y-8">
    <!-- Progress Indicator -->
    <div class="mb-8">
        <div class="flex items-center justify-center">
            <div class="flex items-center space-x-4">
                <!-- Step 1 -->
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-md flex items-center justify-center transition-colors duration-200 {{ $clipInfo ? 'bg-green-600 text-white' : 'bg-purple-600 text-white' }}">
                        <i class="fas fa-search text-sm" aria-hidden="true"></i>
                    </div>
                    <span class="text-xs text-gray-400 mt-2">{{ __('clips.step_check') }}</span>
                </div>

                <!-- Connector -->
                <div class="w-12 h-0.5 transition-colors duration-200 {{ $clipInfo ? 'bg-green-500' : 'bg-gray-600' }}"></div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-md flex items-center justify-center transition-colors duration-200 {{ $clipInfo && !$showPlayer ? 'bg-purple-600 text-white' : ($showPlayer ? 'bg-green-600 text-white' : 'bg-gray-600 text-gray-400') }}">
                        <i class="fas fa-info-circle text-sm" aria-hidden="true"></i>
                    </div>
                    <span class="text-xs text-gray-400 mt-2">{{ __('clips.step_info') }}</span>
                </div>

                <!-- Connector -->
                <div class="w-12 h-0.5 transition-colors duration-200 {{ $showPlayer ? 'bg-green-500' : 'bg-gray-600' }}"></div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-md flex items-center justify-center transition-colors duration-200 {{ $showPlayer ? 'bg-purple-600 text-white' : 'bg-gray-600 text-gray-400' }}">
                        <i class="fas fa-play-circle text-sm" aria-hidden="true"></i>
                    </div>
                    <span class="text-xs text-gray-400 mt-2">{{ __('clips.step_submit') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Input & Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Step 1: Clip ID Input -->
            @if(!$clipInfo)
                <div class="bg-gray-900 rounded-md p-6 border border-gray-800">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-purple-600 rounded-md flex items-center justify-center">
                            <i class="fas fa-link text-white text-sm" aria-hidden="true"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-white">{{ __('clips.clip_id_label') }}</h2>
                    </div>

                    <form wire:submit.prevent="checkClip" class="space-y-4">
                        <div class="space-y-3">
                            <div class="relative">
                                <input
                                    type="text"
                                    id="twitchClipId"
                                    wire:model="twitchClipId"
                                    placeholder="{{ __('clips.clip_id_placeholder') }}"
                                    class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-md text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                                    autocomplete="off"
                                    aria-describedby="twitchClipId-help"
                                >
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-search text-gray-400" aria-hidden="true"></i>
                                </div>
                            </div>

                            @error('twitchClipId')
                                <div class="flex items-center gap-2 text-red-400 bg-red-900/50 border border-red-700 rounded-md p-3">
                                    <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror

                            <p id="twitchClipId-help" class="text-gray-400 text-sm">
                                {{ __('clips.clip_id_help', ['example' => 'PluckyInventiveCarrotPastaThat']) }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed"
                                aria-label="{{ __('clips.check_clip_button') }}"
                            >
                                <i wire:loading class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>
                                <i wire:loading.remove class="fas fa-search mr-2" aria-hidden="true"></i>
                                <span wire:loading.remove>{{ __('clips.check_clip_button') }}</span>
                                <span wire:loading>{{ __('clips.checking_button') }}</span>
                            </button>

                            <div class="flex items-center gap-2 text-gray-400">
                                <i class="fas fa-shield-alt text-green-400" aria-hidden="true"></i>
                                <span class="text-sm">{{ __('clips.secure_private') }}</span>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Help Card -->
                <div class="bg-gray-900 rounded-md p-6 border border-gray-800">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-blue-600 rounded-md flex items-center justify-center">
                            <i class="fas fa-question-circle text-white text-sm" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white">{{ __('clips.help_title') }}</h3>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-start gap-3 p-3 bg-gray-800 rounded-md border border-gray-700">
                            <div class="w-6 h-6 bg-purple-600 rounded flex items-center justify-center text-white font-medium text-xs flex-shrink-0">1</div>
                            <div class="flex-1">
                                <p class="text-gray-300 text-sm">{{ __('clips.help_step_1', ['example_url' => 'https://clips.twitch.tv/PluckyInventiveCarrotPastaThat']) }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 bg-gray-800 rounded-md border border-gray-700">
                            <div class="w-6 h-6 bg-purple-600 rounded flex items-center justify-center text-white font-medium text-xs flex-shrink-0">2</div>
                            <div class="flex-1">
                                <p class="text-gray-300 text-sm">{{ __('clips.help_step_2', ['example_id' => 'PluckyInventiveCarrotPastaThat']) }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 bg-gray-800 rounded-md border border-gray-700">
                            <div class="w-6 h-6 bg-purple-600 rounded flex items-center justify-center text-white font-medium text-xs flex-shrink-0">3</div>
                            <div class="flex-1">
                                <p class="text-gray-300 text-sm">{{ __('clips.help_step_3') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 2: Clip Info Display -->
            @if($clipInfo)
                <div class="space-y-6">
                    <!-- Clip Info Card -->
                    <div class="bg-gray-900 rounded-md p-6 border border-gray-800">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 bg-green-600 rounded-md flex items-center justify-center">
                                <i class="fas fa-info-circle text-white text-sm" aria-hidden="true"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-white">{{ __('clips.clip_info_title') }}</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-800 rounded-md p-4 border border-gray-700">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fas fa-heading text-purple-400 text-sm" aria-hidden="true"></i>
                                    <label class="text-xs font-medium text-gray-400 uppercase">{{ __('clips.title_label') }}</label>
                                </div>
                                <p class="text-white font-medium text-sm">{{ $clipInfo['title'] }}</p>
                            </div>

                            <div class="bg-gray-800 rounded-md p-4 border border-gray-700">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fas fa-user text-purple-400 text-sm" aria-hidden="true"></i>
                                    <label class="text-xs font-medium text-gray-400 uppercase">{{ __('clips.broadcaster_label') }}</label>
                                </div>
                                <p class="text-white font-medium text-sm">{{ $clipInfo['broadcasterName'] }}</p>
                            </div>

                            <div class="bg-gray-800 rounded-md p-4 border border-gray-700">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fas fa-eye text-purple-400 text-sm" aria-hidden="true"></i>
                                    <label class="text-xs font-medium text-gray-400 uppercase">{{ __('clips.view_count_label') }}</label>
                                </div>
                                <p class="text-white font-medium text-sm">{{ number_format($clipInfo['viewCount']) }}</p>
                            </div>

                            <div class="bg-gray-800 rounded-md p-4 border border-gray-700">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fas fa-clock text-purple-400 text-sm" aria-hidden="true"></i>
                                    <label class="text-xs font-medium text-gray-400 uppercase">{{ __('clips.duration_label') }}</label>
                                </div>
                                <p class="text-white font-medium text-sm">{{ round($clipInfo['duration'], 1) }}s</p>
                            </div>

                            <div class="bg-gray-800 rounded-md p-4 border border-gray-700 md:col-span-2">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fas fa-calendar text-purple-400 text-sm" aria-hidden="true"></i>
                                    <label class="text-xs font-medium text-gray-400 uppercase">{{ __('clips.created_at_label') }}</label>
                                </div>
                                <p class="text-white font-medium text-sm">{{ \Carbon\Carbon::parse($clipInfo['createdAt'])->format('M j, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-gray-900 rounded-md p-6 border border-gray-800">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <button
                                wire:click="submit"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed"
                                aria-label="{{ __('clips.submit_clip_button') }}"
                            >
                                <i wire:loading class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>
                                <i wire:loading.remove class="fas fa-paper-plane mr-2" aria-hidden="true"></i>
                                <span wire:loading.remove>{{ __('clips.submit_clip_button') }}</span>
                                <span wire:loading>{{ __('clips.submitting_button') }}</span>
                            </button>

                            <div class="flex items-center gap-2 text-gray-400">
                                <i class="fas fa-info-circle text-blue-400" aria-hidden="true"></i>
                                <span class="text-sm">{{ __('clips.submit_info') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Optional Player Preview -->
                    <div class="bg-gray-900 rounded-md p-6 border border-gray-800">
                        <div class="text-center">
                            <livewire:twitch-player-consent :clip-info="$clipInfo" />
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Features -->
        <div class="space-y-6">
            <!-- Feature Cards -->
            <div class="bg-gray-900 rounded-md p-6 border border-gray-800">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-star text-yellow-400" aria-hidden="true"></i>
                    {{ __('clips.features_title') }}
                </h3>

                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 bg-gray-800 rounded-md border border-gray-700">
                        <i class="fas fa-shield-alt text-green-400 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                        <div>
                            <h4 class="font-medium text-white text-sm">{{ __('clips.feature_secure_title') }}</h4>
                            <p class="text-gray-400 text-xs">{{ __('clips.feature_secure_description') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 bg-gray-800 rounded-md border border-gray-700">
                        <i class="fas fa-rocket text-blue-400 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                        <div>
                            <h4 class="font-medium text-white text-sm">{{ __('clips.feature_fast_title') }}</h4>
                            <p class="text-gray-400 text-xs">{{ __('clips.feature_fast_description') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 bg-gray-800 rounded-md border border-gray-700">
                        <i class="fas fa-users text-purple-400 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                        <div>
                            <h4 class="font-medium text-white text-sm">{{ __('clips.feature_community_title') }}</h4>
                            <p class="text-gray-400 text-xs">{{ __('clips.feature_community_description') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if($successMessage)
        <div class="mt-6 bg-green-900/50 border border-green-700 rounded-md p-4" role="alert">
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-green-600 rounded flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-white text-sm" aria-hidden="true"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-green-400 font-medium mb-1">{{ __('clips.success_title') }}</h3>
                    <p class="text-green-200 text-sm">{{ $successMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Message -->
    @if($errorMessage)
        <div class="mt-6 bg-red-900/50 border border-red-700 rounded-md p-4" role="alert">
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-red-600 rounded flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-white text-sm" aria-hidden="true"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-red-400 font-medium mb-1">{{ __('clips.error_title') }}</h3>
                    <p class="text-red-200 text-sm">{{ $errorMessage }}</p>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function loadTwitchPlayer() {
    const iframe = document.getElementById('twitch-player-iframe');
    const playButton = iframe.previousElementSibling.querySelector('button');
    const overlayText = iframe.nextElementSibling;

    if (iframe && playButton && overlayText) {
        // Hide play button and overlay text
        playButton.style.display = 'none';
        overlayText.style.display = 'none';

        // Show iframe with fade-in effect
        iframe.style.opacity = '1';
    }
}
</script>