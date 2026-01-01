<x-layouts.app title="{{ $clip->title }}">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-zinc-950">
        <div class="max-w-6xl mx-auto space-y-8">
            <!-- Clip Player -->
            <div class="bg-zinc-900 rounded-lg border border-zinc-800 overflow-hidden shadow-2xl">
                <div class="aspect-video bg-zinc-800">
                    <livewire:twitch-player-consent :clip-info="['twitchClipId' => $clip->twitch_clip_id, 'localThumbnailPath' => $clip->local_thumbnail_path]" />
                </div>
            </div>

            <!-- Clip Info -->
            <div class="bg-zinc-900 rounded-lg border border-zinc-800 p-8 shadow-xl">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 mb-6">
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-zinc-100 mb-4 leading-tight">{{ $clip->title }}</h1>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-zinc-400 mb-4">
                            <a href="{{ route('clips.list', ['broadcaster' => $clip->broadcaster->twitch_login]) }}" class="flex items-center gap-2 hover:text-violet-400 transition-colors">
                                <i class="fa-solid fa-user"></i>
                                {{ $clip->broadcaster->twitch_display_name }}
                            </a>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-eye"></i>
                                {{ __('clips.views_count', ['count' => number_format($clip->view_count)]) }}
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-calendar"></i>
                                {{ $clip->created_at_twitch->format('M j, Y') }}
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-clock"></i>
                                {{ __('clips.duration_seconds', ['seconds' => number_format($clip->duration, 2)]) }}
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <button
                            x-data="{ copied: false }"
                            x-on:click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)"
                            class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-zinc-100 rounded-lg transition-colors border border-zinc-700"
                        >
                            <i class="fa-solid fa-share-nodes mr-2"></i>
                            <span x-text="copied ? 'Copied!' : '{{ __('clips.share') }}'"></span>
                        </button>
                    </div>
                </div>

                <!-- Description -->
                @if ($clip->description)
                    <div class="border-t border-zinc-800 pt-6 mb-6">
                        <p class="text-zinc-300 leading-relaxed text-lg">
                            {{ $clip->description }}
                        </p>
                    </div>
                @endif

                <!-- Tags/Meta -->
                <div class="border-t border-zinc-800 pt-6">
                    <div class="flex flex-wrap gap-3">
                        @if ($clip->game)
                            <x-ui.button
                                :href="route('games.view', $clip->game->id)"
                                variant="outline"
                                size="sm"
                                icon="gamepad"
                            >
                                {{ $clip->game->name }}
                            </x-ui.button>
                        @endif
                        @if ($clip->submitter)
                            <span class="px-3 py-1.5 bg-zinc-800 border border-zinc-700 text-zinc-400 rounded-lg text-sm">
                                <i class="fa-solid fa-user-pen mr-2"></i>
                                {{ __('clips.submitted_by_label') }}: {{ $clip->submitter->twitch_login }}
                            </span>
                        @endif
                        <span class="px-3 py-1.5 bg-zinc-800 border border-zinc-700 text-zinc-400 rounded-lg text-sm">
                            <i class="fa-solid fa-scissors mr-2"></i>
                            {{ __('clips.created_by_label') }}: {{ $clip->clip_creator_name }}
                        </span>
                        <span class="px-3 py-1.5 bg-zinc-800 border border-zinc-700 text-zinc-400 rounded-lg text-sm">
                            <i class="fa-solid fa-plus mr-2"></i>
                            {{ __('clips.added_on_label', ['date' => $clip->created_at->format('M j, Y')]) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Rating & Actions -->
            @auth
                <livewire:clips.clip-rating :clip="$clip" :key="'rating-'.$clip->id" />
            @endauth

            <!-- Comments Section -->
            <livewire:clips.clip-comments :clip="$clip" :key="'comments-'.$clip->id" />

            <!-- Related Clips -->
            @if ($relatedClips->isNotEmpty())
                <div class="bg-zinc-900 rounded-lg border border-zinc-800 p-8 shadow-xl">
                    <x-ui.section-header
                        :title="__('clips.related_clips')"
                        icon="film"
                        class="mb-8"
                    />
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
