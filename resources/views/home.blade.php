<x-layouts.app title="{{ __('ui.home') }}">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-neutral-950">
        <div class="max-w-7xl mx-auto">
            <!-- Hero Section -->
            <div class="mb-16">
                <div class="max-w-2xl mx-auto text-center mb-8">
                    <h1 class="text-4xl sm:text-5xl font-bold text-neutral-100 mb-4 leading-tight">
                        {{ __('home.welcome_title', ['app_name' => config('app.name')]) }}
                    </h1>
                    <p class="text-lg text-neutral-400 leading-relaxed mb-8">
                        {{ __('home.welcome_subtitle') }}
                    </p>

                    @auth
                        <!-- Logged in CTAs -->
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('clips.submit') }}"
                               class="inline-flex justify-center items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-neutral-100 font-medium rounded-md transition-colors">
                                <i class="fa-solid fa-plus mr-2"></i>
                                {{ __('home.submit_clip') }}
                            </a>
                            <a href="{{ route('clips.list') }}"
                               class="inline-flex justify-center items-center px-6 py-3 bg-neutral-800 hover:bg-neutral-700 text-neutral-100 font-medium rounded-md transition-colors border border-neutral-700">
                                <i class="fa-solid fa-list mr-2"></i>
                                {{ __('home.browse_clips') }}
                            </a>
                        </div>
                    @else
                        <!-- Guest CTA -->
                        <a href="{{ route('auth.login') }}"
                           class="inline-flex justify-center items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-neutral-100 font-medium rounded-md transition-colors">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i>
                            {{ __('nav.login') }}
                        </a>
                    @endauth
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="max-w-2xl mx-auto mb-6 p-4 bg-green-900/50 border border-green-700 text-green-200 rounded-md">
                        <div class="flex items-start">
                            <i class="fa-solid fa-check-circle mr-3 mt-0.5 flex-shrink-0"></i>
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="max-w-2xl mx-auto p-4 bg-red-900/50 border border-red-700 text-red-200 rounded-md">
                        <div class="flex items-start">
                            <i class="fa-solid fa-triangle-exclamation mr-3 mt-0.5 flex-shrink-0"></i>
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Top Games -->
            <div class="mb-16">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-neutral-100">
                        <i class="fa-solid fa-gamepad mr-2 text-neutral-500"></i>
                        Top Games
                    </h2>
                    <a href="{{ route('games.list') }}" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition-colors">
                        {{ __('home.view_all') }} →
                    </a>
                </div>

                @if($topGames->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($topGames as $game)
                            <a href="{{ route('games.view', $game) }}" class="group block bg-neutral-800 rounded-md border border-neutral-700 hover:border-neutral-600 transition-colors overflow-hidden">
                                <div class="aspect-[3/4] bg-neutral-700 relative">
                                    @if($game->local_box_art_path)
                                        <img
                                            src="{{ Storage::url($game->local_box_art_path) }}"
                                            alt="{{ $game->name }}"
                                            class="w-full h-full object-cover"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fa-solid fa-gamepad text-neutral-600 text-3xl"></i>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                                        <span class="text-xs text-neutral-300">{{ $game->clips_count }} {{ Str::plural('clip', $game->clips_count) }}</span>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <p class="text-sm font-medium text-neutral-300 truncate">{{ $game->name }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-neutral-800/50 rounded-md border border-neutral-700">
                        <p class="text-neutral-400">{{ __('games.no_games_yet') }}</p>
                    </div>
                @endif
            </div>

            <!-- Latest Clips -->
            <div class="mb-16">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-neutral-100">
                        <i class="fa-solid fa-clock mr-2 text-neutral-500"></i>
                        Latest Clips
                    </h2>
                    <a href="{{ route('clips.list') }}" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition-colors">
                        {{ __('home.view_all') }} →
                    </a>
                </div>

                @if($latestClips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($latestClips as $clip)
                            <a href="{{ route('clips.view', $clip) }}" class="group block bg-neutral-800 rounded-md overflow-hidden border border-neutral-700 hover:border-neutral-600 transition-colors">
                                <div class="aspect-video bg-neutral-700 relative overflow-hidden">
                                    <img
                                        src="{{ $clip->thumbnail_url }}"
                                        alt="{{ $clip->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                        loading="lazy"
                                    >
                                    <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/70 rounded text-xs text-neutral-300 font-medium">
                                        {{ $clip->duration }}s
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-medium text-neutral-100 truncate mb-2">{{ $clip->title }}</h3>
                                    <div class="flex items-center justify-between text-xs text-neutral-400 mb-3">
                                        <span>{{ $clip->broadcaster->name }}</span>
                                        <span>{{ $clip->created_at->shortRelativeDiffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-neutral-500">
                                        <span><i class="fa-solid fa-eye mr-1"></i>{{ number_format($clip->views) }}</span>
                                        <span><i class="fa-solid fa-thumbs-up mr-1"></i>{{ number_format($clip->upvotes) }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-neutral-800/50 rounded-md border border-neutral-700">
                        <p class="text-neutral-400">{{ __('clips.no_clips_yet') }}</p>
                    </div>
                @endif
            </div>

            <!-- Top Clips -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-neutral-100">
                        <i class="fa-solid fa-trophy mr-2 text-neutral-500"></i>
                        Top Clips
                    </h2>
                    <a href="{{ route('clips.list', ['sort' => 'top']) }}" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition-colors">
                        {{ __('home.view_all') }} →
                    </a>
                </div>

                @if($topClips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($topClips as $clip)
                            <a href="{{ route('clips.view', $clip) }}" class="group block bg-neutral-800 rounded-md overflow-hidden border border-neutral-700 hover:border-neutral-600 transition-colors">
                                <div class="aspect-video bg-neutral-700 relative overflow-hidden">
                                    <img
                                        src="{{ $clip->thumbnail_url }}"
                                        alt="{{ $clip->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                        loading="lazy"
                                    >
                                    <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/70 rounded text-xs text-neutral-300 font-medium">
                                        {{ $clip->duration }}s
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-medium text-neutral-100 truncate mb-2">{{ $clip->title }}</h3>
                                    <div class="flex items-center justify-between text-xs text-neutral-400 mb-3">
                                        <span>{{ $clip->broadcaster->name }}</span>
                                        <span>{{ $clip->created_at->shortRelativeDiffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-neutral-500">
                                        <span><i class="fa-solid fa-eye mr-1"></i>{{ number_format($clip->views) }}</span>
                                        <span><i class="fa-solid fa-thumbs-up mr-1"></i>{{ number_format($clip->upvotes) }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-neutral-800/50 rounded-md border border-neutral-700">
                        <p class="text-neutral-400">{{ __('clips.no_clips_yet') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>