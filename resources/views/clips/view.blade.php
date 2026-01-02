<x-layouts.app title="{{ $clip->title }}">
    <div class="min-h-screen bg-zinc-950">
        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <!-- Background with subtle gradient -->
            <div
                class="absolute inset-0 bg-gradient-to-br from-(--color-accent-500)/10 via-transparent to-(--color-accent-600)/5">
            </div>

            <!-- Hero Content -->
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                    <!-- Clip Player - Large -->
                    <div class="lg:col-span-2">
                        <div>
                            <h1 class="text-zinc-300 text-2xl font-medium mb-4 flex items-center gap-2">
                                <div class="w-1 h-6 bg-(--color-accent-500) rounded-full"></div>
                                {{ $clip->title }}
                            </h1>
                        </div>
                        <div class="relative group">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-(--color-accent-500) to-(--color-accent-600) rounded-2xl blur-lg opacity-20 group-hover:opacity-30 transition-opacity">
                            </div>

                            <div
                                class="relative bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden shadow-2xl">
                                <div class="aspect-video bg-zinc-800">
                                    <livewire:twitch-player-consent :clip-info="[
                                        'twitchClipId' => $clip->twitch_clip_id,
                                        'localThumbnailPath' => $clip->local_thumbnail_path,
                                    ]" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clip Info Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Broadcaster Info -->
                        <div class="bg-zinc-900/80 backdrop-blur-sm border border-zinc-800/50 rounded-xl p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="w-12 h-12 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-xl flex items-center justify-center">
                                    <i class="fa-solid fa-user text-(--color-accent-400) text-lg"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-zinc-500 uppercase tracking-wide font-medium">
                                        {{ __('clips.streamer') }}</div>
                                    <a href="{{ route('clips.list', ['broadcaster' => $clip->broadcaster->twitch_login]) }}"
                                        class="text-lg font-semibold text-zinc-100 hover:text-(--color-accent-400) transition-colors">
                                        {{ $clip->broadcaster->twitch_display_name }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <div
                                class="bg-zinc-900/80 backdrop-blur-sm border border-zinc-800/50 rounded-xl p-4 text-center group hover:border-(--color-accent-500)/30 transition-colors">
                                <div
                                    class="text-2xl font-bold text-(--color-accent-400) mb-1 group-hover:text-(--color-accent-300) transition-colors">
                                    {{ number_format($clip->view_count) }}
                                </div>
                                <div class="text-xs text-zinc-500 uppercase tracking-wide font-medium">
                                    {{ __('clips.views') }}
                                </div>
                            </div>

                            <div
                                class="bg-zinc-900/80 backdrop-blur-sm border border-zinc-800/50 rounded-xl p-4 text-center group hover:border-(--color-accent-500)/30 transition-colors">
                                <div
                                    class="text-2xl font-bold text-(--color-accent-400) mb-1 group-hover:text-(--color-accent-300) transition-colors">
                                    {{ number_format($clip->duration, 1) }}s
                                </div>
                                <div class="text-xs text-zinc-500 uppercase tracking-wide font-medium">
                                    {{ __('clips.duration') }}
                                </div>
                            </div>
                        </div>

                        <!-- Date Info -->
                        <div class="bg-zinc-900/80 backdrop-blur-sm border border-zinc-800/50 rounded-xl p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-zinc-800/50 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-calendar text-zinc-400 text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-zinc-500 uppercase tracking-wide font-medium">
                                        {{ __('clips.created') }}</div>
                                    <div class="text-zinc-300">{{ $clip->created_at_twitch->format('M j, Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Decorative Elements -->
            <div class="absolute top-1/3 left-10 w-24 h-24 bg-(--color-accent-500)/5 rounded-full blur-2xl"></div>
            <div class="absolute bottom-1/3 right-10 w-32 h-32 bg-(--color-accent-600)/5 rounded-full blur-2xl"></div>
        </div>

        <div class="accent-border-divider-large"></div>

        <!-- Content Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
            <!-- Description Section -->
            @if ($clip->description)
                <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800/50 rounded-2xl p-8 lg:p-12">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-12 h-12 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-quote-left text-(--color-accent-400) text-lg"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-zinc-100">{{ __('clips.description') }}</h2>
                    </div>

                    <div class="accent-border-divider-medium"></div>

                    <div class="prose prose-invert max-w-none">
                        <p class="text-zinc-300 leading-relaxed text-lg">
                            {{ $clip->description }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Tags & Meta Section -->
            <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800/50 rounded-2xl p-8 lg:p-12">
                <div class="flex items-center gap-4 mb-6">
                    <div
                        class="w-12 h-12 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-tags text-(--color-accent-400) text-lg"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-zinc-100">{{ __('clips.details') }}</h2>
                </div>

                <div class="accent-border-divider-medium"></div>

                <div class="flex flex-wrap gap-3">
                    @if ($clip->game)
                        <x-ui.button :href="route('games.view', $clip->game->id)" variant="outline" size="lg" icon="gamepad">
                            {{ $clip->game->name }}
                        </x-ui.button>
                    @endif

                    @if ($clip->submitter)
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-800/50 border border-zinc-700/50 text-zinc-400 rounded-xl text-sm hover:border-(--color-accent-500)/30 transition-colors">
                            <i class="fa-solid fa-user-pen text-(--color-accent-400)"></i>
                            <span class="font-medium">{{ __('clips.submitted_by_label') }}:</span>
                            <span>{{ $clip->submitter->twitch_login }}</span>
                        </div>
                    @endif

                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-800/50 border border-zinc-700/50 text-zinc-400 rounded-xl text-sm hover:border-(--color-accent-500)/30 transition-colors">
                        <i class="fa-solid fa-scissors text-(--color-accent-400)"></i>
                        <span class="font-medium">{{ __('clips.created_by_label') }}:</span>
                        <span>{{ $clip->clip_creator_name }}</span>
                    </div>

                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-800/50 border border-zinc-700/50 text-zinc-400 rounded-xl text-sm hover:border-(--color-accent-500)/30 transition-colors">
                        <i class="fa-solid fa-plus text-(--color-accent-400)"></i>
                        <span
                            class="font-medium">{{ __('clips.added_on_label', ['date' => $clip->created_at->format('M j, Y')]) }}</span>
                    </div>

                    <div class="flex items-center gap-3 mb-6">
                        <x-ui.button x-data="{ copied: false }"
                            x-on:click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)"
                            variant="secondary" size="md" icon="share-nodes">
                            <span x-text="copied ? '{{ __('clips.copied') }}' : '{{ __('clips.share') }}'"></span>
                        </x-ui.button>
                    </div>
                </div>
            </div>

            <!-- Rating Section -->
            @auth
                <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800/50 rounded-2xl p-8 lg:p-12">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-12 h-12 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-star text-(--color-accent-400) text-lg"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-zinc-100">{{ __('clips.rating') }}</h2>
                    </div>

                    <div class="accent-border-divider-medium"></div>

                    <livewire:clips.clip-rating :clip="$clip" :key="'rating-' . $clip->id" />
                </div>
            @endauth

            <!-- Comments Section -->
            <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800/50 rounded-2xl p-8 lg:p-12">
                <div class="flex items-center gap-4 mb-6">
                    <div
                        class="w-12 h-12 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-comments text-(--color-accent-400) text-lg"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-zinc-100">{{ __('clips.comments') }}</h2>
                </div>

                <div class="accent-border-divider-medium"></div>

                <livewire:clips.clip-comments :clip="$clip" :key="'comments-' . $clip->id" />
            </div>

            <!-- Related Clips -->
            @if ($relatedClips->isNotEmpty())
                <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800/50 rounded-2xl p-8 lg:p-12">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-video text-(--color-accent-400) text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-zinc-100">{{ __('clips.related_clips') }}</h2>
                                <p class="text-zinc-400 text-sm">{{ __('clips.related_clips_subtitle') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="accent-border-divider-medium"></div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($relatedClips as $relatedClip)
                            <x-ui.clip-card :clip="$relatedClip" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
