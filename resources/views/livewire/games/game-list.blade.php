<div>
    <!-- Search & Filter -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('games.search_placeholder') }}"
                        class="w-full px-4 py-2.5 pl-10 border border-gray-700 rounded-lg bg-gray-800 text-white placeholder-gray-500 focus:border-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-700 transition-colors"
                        aria-label="{{ __('games.search_placeholder') }}"
                    >
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-search text-gray-500 text-sm" aria-hidden="true"></i>
                    </div>
                    @if($search)
                        <button
                            wire:click="$set('search', '')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-white transition-colors"
                            aria-label="{{ __('games.clear_search') }}"
                        >
                            <i class="fas fa-times text-sm" aria-hidden="true"></i>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Sort -->
            <div>
                <select
                    wire:model.live="sortBy"
                    class="px-4 py-2.5 border border-gray-700 rounded-lg bg-gray-800 text-white focus:border-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-700 transition-colors"
                    aria-label="{{ __('games.sort_by') }}"
                >
                    <option value="clips">{{ __('games.sort_most_clips') }}</option>
                    <option value="alphabetical">{{ __('games.sort_alphabetical') }}</option>
                    <option value="recent">{{ __('games.sort_recent') }}</option>
                </select>
            </div>
        </div>

        <!-- Active Filters -->
        @if($search)
            <div class="mt-3 flex items-center gap-2">
                <span class="text-sm text-gray-400">{{ __('games.active_filters') }}:</span>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-800 border border-gray-700 rounded-lg text-sm text-gray-300">
                    <i class="fas fa-search text-xs text-gray-500" aria-hidden="true"></i>
                    {{ $search }}
                    <button wire:click="$set('search', '')" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-times text-xs" aria-hidden="true"></i>
                    </button>
                </span>
            </div>
        @endif
    </div>

    <!-- Games Grid -->
    @if($games->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach($games as $game)
                <a href="{{ route('games.view', $game) }}" class="group block bg-gray-800 rounded-lg border border-gray-700 hover:border-gray-600 transition-colors overflow-hidden">
                    <div class="aspect-[3/4] bg-gray-700 relative">
                        @if($game->box_art_url)
                            <img
                                src="{{ str_replace(['{width}', '{height}'], ['285', '380'], $game->box_art_url) }}"
                                alt="{{ $game->name }}"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-600">
                                <i class="fas fa-gamepad text-3xl" aria-hidden="true"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-3">
                        <h3 class="font-medium text-white text-sm mb-1 line-clamp-2" title="{{ $game->name }}">
                            {{ $game->name }}
                        </h3>
                        <p class="text-xs text-gray-500">
                            {{ number_format($game->clips_count) }} {{ __('games.clips_count', ['count' => $game->clips_count]) }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $games->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-lg bg-gray-800 border border-gray-700 mb-4">
                <i class="fas fa-gamepad text-gray-500 text-2xl" aria-hidden="true"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-300 mb-2">{{ __('games.no_games_found') }}</h3>
            <p class="text-sm text-gray-500 mb-6">
                @if($search)
                    {{ __('games.no_games_search', ['search' => $search]) }}
                @else
                    {{ __('games.no_games_yet') }}
                @endif
            </p>
            @if($search)
                <button
                    wire:click="$set('search', '')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-600"
                    aria-label="{{ __('games.clear_search') }}"
                >
                    <i class="fas fa-times" aria-hidden="true"></i>
                    {{ __('games.clear_search') }}
                </button>
            @endif
        </div>
    @endif
</div>
