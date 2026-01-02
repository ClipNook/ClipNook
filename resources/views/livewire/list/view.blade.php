<div>
    <div class="min-h-screen bg-zinc-950">
        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-(--color-accent-500)/5 via-transparent to-(--color-accent-600)/3">
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-zinc-900/20 via-transparent to-transparent"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-2 h-8 bg-(--color-accent-500) rounded-full"></div>
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold text-zinc-100">
                                @if($componentName === 'games')
                                    {{ __('games.list_page_title') }}
                                @else
                                    {{ __('clips.library_page_title') }}
                                @endif
                            </h1>
                            <p class="text-zinc-400 mt-1">
                                @if($componentName === 'games')
                                    {{ __('games.list_page_subtitle') }}
                                @else
                                    {{ __('clips.library_page_subtitle') }}
                                @endif
                            </p>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-900/50 border border-green-800 rounded-lg">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-check-circle text-green-400 mt-0.5"></i>
                            <span class="text-green-200 text-sm">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                </div>

                <!-- Content -->
                <div class="bg-zinc-900/40 backdrop-blur-sm border border-zinc-800/30 rounded-2xl p-6 lg:p-8">
                    <x-ui.breadcrumb :items="[
                        ['url' => route('home'), 'label' => __('common.home')],
                        ['url' => $componentName === 'games' ? route('games.list') : route('clips.list'), 'label' => $componentName === 'games' ? __('games.browse') : __('clips.browse')],
                    ]" />
                    <!-- Filters & Search Card -->
                    <div class="bg-zinc-900/40 backdrop-blur-sm border border-zinc-800/30 rounded-2xl mb-4 p-6 lg:p-8">
                        <div class="space-y-4">
                            <div class="flex flex-col lg:flex-row gap-4">
                                <!-- Search -->
                                <div class="flex-1">
                                    <label for="{{ $componentName }}-search" class="block text-sm font-medium text-zinc-400 mb-2">
                                        <i class="fa-solid fa-magnifying-glass text-(--color-accent-400) mr-2"></i>
                                        @if($componentName === 'games')
                                            {{ __('games.search_games') }}
                                        @else
                                            {{ __('clips.search_clips') }}
                                        @endif
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="{{ $componentName }}-search" wire:model.live.debounce.300ms="search"
                                            placeholder="@if($componentName === 'games'){{ __('games.search_placeholder') }}@else{{ __('clips.search_placeholder') }}@endif"
                                            class="w-full pl-11 pr-11 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:border-(--color-accent-500) focus:outline-none focus:ring-2 focus:ring-(--color-accent-500)/20 transition-all"
                                            aria-label="@if($componentName === 'games'){{ __('games.search_placeholder') }}@else{{ __('clips.search_placeholder') }}@endif">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <i class="fa-solid fa-magnifying-glass text-zinc-500"></i>
                                        </div>
                                        @if ($search)
                                            <button wire:click="$set('search', '')" type="button"
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400 hover:text-white transition-colors"
                                                aria-label="@if($componentName === 'games'){{ __('games.clear_search') }}@else{{ __('clips.clear_search') }}@endif">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Sort -->
                                <div class="lg:w-64">
                                    <label for="{{ $componentName }}-sort" class="block text-sm font-medium text-zinc-400 mb-2">
                                        <i class="fa-solid fa-arrow-down-wide-short text-(--color-accent-400) mr-2"></i>
                                        @if($componentName === 'games')
                                            {{ __('games.sort_by') }}
                                        @else
                                            {{ __('clips.sort_by') }}
                                        @endif
                                    </label>
                                    <select id="{{ $componentName }}-sort" wire:model.live="sortBy"
                                        class="w-full px-4 py-3 border border-zinc-700 rounded-lg bg-zinc-800 text-white focus:border-(--color-accent-500) focus:outline-none focus:ring-2 focus:ring-(--color-accent-500)/20 transition-all"
                                        aria-label="@if($componentName === 'games'){{ __('games.sort_by') }}@else{{ __('clips.sort_by') }}@endif">
                                        @if($componentName === 'games')
                                            <option value="clips">{{ __('games.sort_most_clips') }}</option>
                                            <option value="alphabetical">{{ __('games.sort_alphabetical') }}</option>
                                            <option value="recent">{{ __('games.sort_recent') }}</option>
                                        @else
                                            <option value="recent">{{ __('clips.sort_recent') }}</option>
                                            <option value="popular">{{ __('clips.sort_popular') }}</option>
                                            <option value="views">{{ __('clips.sort_views') }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <!-- Active Filters -->
                            @if ($search)
                                <div class="flex items-center gap-3 p-4 bg-zinc-800/30 border border-zinc-700/50 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-(--color-accent-500)/10 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-filter text-(--color-accent-400) text-sm"></i>
                                        </div>
                                        <span class="text-sm font-medium text-zinc-300">
                                            @if($componentName === 'games')
                                                {{ __('games.active_filters') }}
                                            @else
                                                {{ __('clips.active_filters') }}
                                            @endif
                                        :</span>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-zinc-800 border border-zinc-700 rounded-lg text-sm text-zinc-300 hover:border-(--color-accent-500)/30 transition-colors">
                                            <i class="fa-solid fa-magnifying-glass text-(--color-accent-400) text-xs"></i>
                                            <span class="font-medium">{{ e($search) }}</span>
                                            <button wire:click="$set('search', '')" type="button" class="ml-1 text-zinc-400 hover:text-white transition-colors">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Content Grid or Empty State -->
                    @php
                        $hasItems = $componentName === 'games'
                            ? ($games->count() > 0)
                            : ($clips->count() > 0);
                    @endphp
                    @if ($hasItems)
                        <div>
                            <!-- Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 {{ $componentName === 'games' ? '2xl:grid-cols-5' : '' }} gap-6">
                                @if($componentName === 'games')
                                    @foreach ($games as $game)
                                        <x-ui.game-card :game="$game" />
                                    @endforeach
                                @else
                                    @foreach ($clips as $clip)
                                        <x-ui.clip-card :clip="$clip" />
                                    @endforeach
                                @endif
                            </div>

                            <!-- Pagination -->
                            <div class="mt-8">
                                @if($componentName === 'games')
                                    {{ $games->links() }}
                                @else
                                    {{ $clips->links() }}
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="bg-zinc-900/40 backdrop-blur-sm border border-zinc-800/30 rounded-2xl p-8 lg:p-12">
                            <div class="text-center max-w-md mx-auto">
                                <div class="relative inline-flex mb-6">
                                    <div
                                        class="absolute -inset-1 bg-gradient-to-r from-(--color-accent-500) to-(--color-accent-600) rounded-2xl blur-lg opacity-20">
                                    </div>
                                    <div
                                        class="relative w-20 h-20 bg-zinc-800 border border-zinc-700 rounded-2xl flex items-center justify-center">
                                        <i class="fa-solid fa-{{ $componentName === 'games' ? 'gamepad' : 'video' }} text-zinc-500 text-3xl"></i>
                                    </div>
                                </div>

                                <h3 class="text-xl font-bold text-zinc-100 mb-3">
                                    @if($componentName === 'games')
                                        {{ __('games.no_games_found') }}
                                    @else
                                        {{ __('clips.no_clips_found') }}
                                    @endif
                                </h3>

                                <p class="text-zinc-400 leading-relaxed mb-6">
                                    @if ($search)
                                        @if($componentName === 'games')
                                            {{ __('games.no_games_search', ['search' => e($search)]) }}
                                        @else
                                            {{ __('clips.no_clips_search', ['search' => $search]) }}
                                        @endif
                                    @else
                                        @if($componentName === 'games')
                                            {{ __('games.no_games_yet') }}
                                        @else
                                            {{ __('clips.no_clips_yet') }}
                                        @endif
                                    @endif
                                </p>

                                @if ($search)
                                    <x-ui.button wire:click="$set('search', '')" variant="secondary" size="md" class="inline-flex items-center gap-2">
                                        <i class="fa-solid fa-xmark"></i>
                                        @if($componentName === 'games')
                                            {{ __('games.clear_search') }}
                                        @else
                                            {{ __('clips.clear_search') }}
                                        @endif
                                    </x-ui.button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
