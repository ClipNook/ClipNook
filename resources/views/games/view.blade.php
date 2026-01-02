<x-layouts.app title="{{ $game->name }} - {{ __('games.view_page_title') }}">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-zinc-950">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Game Header -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-lg overflow-hidden">
                <div class="relative h-64 bg-zinc-800">
                    <div class="absolute inset-0 flex items-center justify-center">
                        @if($game->local_box_art_path)
                            <div class="w-40 h-56 bg-zinc-800 border-2 border-zinc-700 rounded-lg overflow-hidden">
                                <img
                                    src="{{ Storage::url($game->local_box_art_path) }}"
                                    alt="{{ $game->name }}"
                                    class="w-full h-full object-cover"
                                >
                            </div>
                        @else
                            <div class="w-40 h-56 bg-zinc-800 rounded-lg border-2 border-zinc-700 flex items-center justify-center shadow-2xl">
                                <i class="fa-solid fa-gamepad text-zinc-600 text-5xl"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="p-8 text-center">
                    <h1 class="text-3xl font-bold text-zinc-100 mb-4">{{ $game->name }}</h1>
                    <div class="flex flex-wrap justify-center gap-4 text-sm">
                        <div class="px-4 py-2 bg-zinc-800 border border-zinc-700 text-zinc-300 rounded-lg hover:border-violet-600 transition-colors">
                            <i class="fa-solid fa-video mr-2 text-violet-400"></i>
                            {{ number_format($clipsCount) }} {{ Str::plural(__('games.clip'), $clipsCount) }}
                        </div>
                        <div class="px-4 py-2 bg-zinc-800 border border-zinc-700 text-zinc-300 rounded-lg hover:border-violet-600 transition-colors">
                            <i class="fa-solid fa-users mr-2 text-violet-400"></i>
                            {{ number_format($streamersCount) }} {{ Str::plural('streamer', $streamersCount) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clips Section -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-zinc-100">
                        <i class="fa-solid fa-video mr-2 text-violet-400"></i>
                        {{ __('games.clips_section') }}
                    </h2>
                </div>

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
                    <div class="text-center py-16 bg-zinc-800 border border-zinc-700 rounded-lg">
                        <i class="fa-solid fa-video text-zinc-600 text-3xl mb-3 block"></i>
                        <p class="text-zinc-400">{{ __('games.no_clips_found') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
