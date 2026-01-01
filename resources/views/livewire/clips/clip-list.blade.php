<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-white">{{ __('clips.library_title') }}</h2>
            <p class="text-sm text-gray-400 mt-0.5">{{ __('clips.library_subtitle') }}</p>
        </div>

        <!-- Search -->
        <div class="w-full sm:w-auto">
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('clips.search_placeholder') }}"
                    class="w-full sm:w-64 px-3 py-2 pl-9 border border-gray-700 rounded-md bg-gray-800 text-white placeholder-gray-500 focus:border-purple-500 focus:outline-none transition-colors"
                    aria-label="{{ __('clips.search_placeholder') }}"
                >
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-search text-gray-500" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Clip Grid -->
    @if($clips->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($clips as $clip)
                <a href="{{ route('clips.view', $clip->id) }}" class="group bg-gray-800 rounded-md overflow-hidden border border-gray-700 hover:border-gray-600 transition-colors">
                    <!-- Thumbnail -->
                    <div class="aspect-video bg-gray-700 relative">
                        @if($clip->thumbnail_url)
                            <img
                                src="{{ $clip->thumbnail_url }}"
                                alt="{{ $clip->title }}"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-500">
                                <i class="fas fa-image text-4xl" aria-hidden="true"></i>
                            </div>
                        @endif

                        <!-- Play Overlay -->
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors flex items-center justify-center">
                            <i class="fas fa-play-circle text-white text-5xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </div>

                        <!-- Duration Badge -->
                        <div class="absolute bottom-2 right-2 bg-black/75 text-white text-xs px-2 py-1 rounded">
                            {{ round($clip->duration, 1) }}s
                        </div>

                        <!-- Status Badge -->
                        @if($clip->status !== 'approved')
                            <div class="absolute top-2 left-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium
                                    @if($clip->status === 'pending') bg-yellow-900/90 text-yellow-200
                                    @elseif($clip->status === 'rejected') bg-red-900/90 text-red-200
                                    @else bg-gray-900/90 text-gray-200
                                    @endif">
                                    {{ ucfirst($clip->status ?? 'unknown') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="text-white font-medium text-sm mb-2 line-clamp-2 group-hover:text-purple-400 transition-colors" title="{{ $clip->title }}">
                            {{ $clip->title }}
                        </h3>

                        <div class="space-y-1 text-xs text-gray-400">
                            <div class="flex items-center">
                                <i class="fas fa-user mr-2" aria-hidden="true"></i>
                                <span class="truncate">{{ $clip->broadcaster?->twitch_display_name ?? 'Unknown' }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-eye mr-2" aria-hidden="true"></i>
                                    <span>{{ number_format($clip->view_count) }}</span>
                                </div>
                                <span>{{ $clip->created_at_twitch?->format('M j') ?? $clip->created_at->format('M j') }}</span>
                            </div>

                            @if($clip->game)
                                <div class="flex items-center">
                                    <i class="fas fa-gamepad mr-2" aria-hidden="true"></i>
                                    <span class="truncate">{{ $clip->game->name }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Votes -->
                        @if($clip->upvotes > 0 || $clip->downvotes > 0)
                            <div class="mt-2 flex items-center gap-2 text-xs">
                                <span class="text-green-400">
                                    <i class="fas fa-thumbs-up mr-1"></i>
                                    {{ $clip->upvotes }}
                                </span>
                                <span class="text-red-400">
                                    <i class="fas fa-thumbs-down mr-1"></i>
                                    {{ $clip->downvotes }}
                                </span>
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $clips->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <i class="fas fa-video text-gray-600 text-6xl mb-4" aria-hidden="true"></i>
            <h3 class="text-xl font-medium text-gray-400 mb-2">{{ __('clips.no_clips_found') }}</h3>
            <p class="text-gray-500">
                @if($search)
                    {{ __('clips.no_clips_search', ['search' => $search]) }}
                @else
                    {{ __('clips.no_clips_yet') }}
                @endif
            </p>
            @if($search)
                <button
                    wire:click="$set('search', '')"
                    class="mt-4 inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors focus:outline-none"
                    aria-label="{{ __('clips.clear_search') }}"
                >
                    <i class="fas fa-times mr-2" aria-hidden="true"></i>
                    {{ __('clips.clear_search') }}
                </button>
            @endif
        </div>
    @endif
</div>
