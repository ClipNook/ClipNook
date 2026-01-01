<x-layouts.app title="{{ $game->name }} - {{ __('games.view_page_title') }}">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-gray-950">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Game Header -->
            <div class="bg-gray-900 rounded-lg border border-gray-800 overflow-hidden">
                <div class="relative h-48 bg-gray-800">
                    <div class="absolute inset-0 flex items-center justify-center">
                        @if($game->box_art_url)
                            <div class="w-32 h-44 bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                                <img
                                    src="{{ str_replace(['{width}', '{height}'], ['285', '380'], $game->box_art_url) }}"
                                    alt="{{ $game->name }}"
                                    class="w-full h-full object-cover"
                                >
                            </div>
                        @else
                            <div class="w-32 h-44 bg-gray-800 rounded-lg border border-gray-700 flex items-center justify-center">
                                <i class="fas fa-gamepad text-gray-600 text-4xl" aria-hidden="true"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="p-6 pt-20">
                    <h1 class="text-2xl font-semibold text-white mb-4">{{ $game->name }}</h1>
                    <div class="flex flex-wrap gap-3 text-sm">
                        <span class="px-3 py-1.5 bg-gray-800 border border-gray-700 text-gray-300 rounded-lg">
                            <i class="fas fa-video mr-1.5 text-gray-500" aria-hidden="true"></i>
                            {{ number_format($clipsCount) }} {{ Str::plural('clip', $clipsCount) }}
                        </span>
                        <span class="px-3 py-1.5 bg-gray-800 border border-gray-700 text-gray-300 rounded-lg">
                            <i class="fas fa-users mr-1.5 text-gray-500" aria-hidden="true"></i>
                            {{ number_format($streamersCount) }} {{ Str::plural('streamer', $streamersCount) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Clips Section -->
            <div class="bg-gray-900 rounded-lg border border-gray-800 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-white">
                        {{ __('games.clips_section') }}
                    </h2>
                </div>

                @if($game->clips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($game->clips as $clip)
                            <a href="{{ route('clips.view', $clip->id) }}" class="group block bg-gray-800 rounded-lg overflow-hidden border border-gray-700 hover:border-gray-600 transition-colors">
                                <!-- Thumbnail -->
                                <div class="aspect-video bg-gray-700 relative overflow-hidden">
                                    @if($clip->thumbnail_url)
                                        <img
                                            src="{{ $clip->thumbnail_url }}"
                                            alt="{{ $clip->title }}"
                                            class="w-full h-full object-cover"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-600">
                                            <i class="fas fa-video text-3xl" aria-hidden="true"></i>
                                        </div>
                                    @endif

                                    <!-- Duration Badge -->
                                    <div class="absolute bottom-2 right-2 bg-black/80 text-white text-xs font-medium px-2 py-0.5 rounded">
                                        {{ round($clip->duration, 1) }}s
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-4">
                                    <h3 class="text-white font-medium text-sm mb-3 line-clamp-2 leading-snug" title="{{ $clip->title }}">
                                        {{ $clip->title }}
                                    </h3>

                                    <div class="space-y-2">
                                        <!-- Broadcaster -->
                                        <div class="flex items-center gap-2 text-xs">
                                            <i class="fas fa-user text-gray-500 w-3" aria-hidden="true"></i>
                                            <span class="text-gray-400 truncate">{{ $clip->broadcaster?->twitch_display_name ?? 'Unknown' }}</span>
                                        </div>

                                        <!-- Stats Row -->
                                        <div class="flex items-center justify-between pt-2 border-t border-gray-700">
                                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                                    {{ number_format($clip->view_count) }}
                                                </span>
                                                @if($clip->upvotes > 0)
                                                    <span class="flex items-center gap-1 text-gray-400">
                                                        <i class="fas fa-thumbs-up" aria-hidden="true"></i>
                                                        {{ $clip->upvotes }}
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-500">
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
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors"
                            >
                                {{ __('games.view_all_clips') }}
                                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-lg bg-gray-800 border border-gray-700 mb-4">
                            <i class="fas fa-video text-gray-500 text-2xl" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-300 mb-2">{{ __('games.no_clips_found') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('games.no_clips_for_game') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
