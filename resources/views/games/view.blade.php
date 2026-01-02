<x-layouts.app title="{{ $game->name }} - {{ __('games.view_page_title') }}">
    <div class="min-h-screen bg-zinc-950 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Page Header -->
            <div class="text-center border-b border-zinc-800/50 bg-zinc-900/80 backdrop-blur-md rounded-lg p-6">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-zinc-800 border border-(--color-accent-500)/50 rounded-lg">
                        <i class="fa-solid fa-gamepad text-xl text-(--color-accent-400)"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold text-zinc-100">{{ $game->name }}</h1>
                        <!-- Subtle accent border at top -->
                        <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500)/30 to-transparent my-2"></div>
                        <p class="text-sm text-zinc-400">{{ __('games.view_page_subtitle') }}</p>
                    </div>
                </div>
            </div>

            <!-- Game Info -->
            <div class="border border-zinc-800 rounded-lg p-6 bg-zinc-900/50">
                <!-- Subtle accent border at top -->
                <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500)/30 to-transparent mb-6"></div>

                <div class="flex flex-col lg:flex-row items-center gap-6">
                    <!-- Game Cover -->
                    <div class="flex-shrink-0">
                        @if($game->local_box_art_path)
                            <div class="w-32 h-48 bg-zinc-800 border border-zinc-700 rounded-lg overflow-hidden">
                                <img
                                    src="{{ Storage::url($game->local_box_art_path) }}"
                                    alt="{{ $game->name }}"
                                    class="w-full h-full object-cover"
                                >
                            </div>
                        @else
                            <div class="w-32 h-48 bg-zinc-800 border border-zinc-700 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-gamepad text-zinc-600 text-3xl"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Game Stats -->
                    <div class="flex-1">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-4 text-center">
                                <div class="text-lg font-bold text-zinc-100">{{ number_format($clipsCount) }}</div>
                                <div class="text-sm text-zinc-500 uppercase">{{ Str::plural(__('games.clip'), $clipsCount) }}</div>
                            </div>
                            <div class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-4 text-center">
                                <div class="text-lg font-bold text-zinc-100">{{ number_format($streamersCount) }}</div>
                                <div class="text-sm text-zinc-500 uppercase">{{ Str::plural('streamer', $streamersCount) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clips Section -->
            <div class="border border-zinc-800 rounded-lg p-6 bg-zinc-900/50">
                <!-- Subtle accent border at top -->
                <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500)/30 to-transparent mb-6"></div>

                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-zinc-100">{{ __('games.clips_section') }}</h2>
                    @if($clipsCount > 12)
                        <a href="{{ route('clips.list', ['game' => $game->id]) }}" class="text-sm text-(--color-accent-400) hover:text-(--color-accent-300) transition-colors">{{ __('games.view_all_clips') }}</a>
                    @endif
                </div>

                @if($game->clips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($game->clips as $clip)
                            <x-ui.clip-card :clip="$clip" />
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fa-solid fa-video text-zinc-600 text-2xl mb-2"></i>
                        <p class="text-zinc-500">{{ __('games.no_clips_found') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
