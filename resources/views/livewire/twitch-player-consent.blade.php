<div>
    @if (!$showPlayer)
        <!-- Fake Player Placeholder with DSGVO Notice -->
        <div class="relative aspect-video bg-gray-900 rounded-md border border-gray-700 overflow-hidden cursor-pointer group"
             wire:click="loadPlayer"
             role="button"
             tabindex="0"
             aria-label="{{ __('clips.twitch_consent_load_button') }}">
            <!-- Background Image or Placeholder -->
            <div class="absolute inset-0 bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                <!-- Play Icon -->
                <div class="text-white opacity-70 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-play-circle text-5xl"></i>
                </div>
            </div>
            <!-- Overlay Content -->
            <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center bg-black/40 group-hover:bg-black/20 transition-colors">
                <h3 class="text-lg font-semibold text-white mb-2">{{ __('clips.twitch_consent_title') }}</h3>
                <p class="text-gray-300 text-sm mb-4">{{ __('clips.twitch_consent_description') }}</p>
                <!-- Privacy Notice -->
                <div class="bg-gray-800 bg-opacity-80 border border-gray-600 rounded-md p-3 mb-4 max-w-md">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-shield-alt text-purple-400 mr-2"></i>
                        <span class="text-purple-300 font-medium text-sm">{{ __('clips.twitch_consent_privacy_title') }}</span>
                    </div>
                    <p class="text-gray-300 text-xs">
                        {{ __('clips.twitch_consent_privacy_notice') }}
                    </p>
                </div>
                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-md text-sm font-medium transition-colors focus:outline-none flex items-center">
                        <i class="fas fa-play mr-2"></i>
                        {{ __('clips.twitch_consent_load_button') }}
                    </button>
                </div>
            </div>
        </div>
    @else
        <!-- Twitch Player Embed -->
        <div class="relative aspect-video rounded-md overflow-hidden border border-gray-700">
            <iframe
                src="https://clips.twitch.tv/embed?clip={{ $clipInfo['twitchClipId'] }}&parent={{ request()->getHost() }}"
                height="100%"
                width="100%"
                frameborder="0"
                scrolling="no"
                allowfullscreen="true"
                class="absolute inset-0 w-full h-full"
                aria-label="Twitch clip player"
            ></iframe>
        </div>
    @endif
</div>