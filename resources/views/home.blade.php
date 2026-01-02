<x-layouts.app title="{{ __('ui.home') }}">
    <div class="min-h-screen bg-zinc-950">
        <!-- Header Section -->
        <section class="bg-zinc-900/80 backdrop-blur-md border-b border-zinc-800/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
                    <div class="flex items-center gap-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-(--color-accent-900)/20 border-2 border-(--color-accent-500) rounded-xl">
                            <i class="fa-solid fa-video text-2xl text-(--color-accent-400)"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-zinc-100 mb-2">{{ __('home.welcome_title', ['app_name' => config('app.name')]) }}</h1>
                            <p class="text-lg text-zinc-400">{{ __('home.welcome_subtitle') }}</p>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-zinc-800/50 border border-zinc-700/50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-(--color-accent-300)">{{ number_format($stats['clips_count'] ?? 0) }}</div>
                            <div class="text-sm text-zinc-400 uppercase tracking-wider">{{ __('home.clips') }}</div>
                        </div>
                        <div class="bg-zinc-800/50 border border-zinc-700/50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-(--color-accent-300)">{{ number_format($stats['games_count'] ?? 0) }}</div>
                            <div class="text-sm text-zinc-400 uppercase tracking-wider">{{ __('home.games') }}</div>
                        </div>
                        <div class="bg-zinc-800/50 border border-zinc-700/50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-(--color-accent-300)">{{ number_format($stats['users_count'] ?? 0) }}</div>
                            <div class="text-sm text-zinc-400 uppercase tracking-wider">{{ __('home.users') }}</div>
                        </div>
                        <div class="bg-zinc-800/50 border border-zinc-700/50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-(--color-accent-300)">{{ number_format($stats['views_count'] ?? 0) }}</div>
                            <div class="text-sm text-zinc-400 uppercase tracking-wider">{{ __('home.views') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content Sections -->
        <section class="px-4 sm:px-6 lg:px-8 py-16">
            <div class="max-w-7xl mx-auto space-y-16">
                <!-- Latest Clips -->
                <div class="bg-zinc-900/50 border border-zinc-800/50 rounded-xl p-8">
                    <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500)/30 to-transparent mb-8"></div>

                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-(--color-accent-900)/20 border border-(--color-accent-500) rounded-lg">
                                <i class="fa-solid fa-clock text-xl text-(--color-accent-400)"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-zinc-100">{{ __('clips.latest_clips') }}</h2>
                                <p class="text-sm text-zinc-400">{{ __('clips.discover_latest') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('clips.list') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-(--color-accent-500) text-(--color-accent-400) hover:bg-(--color-accent-500) hover:text-zinc-100 rounded-lg font-medium transition-colors">
                            <i class="fa-solid fa-arrow-right"></i>
                            <span>{{ __('clips.view_all') }}</span>
                        </a>
                    </div>

                    @if($latestClips->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($latestClips as $clip)
                                <x-ui.clip-card :clip="$clip" />
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-zinc-800/50 rounded-lg">
                            <i class="fa-solid fa-film text-(--color-accent-400) text-3xl mb-4"></i>
                            <p class="text-zinc-400">{{ __('clips.no_clips_yet') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Top Games -->
                <div class="bg-zinc-900/50 border border-zinc-800/50 rounded-xl p-8">
                    <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500)/30 to-transparent mb-8"></div>

                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-(--color-accent-900)/20 border border-(--color-accent-500) rounded-lg">
                                <i class="fa-solid fa-gamepad text-xl text-(--color-accent-400)"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-zinc-100">{{ __('games.top_games') }}</h2>
                                <p class="text-sm text-zinc-400">{{ __('games.discover_popular') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('games.list') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-(--color-accent-500) text-(--color-accent-400) hover:bg-(--color-accent-500) hover:text-zinc-100 rounded-lg font-medium transition-colors">
                            <i class="fa-solid fa-arrow-right"></i>
                            <span>{{ __('games.view_all') }}</span>
                        </a>
                    </div>

                    @if($topGames->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($topGames as $game)
                                <x-ui.game-card :game="$game" />
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-zinc-800/50 rounded-lg">
                            <i class="fa-solid fa-gamepad text-(--color-accent-400) text-3xl mb-4"></i>
                            <p class="text-zinc-400">{{ __('games.no_games_yet') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Top Clips -->
                <div class="bg-zinc-900/50 border border-zinc-800/50 rounded-xl p-8">
                    <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500)/30 to-transparent mb-8"></div>

                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-(--color-accent-900)/20 border border-(--color-accent-500) rounded-lg">
                                <i class="fa-solid fa-fire text-xl text-(--color-accent-400)"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-zinc-100">{{ __('clips.top_clips') }}</h2>
                                <p class="text-sm text-zinc-400">{{ __('clips.discover_top') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('clips.list', ['sort' => 'top']) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-(--color-accent-500) text-(--color-accent-400) hover:bg-(--color-accent-500) hover:text-zinc-100 rounded-lg font-medium transition-colors">
                            <i class="fa-solid fa-arrow-right"></i>
                            <span>{{ __('clips.view_top') }}</span>
                        </a>
                    </div>

                    @if($topClips->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($topClips as $clip)
                                <x-ui.clip-card :clip="$clip" />
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-zinc-800/50 rounded-lg">
                            <i class="fa-solid fa-trophy text-(--color-accent-400) text-3xl mb-4"></i>
                            <p class="text-zinc-400">{{ __('clips.no_clips_yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>