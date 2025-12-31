<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white flex items-center">
                <i class="fas fa-list mr-3 text-purple-400" aria-hidden="true"></i>
                {{ __('clips.library_title') }}
            </h2>
            <p class="text-gray-400 mt-1">{{ __('clips.library_subtitle') }}</p>
        </div>

        <!-- Search -->
        <div class="w-full sm:w-auto">
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('clips.search_placeholder') }}"
                    class="w-full sm:w-64 px-4 py-2 pl-10 border border-gray-600 rounded-md bg-gray-800 text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                    aria-label="{{ __('clips.search_placeholder') }}"
                >
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-search text-gray-400" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Clip Grid -->
    @if($clips->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($clips as $clip)
                <div class="bg-gray-800 rounded-lg overflow-hidden border border-gray-700 hover:border-gray-600 transition-colors duration-200">
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

                        <!-- Duration Badge -->
                        <div class="absolute bottom-2 right-2 bg-black/75 text-white text-xs px-2 py-1 rounded">
                            {{ round($clip->duration, 1) }}s
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="text-white font-medium text-sm mb-2 line-clamp-2" title="{{ $clip->title }}">
                            {{ $clip->title }}
                        </h3>

                        <div class="space-y-1 text-xs text-gray-400">
                            <div class="flex items-center">
                                <i class="fas fa-user mr-2" aria-hidden="true"></i>
                                <span>{{ $clip->broadcaster?->display_name ?? 'Unknown' }}</span>
                            </div>

                            <div class="flex items-center">
                                <i class="fas fa-eye mr-2" aria-hidden="true"></i>
                                <span>{{ number_format($clip->view_count) }} views</span>
                            </div>

                            <div class="flex items-center">
                                <i class="fas fa-calendar mr-2" aria-hidden="true"></i>
                                <span>{{ $clip->created_at_twitch?->format('M j, Y') ?? $clip->created_at->format('M j, Y') }}</span>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="mt-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium
                                @if($clip->status === 'approved') bg-green-900/50 text-green-200 border border-green-700
                                @elseif($clip->status === 'pending') bg-yellow-900/50 text-yellow-200 border border-yellow-700
                                @elseif($clip->status === 'rejected') bg-red-900/50 text-red-200 border border-red-700
                                @else bg-gray-900/50 text-gray-200 border border-gray-700
                                @endif">
                                <i class="fas fa-circle mr-1 text-xs" aria-hidden="true"></i>
                                {{ ucfirst($clip->status ?? 'unknown') }}
                            </span>
                        </div>
                    </div>
                </div>
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
                    class="mt-4 inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                    aria-label="{{ __('clips.clear_search') }}"
                >
                    <i class="fas fa-times mr-2" aria-hidden="true"></i>
                    {{ __('clips.clear_search') }}
                </button>
            @endif
        </div>
    @endif
</div>
