<div>
    @if (!$showPlayer)
        <!-- Privacy-First Player Placeholder -->
        <div class="relative aspect-video bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-lg overflow-hidden shadow-2xl cursor-pointer group"
             wire:click="loadPlayer"
             role="button"
             tabindex="0"
             aria-label="{{ __('clips.twitch_consent_load_button') }}">
            
            <!-- Thumbnail Background -->
            @if(isset($clipInfo['localThumbnailPath']))
                <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30 group-hover:opacity-40 transition-opacity duration-300"
                     style="background-image: url('{{ Storage::url($clipInfo['localThumbnailPath']) }}');">
                </div>
            @endif

            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent"></div>

            <!-- Main Content -->
            <div class="relative h-full flex flex-col items-center justify-center p-8 text-center">

                <!-- Title -->
                <h3 class="text-2xl font-bold text-white mb-3 tracking-tight">
                    {{ __('clips.twitch_consent_title') }}
                </h3>

                <!-- Description -->
                <p class="text-gray-300 text-base mb-8 max-w-lg leading-relaxed">
                    {{ __('clips.twitch_consent_description') }}
                </p>

                <!-- Privacy Notice Card -->
                <div class="bg-white/5 dark:bg-white/10 backdrop-blur-sm border border-white/10 rounded-lg p-5 mb-8 max-w-lg group-hover:bg-white/10 transition-colors">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div class="text-left">
                            <h4 class="text-purple-300 font-semibold text-sm mb-1.5">
                                {{ __('clips.twitch_consent_privacy_title') }}
                            </h4>
                            <p class="text-gray-300/90 text-sm leading-relaxed">
                                {{ __('clips.twitch_consent_privacy_notice') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Twitch Player Embed -->
        <div class="relative aspect-video rounded-lg overflow-hidden shadow-2xl border border-gray-700/50">
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
