<x-layouts.app title="{{ $game->name }} - {{ __('games.view_page_title') }}">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-neutral-950">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Game Header -->
            <div class="bg-neutral-900 rounded-lg border border-neutral-800 overflow-hidden shadow-xl">
                <div class="relative h-64 bg-gradient-to-br from-neutral-800 to-neutral-900">
                    <div class="absolute inset-0 bg-black/40"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        @if($game->local_box_art_path)
                            <div class="w-40 h-56 bg-neutral-800 rounded-lg border-2 border-neutral-700 overflow-hidden shadow-2xl">
                                <img
                                    src="{{ Storage::url($game->local_box_art_path) }}"
                                    alt="{{ $game->name }}"
                                    class="w-full h-full object-cover"
                                >
                            </div>
                        @else
                            <div class="w-40 h-56 bg-neutral-800 rounded-lg border-2 border-neutral-700 flex items-center justify-center shadow-2xl">
                                <i class="fa-solid fa-gamepad text-neutral-600 text-5xl"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="p-8 text-center">
                    <h1 class="text-3xl font-bold text-neutral-100 mb-4">{{ $game->name }}</h1>
                    <div class="flex flex-wrap justify-center gap-4 text-sm">
                        <div class="px-4 py-2 bg-neutral-800 border border-neutral-700 text-neutral-300 rounded-lg">
                            <i class="fa-solid fa-video mr-2 text-purple-400"></i>
                            {{ number_format($clipsCount) }} {{ Str::plural('clip', $clipsCount) }}
                        </div>
                        <div class="px-4 py-2 bg-neutral-800 border border-neutral-700 text-neutral-300 rounded-lg">
                            <i class="fa-solid fa-users mr-2 text-blue-400"></i>
                            {{ number_format($streamersCount) }} {{ Str::plural('streamer', $streamersCount) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clips Section -->
            <div class="bg-neutral-900 rounded-lg border border-neutral-800 shadow-xl p-8">
                <x-ui.section-header
                    :title="__('games.clips_section')"
                    icon="video"
                    class="mb-8"
                />

                @if($game->clips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($game->clips as $clip)
                            <x-ui.clip-card :clip="$clip" />
                        @endforeach
                    </div>

                    @if($clipsCount > 12)
                        <div class="mt-8 text-center">
                            <x-ui.button
                                :href="route('clips.list', ['game' => $game->id])"
                                variant="secondary"
                                icon="arrow-right"
                                icon-position="right"
                            >
                                {{ __('games.view_all_clips') }}
                            </x-ui.button>
                        </div>
                    @endif
                @else
                    <x-ui.empty-state
                        :message="__('games.no_clips_found')"
                        icon="video"
                        class="py-16"
                    />
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
