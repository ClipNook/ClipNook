<x-layouts.app title="{{ $game->name }} - {{ __('games.view_page_title') }}">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-neutral-950">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Game Header -->
            <div class="bg-neutral-900 rounded-md border border-neutral-800 overflow-hidden">
                <div class="relative h-48 bg-neutral-800">
                    <div class="absolute inset-0 flex items-center justify-center">
                        @if($game->local_box_art_path)
                            <div class="w-32 h-44 bg-neutral-800 rounded-md border border-neutral-700 overflow-hidden">
                                <img
                                    src="{{ Storage::url($game->local_box_art_path) }}"
                                    alt="{{ $game->name }}"
                                    class="w-full h-full object-cover"
                                >
                            </div>
                        @else
                            <div class="w-32 h-44 bg-neutral-800 rounded-md border border-neutral-700 flex items-center justify-center">
                                <i class="fa-solid fa-gamepad text-neutral-600 text-4xl"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="p-6 pt-20">
                    <h1 class="text-2xl font-semibold text-neutral-100 mb-4">{{ $game->name }}</h1>
                    <div class="flex flex-wrap gap-3 text-sm">
                        <span class="px-3 py-1.5 bg-neutral-800 border border-neutral-700 text-neutral-300 rounded-md">
                            <i class="fa-solid fa-video mr-1.5 text-neutral-500"></i>
                            {{ number_format($clipsCount) }} {{ Str::plural('clip', $clipsCount) }}
                        </span>
                        <span class="px-3 py-1.5 bg-neutral-800 border border-neutral-700 text-neutral-300 rounded-md">
                            <i class="fa-solid fa-users mr-1.5 text-neutral-500"></i>
                            {{ number_format($streamersCount) }} {{ Str::plural('streamer', $streamersCount) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Clips Section -->
            <div class="bg-neutral-900 rounded-md border border-neutral-800 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-neutral-100">
                        {{ __('games.clips_section') }}
                    </h2>
                </div>

                @if($game->clips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($game->clips as $clip)
                            <a href="{{ route('clips.view', $clip) }}" class="group block bg-neutral-800 rounded-md overflow-hidden border border-neutral-700 hover:border-neutral-600 transition-colors">
                                <!-- Thumbnail -->
                                <div class="aspect-video bg-neutral-700 relative overflow-hidden">
                                    @if($clip->thumbnail_url)
                                        <img
                                            src="{{ $clip->thumbnail_url }}"
                                            alt="{{ $clip->title }}"
                                            class="w-full h-full object-cover"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-neutral-600">
                                            <i class="fa-solid fa-video text-3xl"></i>
                                        </div>
                                    @endif

                                    <!-- Duration Badge -->
                                    <div class="absolute bottom-2 right-2 bg-black/80 text-white text-xs font-medium px-2 py-0.5 rounded">
                                        {{ round($clip->duration, 1) }}s
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-4">
                                    <h3 class="text-neutral-100 font-medium text-sm mb-3 line-clamp-2 leading-snug" title="{{ $clip->title }}">
                                        {{ $clip->title }}
                                    </h3>

                                    <div class="space-y-2">
                                        <!-- Broadcaster -->
                                        <div class="flex items-center gap-2 text-xs">
                                            <i class="fa-solid fa-user text-neutral-500 w-3"></i>
                                            <span class="text-neutral-400 truncate">{{ $clip->broadcaster?->twitch_display_name ?? 'Unknown' }}</span>
                                        </div>

                                        <!-- Stats Row -->
                                        <div class="flex items-center justify-between pt-2 border-t border-neutral-700">
                                            <div class="flex items-center gap-3 text-xs text-neutral-500">
                                                <span class="flex items-center gap-1">
                                                    <i class="fa-solid fa-eye"></i>
                                                    {{ number_format($clip->view_count) }}
                                                </span>
                                                @if($clip->upvotes > 0)
                                                    <span class="flex items-center gap-1 text-neutral-400">
                                                        <i class="fa-solid fa-thumbs-up"></i>
                                                        {{ $clip->upvotes }}
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-neutral-500">
                                                {{ $clip->created_at_twitch?->format('M j') ?? $clip->created_at->format('M j') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if($clipsCount > 12)
                        <div class="mt-6 text-center">
                            <a
                                href="{{ route('clips.list', ['game' => $game->id]) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-neutral-700 hover:bg-neutral-600 text-white text-sm font-medium rounded-md transition-colors"
                            >
                                {{ __('games.view_all_clips') }}
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-md bg-neutral-800 border border-neutral-700 mb-4">
                            <i class="fa-solid fa-video text-neutral-500 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-neutral-300 mb-2">{{ __('games.no_clips_found') }}</h3>
                        <p class="text-sm text-neutral-500">{{ __('games.no_clips_for_game') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
