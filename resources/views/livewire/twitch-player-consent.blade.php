<div class="space-y-4">
    @if($showPlayer)
        <!-- Twitch Player Embed -->
        <div id="twitch-player" class="aspect-video bg-black rounded-md overflow-hidden">
            <!-- Twitch embed will be loaded here via JavaScript -->
            <div class="w-full h-full flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin text-white text-3xl mb-2" aria-hidden="true"></i>
                    <p class="text-white text-sm">{{ __('clips.loading_player') }}</p>
                </div>
            </div>
        </div>
    @else
        <!-- Thumbnail Preview (only show if clipInfo exists) -->
        @if($clipInfo)
            <div class="relative aspect-video bg-gray-800 rounded-md border border-gray-700 overflow-hidden group">
                <!-- Overlay -->
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-play-circle text-white text-3xl mb-2" aria-hidden="true"></i>
                        <p class="text-white text-sm font-medium">{{ __('clips.click_to_play') }}</p>
                    </div>
                </div>

                <!-- Duration Badge -->
                @if(isset($clipInfo['duration']))
                    <div class="absolute bottom-2 right-2 bg-black/75 text-white text-xs px-2 py-1 rounded">
                        {{ round($clipInfo['duration'], 1) }}s
                    </div>
                @endif
            </div>
        @endif

        <!-- Load Button -->
        <div class="text-center">
            <button
                wire:click="loadPlayer"
                wire:loading.attr="disabled"
                class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 focus:ring-offset-gray-950 disabled:opacity-50 disabled:cursor-not-allowed"
                aria-label="{{ __('clips.consent_button') }}"
            >
                <i wire:loading class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>
                <i wire:loading.remove class="fas fa-play-circle mr-2" aria-hidden="true"></i>
                <span wire:loading.remove>{{ __('clips.consent_button') }}</span>
                <span wire:loading>{{ __('clips.loading_player') }}</span>
            </button>
        </div>
    @endif
</div>