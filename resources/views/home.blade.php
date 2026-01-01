<x-layouts.app title="{{ __('ui.home') }}">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gray-950">
        <div class="max-w-7xl mx-auto">
            <!-- Hero Section -->
            <div class="mb-16">
                <div class="max-w-2xl mx-auto text-center mb-8">
                    <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4 leading-tight">
                        {{ __('home.welcome_title', ['app_name' => config('app.name')]) }}
                    </h1>
                    <p class="text-lg text-gray-300 leading-relaxed mb-8">
                        {{ __('home.welcome_subtitle') }}
                    </p>

                    @auth
                        <!-- Logged in CTAs -->
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('clips.submit') }}"
                               class="inline-flex justify-center items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2" aria-hidden="true"></i>
                                {{ __('home.submit_clip') }}
                            </a>
                            <a href="{{ route('clips.list') }}"
                               class="inline-flex justify-center items-center px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                                <i class="fas fa-list mr-2" aria-hidden="true"></i>
                                {{ __('home.browse_clips') }}
                            </a>
                        </div>
                    @else
                        <!-- Guest CTA -->
                        <a href="{{ route('auth.login') }}"
                           class="inline-flex justify-center items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i>
                            {{ __('nav.login') }}
                        </a>
                    @endauth
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="max-w-2xl mx-auto mb-6 p-4 bg-green-900/50 border border-green-700 text-green-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle mr-3 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="max-w-2xl mx-auto p-4 bg-red-900/50 border border-red-700 text-red-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle mr-3 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Top Games -->
            <div class="mb-16">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">
                        <i class="fas fa-gamepad mr-2 text-gray-500" aria-hidden="true"></i>
                        Top Games
                    </h2>
                    <a href="{{ route('games.list') }}" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition-colors">
                        {{ __('home.view_all') }} →
                    </a>
                </div>

                @if($topGames->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($topGames as $game)
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
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-gamepad text-gray-600 text-3xl" aria-hidden="true"></i>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                                        <span class="text-xs text-gray-300">{{ $game->clips_count }} {{ Str::plural('clip', $game->clips_count) }}</span>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <p class="text-sm font-medium text-gray-300 truncate">{{ $game->name }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-800/50 rounded-lg border border-gray-700">
                        <p class="text-gray-400">{{ __('games.no_games_yet') }}</p>
                    </div>
                @endif
            </div>

            <!-- Latest Clips -->
            <div class="mb-16">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">
                        <i class="fas fa-clock mr-2 text-gray-500" aria-hidden="true"></i>
                        Latest Clips
                    </h2>
                    <a href="{{ route('clips.list') }}" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition-colors">
                        {{ __('home.view_all') }} →
                    </a>
                </div>

                @if($latestClips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($latestClips as $clip)
                            <a href="{{ route('clips.view', $clip) }}" class="group block bg-gray-800 rounded-lg overflow-hidden border border-gray-700 hover:border-gray-600 transition-colors">
                                <div class="aspect-video bg-gray-700 relative overflow-hidden">
                                    <img
                                        src="{{ $clip->thumbnail_url }}"
                                        alt="{{ $clip->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                        loading="lazy"
                                    >
                                    <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/70 rounded text-xs text-gray-300 font-medium">
                                        {{ $clip->duration }}s
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-medium text-white truncate mb-2">{{ $clip->title }}</h3>
                                    <div class="flex items-center justify-between text-xs text-gray-400 mb-3">
                                        <span>{{ $clip->broadcaster->name }}</span>
                                        <span>{{ $clip->created_at->shortRelativeDiffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span><i class="fas fa-eye mr-1" aria-hidden="true"></i>{{ number_format($clip->views) }}</span>
                                        <span><i class="fas fa-thumbs-up mr-1" aria-hidden="true"></i>{{ number_format($clip->upvotes) }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-800/50 rounded-lg border border-gray-700">
                        <p class="text-gray-400">{{ __('clips.no_clips_yet') }}</p>
                    </div>
                @endif
            </div>

            <!-- Top Clips -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">
                        <i class="fas fa-fire mr-2 text-gray-500" aria-hidden="true"></i>
                        Top Clips
                    </h2>
                    <a href="{{ route('clips.list') }}" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition-colors">
                        {{ __('home.view_all') }} →
                    </a>
                </div>

                @if($topClips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($topClips as $clip)
                            <a href="{{ route('clips.view', $clip) }}" class="group block bg-gray-800 rounded-lg overflow-hidden border border-gray-700 hover:border-gray-600 transition-colors">
                                <div class="aspect-video bg-gray-700 relative overflow-hidden">
                                    <img
                                        src="{{ $clip->thumbnail_url }}"
                                        alt="{{ $clip->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                        loading="lazy"
                                    >
                                    <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/70 rounded text-xs text-gray-300 font-medium">
                                        {{ $clip->duration }}s
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-medium text-white truncate mb-2">{{ $clip->title }}</h3>
                                    <div class="flex items-center justify-between text-xs text-gray-400 mb-3">
                                        <span>{{ $clip->broadcaster->name }}</span>
                                        <span>{{ $clip->created_at->shortRelativeDiffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span><i class="fas fa-eye mr-1" aria-hidden="true"></i>{{ number_format($clip->views) }}</span>
                                        <span><i class="fas fa-thumbs-up mr-1" aria-hidden="true"></i>{{ number_format($clip->upvotes) }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-800/50 rounded-lg border border-gray-700">
                        <p class="text-gray-400">{{ __('clips.no_clips_yet') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>