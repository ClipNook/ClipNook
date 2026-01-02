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
                        class="w-full px-4 py-2.5 pl-10 border border-zinc-700 rounded-md bg-zinc-800 text-white placeholder-zinc-500 focus:border-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-700 transition-colors"
                        aria-label="{{ __('clips.search_placeholder') }}"
                    >
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fa-solid fa-magnifying-glass text-zinc-500 text-sm"></i>
                    </div>
                    @if($search)
                        <button
                            wire:click="$set('search', '')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400 hover:text-white transition-colors"
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
                    class="px-4 py-2.5 border border-zinc-700 rounded-md bg-zinc-800 text-white focus:border-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-700 transition-colors"
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
                <span class="text-sm text-zinc-400">{{ __('clips.active_filters') }}:</span>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-zinc-800 border border-zinc-700 rounded-md text-sm text-zinc-300">
                    <i class="fa-solid fa-magnifying-glass text-xs text-zinc-500"></i>
                    {{ $search }}
                    <button wire:click="$set('search', '')" class="text-zinc-400 hover:text-white transition-colors">
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
                 <x-ui.clip-card :clip="$clip" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $clips->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-md bg-zinc-800 border border-zinc-700 mb-4">
                <i class="fa-solid fa-video text-zinc-500 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-zinc-300 mb-2">{{ __('clips.no_clips_found') }}</h3>
            <p class="text-sm text-zinc-500 mb-6">
                @if($search)
                    {{ __('clips.no_clips_search', ['search' => $search]) }}
                @else
                    {{ __('clips.no_clips_yet') }}
                @endif
            </p>
            @if($search)
                <x-ui.button
                    wire:click="$set('search', '')"
                    variant="secondary"
                    size="sm"
                    class="inline-flex items-center gap-2"
                    aria-label="{{ __('clips.clear_search') }}"
                >
                    <i class="fa-solid fa-xmark"></i>
                    {{ __('clips.clear_search') }}
                </x-ui.button>
            @endif
        </div>
    @endif
</div>
