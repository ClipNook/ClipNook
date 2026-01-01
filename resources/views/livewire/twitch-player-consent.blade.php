<div>
    @if (!$showPlayer)
        <!-- Privacy-First Player Placeholder -->
        <div class="relative aspect-video bg-zinc-900 rounded-lg overflow-hidden shadow-2xl cursor-pointer group"
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

            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/80"></div>

            <!-- Main Content -->
            <div class="relative h-full flex flex-col items-center justify-center p-8 text-center">

                <!-- Title -->
                <h3 class="text-2xl font-bold text-white mb-3 tracking-tight">
                    {{ __('clips.twitch_consent_title') }}
                </h3>

                <!-- Description -->
                <p class="text-zinc-300 text-base mb-8 max-w-lg leading-relaxed">
                    {{ __('clips.twitch_consent_description') }}
                </p>

                <!-- Privacy Notice Card -->
                <div class="bg-zinc-800/50 backdrop-blur-sm border border-zinc-700 rounded-lg p-5 mb-8 max-w-lg group-hover:bg-zinc-700/50 transition-colors">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-shield-halved w-5 h-5 text-violet-400"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="text-violet-300 font-semibold text-sm mb-1.5">
                                {{ __('clips.twitch_consent_privacy_title') }}
                            </h4>
                            <p class="text-zinc-300/90 text-sm leading-relaxed">
                                {{ __('clips.twitch_consent_privacy_notice') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Twitch Player Embed -->
        <div class="relative aspect-video rounded-lg overflow-hidden shadow-2xl border border-zinc-700/50">
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
