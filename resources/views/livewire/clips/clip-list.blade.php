<div>
    <div class="min-h-screen bg-zinc-950">
        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <div
                class="absolute inset-0 bg-gradient-to-br from-(--color-accent-500)/5 via-transparent to-(--color-accent-600)/3">
                <div
                    class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-zinc-900/20 via-transparent to-transparent">
                </div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-2 h-8 bg-(--color-accent-500) rounded-full"></div>
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold text-zinc-100">{{ __('clips.library_page_title') }}
                            </h1>
                            <p class="text-zinc-400 mt-1">{{ __('clips.library_page_subtitle') }}</p>
                        </div>
                    </div>

                    @auth
                        <a href="{{ route('clips.submit') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-(--color-accent-500) text-(--color-accent-400) hover:bg-(--color-accent-500) hover:text-zinc-100 rounded-lg font-medium transition-colors shadow-md hover:shadow-(--color-accent-500)/20">
                            <i class="fa-solid fa-plus"></i>
                            <span>{{ __('clips.submit_clip') }}</span>
                        </a>
                    @endauth
                </div>
                <!-- Content -->
                <div class="bg-zinc-900/40 backdrop-blur-sm border border-zinc-800/30 rounded-2xl p-6 lg:p-8">
                    <x-ui.breadcrumb :items="[
                        ['url' => route('home'), 'label' => __('common.home')],
                        ['url' => route('clips.list'), 'label' => __('clips.browse')],
                    ]" />
                    <!-- Filters & Search Card -->
                    <div class="bg-zinc-900/40 backdrop-blur-sm border border-zinc-800/30 rounded-2xl p-6 lg:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-sliders text-(--color-accent-400)"></i>
                            </div>
                            <h2 class="text-xl font-bold text-zinc-100">{{ __('clips.filter_search') }}</h2>
                        </div>

                        <div class="space-y-4">
                            <div class="flex flex-col lg:flex-row gap-4">
                                <!-- Search -->
                                <div class="flex-1">
                                    <label for="clip-search" class="block text-sm font-medium text-zinc-400 mb-2">
                                        <i class="fa-solid fa-magnifying-glass text-(--color-accent-400) mr-2"></i>
                                        {{ __('clips.search_clips') }}
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="clip-search" wire:model.live.debounce.300ms="search"
                                            placeholder="{{ __('clips.search_placeholder') }}"
                                            class="w-full pl-11 pr-11 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:border-(--color-accent-500) focus:outline-none focus:ring-2 focus:ring-(--color-accent-500)/20 transition-all"
                                            aria-label="{{ __('clips.search_placeholder') }}">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <i class="fa-solid fa-magnifying-glass text-zinc-500"></i>
                                        </div>
                                        @if ($search)
                                            <button wire:click="$set('search', '')" type="button"
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400 hover:text-white transition-colors"
                                                aria-label="{{ __('clips.clear_search') }}">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Sort -->
                                <div class="lg:w-64">
                                    <label for="clip-sort" class="block text-sm font-medium text-zinc-400 mb-2">
                                        <i class="fa-solid fa-arrow-down-wide-short text-(--color-accent-400) mr-2"></i>
                                        {{ __('clips.sort_by') }}
                                    </label>
                                    <select id="clip-sort" wire:model.live="sortBy"
                                        class="w-full px-4 py-3 border border-zinc-700 rounded-lg bg-zinc-800 text-white focus:border-(--color-accent-500) focus:outline-none focus:ring-2 focus:ring-(--color-accent-500)/20 transition-all"
                                        aria-label="{{ __('clips.sort_by') }}">
                                        <option value="recent">{{ __('clips.sort_recent') }}</option>
                                        <option value="popular">{{ __('clips.sort_popular') }}</option>
                                        <option value="views">{{ __('clips.sort_views') }}</option>
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
                                        <span class="text-sm font-medium text-zinc-300">{{ __('clips.active_filters') }}:</span>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-zinc-800 border border-zinc-700 rounded-lg text-sm text-zinc-300 hover:border-(--color-accent-500)/30 transition-colors">
                                            <i class="fa-solid fa-magnifying-glass text-(--color-accent-400) text-xs"></i>
                                            <span class="font-medium">{{ $search }}</span>
                                            <button wire:click="$set('search', '')" type="button" class="ml-1 text-zinc-400 hover:text-white transition-colors">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Clip Grid -->
                    @if ($clips->count() > 0)
                        <div>
                            <!-- Stats Header -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-(--color-accent-500)/10 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-video text-(--color-accent-400) text-sm"></i>
                                    </div>
                                    <p class="text-sm text-zinc-400">
                                        {{ trans_choice('clips.showing_clips_count', $clips->total(), ['count' => number_format($clips->total())]) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Clip Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                @foreach ($clips as $clip)
                                    <x-ui.clip-card :clip="$clip" />
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-8">
                                {{ $clips->links() }}
                            </div>
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="bg-zinc-900/40 backdrop-blur-sm border border-zinc-800/30 rounded-2xl p-8 lg:p-12">
                            <div class="text-center max-w-md mx-auto">
                                <div class="relative inline-flex mb-6">
                                    <div class="absolute -inset-1 bg-gradient-to-r from-(--color-accent-500) to-(--color-accent-600) rounded-2xl blur-lg opacity-20"></div>
                                    <div class="relative w-20 h-20 bg-zinc-800 border border-zinc-700 rounded-2xl flex items-center justify-center">
                                        <i class="fa-solid fa-video text-zinc-500 text-3xl"></i>
                                    </div>
                                </div>

                                <h3 class="text-xl font-bold text-zinc-100 mb-3">{{ __('clips.no_clips_found') }}</h3>

                                <p class="text-zinc-400 leading-relaxed mb-6">
                                    @if ($search)
                                        {{ __('clips.no_clips_search', ['search' => $search]) }}
                                    @else
                                        {{ __('clips.no_clips_yet') }}
                                    @endif
                                </p>

                                @if ($search)
                                    <x-ui.button wire:click="$set('search', '')" variant="secondary" size="md" class="inline-flex items-center gap-2">
                                        <i class="fa-solid fa-xmark"></i>
                                        {{ __('clips.clear_search') }}
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
