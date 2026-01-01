<div>
    <!-- Filters & Search -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('clips.search_placeholder') }}"
                        class="w-full px-4 py-2.5 pl-10 border border-neutral-700 rounded-md bg-neutral-800 text-white placeholder-neutral-500 focus:border-neutral-600 focus:outline-none focus:ring-2 focus:ring-neutral-700 transition-colors"
                        aria-label="{{ __('clips.search_placeholder') }}"
                    >
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fa-solid fa-magnifying-glass text-neutral-500 text-sm"></i>
                    </div>
                    @if($search)
                        <button
                            wire:click="$set('search', '')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-neutral-400 hover:text-white transition-colors"
                            aria-label="{{ __('clips.clear_search') }}"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Sort & Filter -->
            <div class="flex gap-3">
                <select
                    wire:model.live="sortBy"
                    class="px-4 py-2.5 border border-neutral-700 rounded-md bg-neutral-800 text-white focus:border-neutral-600 focus:outline-none focus:ring-2 focus:ring-neutral-700 transition-colors"
                    aria-label="{{ __('clips.sort_by') }}"
                >
                    <option value="recent">{{ __('clips.sort_recent') }}</option>
                    <option value="popular">{{ __('clips.sort_popular') }}</option>
                    <option value="views">{{ __('clips.sort_views') }}</option>
                </select>
            </div>
        </div>

        <!-- Active Filters -->
        @if($search)
            <div class="mt-3 flex items-center gap-2">
                <span class="text-sm text-neutral-400">{{ __('clips.active_filters') }}:</span>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-neutral-800 border border-neutral-700 rounded-md text-sm text-neutral-300">
                    <i class="fa-solid fa-magnifying-glass text-xs text-neutral-500"></i>
                    {{ $search }}
                    <button wire:click="$set('search', '')" class="text-neutral-400 hover:text-white transition-colors">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </span>
            </div>
        @endif
    </div>

    <!-- Clip Grid -->
    @if($clips->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($clips as $clip)
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

                            <!-- Game -->
                            @if($clip->game)
                                <div class="flex items-center gap-2 text-xs">
                                    <i class="fa-solid fa-gamepad text-neutral-500 w-3"></i>
                                    <span class="text-neutral-400 truncate">{{ $clip->game->name }}</span>
                                </div>
                            @endif

                            <!-- Stats Row -->
                            <div class="flex items-center justify-between pt-2 border-t border-neutral-700">
                                <div class="flex items-center gap-3 text-xs text-neutral-500">
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-eye"></i>
                                        {{ number_format($clip->view_count) }}
                                    </span>
                                    @if($clip->upvotes > 0 || $clip->downvotes > 0)
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

        <!-- Pagination -->
        <div class="mt-6">
            {{ $clips->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-md bg-neutral-800 border border-neutral-700 mb-4">
                <i class="fa-solid fa-video text-neutral-500 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-neutral-300 mb-2">{{ __('clips.no_clips_found') }}</h3>
            <p class="text-sm text-neutral-500 mb-6">
                @if($search)
                    {{ __('clips.no_clips_search', ['search' => $search]) }}
                @else
                    {{ __('clips.no_clips_yet') }}
                @endif
            </p>
            @if($search)
                <button
                    wire:click="$set('search', '')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-neutral-700 hover:bg-neutral-600 text-white text-sm font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-neutral-600"
                    aria-label="{{ __('clips.clear_search') }}"
                >
                    <i class="fa-solid fa-xmark"></i>
                    {{ __('clips.clear_search') }}
                </button>
            @endif
        </div>
    @endif
</div>
