<x-layouts.app title="{{ __('ui.home') }}">
    <div class="min-h-screen bg-zinc-950">
        <!-- Hero Section -->
        <section class="py-24 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center space-y-12">
                    <div class="space-y-8">
                        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-zinc-100 leading-tight">
                            {{ __('home.welcome_title', ['app_name' => config('app.name')]) }}
                        </h1>

                        <p class="text-xl text-zinc-400 max-w-3xl mx-auto leading-relaxed">
                            {{ __('home.welcome_subtitle') }}
                        </p>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 max-w-4xl mx-auto">
                        <div class="bg-zinc-900/50 border border-zinc-800 rounded-lg p-6">
                            <div class="text-center space-y-2">
                                <div class="text-3xl font-bold text-zinc-100">
                                    {{ number_format($stats['clips_count'] ?? 0) }}
                                </div>
                                <div class="text-sm text-zinc-500 uppercase tracking-wider">{{ __('home.clips') }}</div>
                            </div>
                        </div>

                        <div class="bg-zinc-900/50 border border-zinc-800 rounded-lg p-6">
                            <div class="text-center space-y-2">
                                <div class="text-3xl font-bold text-zinc-100">
                                    {{ number_format($stats['games_count'] ?? 0) }}
                                </div>
                                <div class="text-sm text-zinc-500 uppercase tracking-wider">{{ __('home.games') }}</div>
                            </div>
                        </div>

                        <div class="bg-zinc-900/50 border border-zinc-800 rounded-lg p-6">
                            <div class="text-center space-y-2">
                                <div class="text-3xl font-bold text-zinc-100">
                                    {{ number_format($stats['users_count'] ?? 0) }}
                                </div>
                                <div class="text-sm text-zinc-500 uppercase tracking-wider">{{ __('home.users') }}</div>
                            </div>
                        </div>

                        <div class="bg-zinc-900/50 border border-zinc-800 rounded-lg p-6">
                            <div class="text-center space-y-2">
                                <div class="text-3xl font-bold text-zinc-100">
                                    {{ number_format($stats['views_count'] ?? 0) }}
                                </div>
                                <div class="text-sm text-zinc-500 uppercase tracking-wider">{{ __('home.views') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center pt-8">
                        @auth
                            <a href="{{ route('clips.submit') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-(--color-accent-500) hover:bg-(--color-accent-600) text-zinc-100 rounded text-lg font-medium transition-colors">
                                <i class="fa-solid fa-plus"></i>
                                <span>{{ __('home.submit_clip') }}</span>
                            </a>
                            <a href="{{ route('clips.list') }}" class="inline-flex items-center gap-2 px-6 py-3 border border-zinc-700 hover:border-(--color-accent-500)/50 text-zinc-400 hover:text-(--color-accent-400) rounded text-lg font-medium transition-colors">
                                <i class="fa-solid fa-film"></i>
                                <span>{{ __('home.browse_clips') }}</span>
                            </a>
                        @else
                            <a href="{{ route('auth.login') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-(--color-accent-500) hover:bg-(--color-accent-600) text-zinc-100 rounded text-lg font-medium transition-colors">
                                <i class="fa-brands fa-twitch"></i>
                                <span>{{ __('nav.login') }}</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>

        <!-- Top Games Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 border-t border-zinc-800">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-zinc-100 mb-4">
                        <i class="fa-solid fa-gamepad mr-3 text-violet-400"></i>
                        {{ __('games.top_games') }}
                    </h2>
                    <p class="text-zinc-400 max-w-2xl mx-auto">{{ __('games.discover_popular') }}</p>
                </div>

                @if($topGames->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($topGames as $game)
                            <x-ui.game-card :game="$game" />
                        @endforeach
                    </div>

                    <div class="text-center mt-8">
                        <a href="{{ route('games.list') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-zinc-700 hover:border-(--color-accent-500)/50 text-zinc-400 hover:text-(--color-accent-400) rounded text-sm font-medium transition-colors">
                            <i class="fa-solid fa-arrow-right"></i>
                            <span>{{ __('games.view_all') }}</span>
                        </a>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="max-w-md mx-auto space-y-4">
                            <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto">
                                <i class="fa-solid fa-gamepad text-zinc-600 text-xl"></i>
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-lg font-medium text-zinc-300">{{ __('games.no_games_yet') }}</h3>
                                <p class="text-zinc-500 text-sm">{{ __('games.be_first_to_add') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Latest Clips Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 border-t border-zinc-800">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-zinc-100 mb-4">
                        <i class="fa-solid fa-clock mr-3 text-violet-400"></i>
                        {{ __('clips.latest_clips') }}
                    </h2>
                    <p class="text-zinc-400 max-w-2xl mx-auto">{{ __('clips.fresh_content') }}</p>
                </div>

                @if($latestClips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($latestClips as $clip)
                            <x-ui.clip-card :clip="$clip" />
                        @endforeach
                    </div>

                    <div class="text-center mt-8">
                        <a href="{{ route('clips.list') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-zinc-700 hover:border-(--color-accent-500)/50 text-zinc-400 hover:text-(--color-accent-400) rounded text-sm font-medium transition-colors">
                            <i class="fa-solid fa-arrow-right"></i>
                            <span>{{ __('clips.view_all') }}</span>
                        </a>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="max-w-md mx-auto space-y-4">
                            <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto">
                                <i class="fa-solid fa-film text-zinc-600 text-xl"></i>
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-lg font-medium text-zinc-300">{{ __('clips.no_clips_yet') }}</h3>
                                <p class="text-zinc-500 text-sm">{{ __('clips.be_first_to_submit') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Top Clips Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 border-t border-zinc-800">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-zinc-100 mb-4">
                        <i class="fa-solid fa-fire mr-3 text-violet-400"></i>
                        {{ __('clips.top_clips') }}
                    </h2>
                    <p class="text-zinc-400 max-w-2xl mx-auto">{{ __('clips.most_upvoted') }}</p>
                </div>

                @if($topClips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($topClips as $clip)
                            <x-ui.clip-card :clip="$clip" />
                        @endforeach
                    </div>

                    <div class="text-center mt-8">
                        <a href="{{ route('clips.list', ['sort' => 'top']) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-zinc-700 hover:border-(--color-accent-500)/50 text-zinc-400 hover:text-(--color-accent-400) rounded text-sm font-medium transition-colors">
                            <i class="fa-solid fa-arrow-right"></i>
                            <span>{{ __('clips.view_top') }}</span>
                        </a>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="max-w-md mx-auto space-y-4">
                            <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto">
                                <i class="fa-solid fa-trophy text-zinc-600 text-xl"></i>
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-lg font-medium text-zinc-300">{{ __('clips.no_clips_yet') }}</h3>
                                <p class="text-zinc-500 text-sm">{{ __('clips.be_first_to_submit') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 border-t border-zinc-800">
            <div class="max-w-4xl mx-auto text-center">
                <div class="bg-zinc-900/50 border border-zinc-800 rounded-lg p-8">
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-zinc-800 border border-zinc-700 rounded text-sm text-zinc-400">
                            <i class="fa-solid fa-rocket text-(--color-accent-400)"></i>
                            {{ __('home.get_started') }}
                        </div>

                        <div class="space-y-4">
                            <h2 class="text-4xl font-bold text-zinc-100">
                                {{ __('home.cta_title') }}
                            </h2>
                            <p class="text-lg text-zinc-400 max-w-2xl mx-auto">
                                {{ __('home.cta_subtitle') }}
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                            @auth
                                <a href="{{ route('clips.submit') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-(--color-accent-500) hover:bg-(--color-accent-600) text-zinc-100 rounded text-lg font-medium transition-colors">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>{{ __('home.submit_clip') }}</span>
                                </a>
                            @else
                                <a href="{{ route('clips.list') }}" class="inline-flex items-center gap-2 px-6 py-3 border border-zinc-700 hover:border-(--color-accent-500)/50 text-zinc-400 hover:text-(--color-accent-400) rounded text-lg font-medium transition-colors">
                                    <i class="fa-solid fa-film"></i>
                                    <span>{{ __('home.browse_clips') }}</span>
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>