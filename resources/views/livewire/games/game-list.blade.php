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
                        class="w-full px-4 py-2.5 pl-10 border border-neutral-700 rounded-md bg-neutral-800 text-white placeholder-neutral-500 focus:border-purple-500 focus:outline-none transition-colors"
                        aria-label="{{ __('games.search_placeholder') }}"
                    >
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fa-solid fa-magnifying-glass text-neutral-500 text-sm" aria-hidden="true"></i>
                    </div>
                    @if($search)
                        <button
                            wire:click="$set('search', '')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-neutral-400 hover:text-white transition-colors"
                            aria-label="{{ __('games.clear_search') }}"
                        >
                            <i class="fa-solid fa-xmark text-sm" aria-hidden="true"></i>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Sort -->
            <div>
                <select
                    wire:model.live="sortBy"
                    class="px-4 py-2.5 border border-neutral-700 rounded-md bg-neutral-800 text-white focus:border-purple-500 focus:outline-none transition-colors"
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
                <span class="text-sm text-neutral-400">{{ __('games.active_filters') }}:</span>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-neutral-800 border border-neutral-700 rounded-md text-sm text-neutral-300">
                    <i class="fa-solid fa-magnifying-glass text-xs text-neutral-500" aria-hidden="true"></i>
                    {{ e($search) }}
                    <button wire:click="$set('search', '')" class="text-neutral-400 hover:text-white transition-colors">
                        <i class="fa-solid fa-xmark text-xs" aria-hidden="true"></i>
                    </button>
                </span>
            </div>
        @endif
    </div>

    <!-- Games Grid -->
    @if($games->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach($games as $game)
                <a href="{{ route('games.view', $game) }}" class="group block bg-neutral-800 rounded-md border border-neutral-700 hover:border-neutral-600 transition-colors overflow-hidden">
                    <div class="aspect-[3/4] bg-neutral-700 relative">
                        @if($game->local_box_art_path)
                            <img
                                src="{{ Storage::url($game->local_box_art_path) }}"
                                alt="{{ e($game->name) }}"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center text-neutral-600">
                                <i class="fa-solid fa-gamepad text-3xl" aria-hidden="true"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-3">
                        <h3 class="font-medium text-white text-sm mb-1 line-clamp-2" title="{{ e($game->name) }}">
                            {{ e($game->name) }}
                        </h3>
                        <p class="text-xs text-neutral-500">
                            {{ number_format($game->clips_count) }} {{ trans_choice('games.clips_count', $game->clips_count) }}
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
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-md bg-neutral-800 border border-neutral-700 mb-4">
                <i class="fa-solid fa-gamepad text-neutral-500 text-2xl" aria-hidden="true"></i>
            </div>
            <h3 class="text-lg font-medium text-neutral-300 mb-2">{{ __('games.no_games_found') }}</h3>
            <p class="text-sm text-neutral-500 mb-6">
                @if($search)
                    {{ __('games.no_games_search', ['search' => e($search)]) }}
                @else
                    {{ __('games.no_games_yet') }}
                @endif
            </p>
            @if($search)
                <button
                    wire:click="$set('search', '')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-neutral-700 hover:bg-neutral-600 text-white text-sm font-medium rounded-md transition-colors focus:outline-none"
                    aria-label="{{ __('games.clear_search') }}"
                >
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    {{ __('games.clear_search') }}
                </button>
            @endif
        </div>
    @endif
</div>
