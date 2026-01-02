<div class="max-w-4xl mx-auto space-y-16">
    <!-- Page Header -->
    <div class="text-center space-y-6">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-zinc-900 border-2 border-violet-500 rounded-2xl">
            <i class="fa-solid fa-upload text-3xl text-violet-400"></i>
        </div>
        <div class="space-y-4">
            <h1 class="text-4xl sm:text-5xl font-bold text-zinc-100">{{ __('clips.submit_page_title') }}</h1>
            <p class="text-xl text-zinc-400 max-w-2xl mx-auto">{{ __('clips.submit_page_subtitle') }}</p>
        </div>
    </div>

    <!-- Step 1: Clip URL/ID eingeben -->
    @if(!$clipInfo)
        <div class="relative bg-zinc-900 border border-zinc-800 hover:border-zinc-700 rounded-xl overflow-hidden transition-all duration-200">
            <!-- Accent Border -->
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-violet-500 opacity-0 hover:opacity-100 transition-opacity duration-200"></div>

            <div class="p-8">
                <div class="flex flex-col items-center gap-6 mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-zinc-900 border-2 border-violet-500 rounded-xl">
                        <i class="fa-solid fa-link text-2xl text-violet-400"></i>
                    </div>
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-zinc-100 mb-2">{{ __('clips.clip_id_label') }}</h2>
                        <p class="text-lg text-zinc-400">{{ __('clips.clip_id_description') }}</p>
                    </div>
                </div>

                <form wire:submit.prevent="checkClip" class="space-y-6">
                    <div>
                        <input
                            type="text"
                            id="twitchClipId"
                            wire:model="twitchClipId"
                            placeholder="{{ __('clips.clip_id_placeholder') }}"
                            class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:border-violet-500 focus:outline-none transition-colors text-lg"
                            autocomplete="off"
                        >

                        @error('twitchClipId')
                            <p class="mt-3 text-sm text-red-400">{{ $message }}</p>
                        @enderror

                        <p class="mt-3 text-sm text-zinc-500">
                            {{ __('clips.clip_id_help', ['example' => 'PluckyInventiveCarrotPastaThat']) }}
                        </p>
                    </div>

                    <div class="flex justify-center pt-4">
                        <x-ui.button
                            type="submit"
                            wire:loading.attr="disabled"
                            variant="primary"
                            size="lg"
                            class="px-8"
                            :loading="false"
                        >
                            <span wire:loading.remove>{{ __('clips.check_clip_button') }}</span>
                            <span wire:loading>
                                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                                {{ __('clips.checking_button') }}
                            </span>
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Hilfe-Info -->
        <div class="relative bg-zinc-900 border border-zinc-800 hover:border-zinc-700 rounded-xl overflow-hidden transition-all duration-200">
            <!-- Accent Border -->
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-violet-500 opacity-0 hover:opacity-100 transition-opacity duration-200"></div>

            <div class="p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-zinc-900 border-2 border-violet-500 rounded-lg">
                        <i class="fa-solid fa-question text-xl text-violet-400"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-zinc-100">{{ __('clips.help_title') }}</h3>
                        <p class="text-zinc-400">{{ __('clips.help_subtitle') }}</p>
                    </div>
                </div>

                <div class="space-y-4 text-zinc-300">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-violet-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-xs font-bold text-zinc-900">1</span>
                        </div>
                        <p>{{ __('clips.help_step_1', ['example_url' => 'https://clips.twitch.tv/PluckyInventiveCarrotPastaThat']) }}</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-violet-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-xs font-bold text-zinc-900">2</span>
                        </div>
                        <p>{{ __('clips.help_step_2', ['example_id' => 'PluckyInventiveCarrotPastaThat']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Step 2: Clip-Info & Preview -->
    @if($clipInfo)
        <!-- Clip-Informationen -->
        <div class="relative bg-zinc-900 border border-zinc-800 hover:border-zinc-700 rounded-xl overflow-hidden transition-all duration-200">
            <!-- Accent Border -->
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-violet-500 opacity-0 hover:opacity-100 transition-opacity duration-200"></div>

            <div class="p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-zinc-900 border-2 border-violet-500 rounded-xl">
                        <i class="fa-solid fa-info text-2xl text-violet-400"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-zinc-100">{{ __('clips.clip_info_title') }}</h2>
                        <p class="text-zinc-400">{{ __('clips.clip_info_subtitle') }}</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-zinc-800/50 rounded-lg p-6">
                        <div class="text-sm text-zinc-500 uppercase mb-2">{{ __('clips.title_label') }}</div>
                        <div class="text-xl text-zinc-100 font-medium">{{ e($clipInfo['title']) }}</div>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-zinc-800/50 rounded-lg p-4">
                            <div class="text-xs text-zinc-500 uppercase mb-2">{{ __('clips.broadcaster_label') }}</div>
                            <div class="text-zinc-100 font-medium">{{ e($clipInfo['broadcasterName']) }}</div>
                        </div>

                        <div class="bg-zinc-800/50 rounded-lg p-4">
                            <div class="text-xs text-zinc-500 uppercase mb-2">{{ __('clips.view_count_label') }}</div>
                            <div class="text-zinc-100 font-medium">{{ number_format($clipInfo['viewCount']) }}</div>
                        </div>

                        <div class="bg-zinc-800/50 rounded-lg p-4">
                            <div class="text-xs text-zinc-500 uppercase mb-2">{{ __('clips.duration_label') }}</div>
                            <div class="text-zinc-100 font-medium">{{ round($clipInfo['duration'], 1) }}s</div>
                        </div>

                        <div class="bg-zinc-800/50 rounded-lg p-4">
                            <div class="text-xs text-zinc-500 uppercase mb-2">{{ __('clips.created_at_label') }}</div>
                            <div class="text-zinc-100 font-medium">{{ \Carbon\Carbon::parse($clipInfo['createdAt'])->format('M j, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clip-Vorschau -->
        <div class="relative bg-zinc-900 border border-zinc-800 hover:border-zinc-700 rounded-xl overflow-hidden transition-all duration-200">
            <!-- Accent Border -->
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-violet-500 opacity-0 hover:opacity-100 transition-opacity duration-200"></div>

            <div class="p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-zinc-900 border-2 border-violet-500 rounded-xl">
                        <i class="fa-solid fa-play text-2xl text-violet-400"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-zinc-100">{{ __('clips.clip_preview_title') }}</h3>
                        <p class="text-zinc-400">{{ __('clips.clip_preview_subtitle') }}</p>
                    </div>
                </div>

                <livewire:twitch-player-consent :clip-info="$clipInfo" />
            </div>
        </div>

        <!-- Submit Actions -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <x-ui.button
                wire:click="submit"
                wire:loading.attr="disabled"
                variant="success"
                size="lg"
                class="px-8"
                :loading="false"
            >
                <span wire:loading.remove>
                    <i class="fa-solid fa-paper-plane mr-2"></i>
                    {{ __('clips.submit_clip_button') }}
                </span>
                <span wire:loading>
                    <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                    {{ __('clips.submitting_button') }}
                </span>
            </x-ui.button>

            <x-ui.button
                wire:click="$set('clipInfo', null)"
                variant="secondary"
                size="lg"
                class="px-8"
            >
                <i class="fa-solid fa-arrow-left mr-2"></i>
                {{ __('clips.cancel_button') }}
            </x-ui.button>
        </div>
    @endif

    <!-- Messages -->
    @if($successMessage)
        <div class="relative bg-green-900/20 border border-green-700 rounded-xl overflow-hidden p-6">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500"></div>
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-check-circle text-green-400 text-xl"></i>
                <p class="text-green-200 font-medium">{{ e($successMessage) }}</p>
            </div>
        </div>
    @endif

    @if($errorMessage)
        <div class="relative bg-red-900/20 border border-red-700 rounded-xl overflow-hidden p-6">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500"></div>
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-exclamation-triangle text-red-400 text-xl"></i>
                <p class="text-red-200 font-medium">{{ $errorMessage }}</p>
            </div>
        </div>
    @endif
</div>