<x-layouts.app title="{{ __('ui.home') }}">
    <div class="min-h-screen">
        <!-- Hero Section -->
        <section class="bg-zinc-950 py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center space-y-8">
                    <!-- Main Heading -->
                    <div class="space-y-4">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-zinc-100">
                            {{ __('home.welcome_title', ['app_name' => config('app.name')]) }}
                        </h1>
                        <p class="text-xl text-zinc-400 max-w-2xl mx-auto">
                            {{ __('home.welcome_subtitle') }}
                        </p>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @auth
                            <a href="{{ route('clips.submit') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[var(--color-accent-500)] hover:bg-[var(--color-accent-600)] text-zinc-100 rounded text-sm font-medium transition-colors shadow-lg shadow-[var(--color-accent-500)]/20">
                                <i class="fa-solid fa-plus"></i>
                                {{ __('home.submit_clip') }}
                            </a>
                            <a href="{{ route('clips.list') }}" class="inline-flex items-center gap-2 px-6 py-3 border border-zinc-700 hover:border-[var(--color-accent-500)]/50 text-zinc-400 hover:text-[var(--color-accent-400)] rounded text-sm font-medium transition-colors">
                                <i class="fa-solid fa-list"></i>
                                {{ __('home.browse_clips') }}
                            </a>
                        @else
                            <a href="{{ route('auth.login') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[var(--color-accent-500)] hover:bg-[var(--color-accent-600)] text-zinc-100 rounded text-sm font-medium transition-colors shadow-lg shadow-[var(--color-accent-500)]/20">
                                <i class="fa-brands fa-twitch"></i>
                                {{ __('nav.login') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>

        <!-- Top Games Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 bg-zinc-900">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-zinc-100 mb-4">{{ __('games.top_games') }}</h2>
                    <p class="text-zinc-400 max-w-2xl mx-auto">{{ __('games.discover_popular') }}</p>
                </div>

                @if($topGames->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                        @foreach($topGames as $game)
                            <x-ui.game-card :game="$game" />
                        @endforeach
                    </div>
                    <div class="text-center mt-8">
                        <a href="{{ route('games.list') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-zinc-700 hover:border-[var(--color-accent-500)]/50 text-zinc-400 hover:text-[var(--color-accent-400)] rounded text-sm transition-colors">
                            <i class="fa-solid fa-arrow-right"></i>
                            {{ __('games.view_all') }}
                        </a>
                    </div>
                @else
                    <x-ui.empty-state :message="__('games.no_games_yet')" icon="gamepad" />
                @endif
            </div>
        </section>

        <!-- Latest Clips Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 bg-zinc-950">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-zinc-100 mb-4">{{ __('clips.latest_clips') }}</h2>
                    <p class="text-zinc-400 max-w-2xl mx-auto">{{ __('clips.fresh_content') }}</p>
                </div>

                @if($latestClips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($latestClips as $clip)
                            <x-ui.clip-card :clip="$clip" />
                        @endforeach
                    </div>
                    <div class="text-center mt-8">
                        <a href="{{ route('clips.list') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-zinc-700 hover:border-[var(--color-accent-500)]/50 text-zinc-400 hover:text-[var(--color-accent-400)] rounded text-sm transition-colors">
                            <i class="fa-solid fa-arrow-right"></i>
                            {{ __('clips.view_all') }}
                        </a>
                    </div>
                @else
                    <x-ui.empty-state :message="__('clips.no_clips_yet')" icon="film" />
                @endif
            </div>
        </section>

        <!-- Top Clips Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 bg-zinc-900">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-zinc-100 mb-4">{{ __('clips.top_clips') }}</h2>
                    <p class="text-zinc-400 max-w-2xl mx-auto">{{ __('clips.most_upvoted') }}</p>
                </div>

                @if($topClips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($topClips as $clip)
                            <x-ui.clip-card :clip="$clip" />
                        @endforeach
                    </div>
                    <div class="text-center mt-8">
                        <a href="{{ route('clips.list', ['sort' => 'top']) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-zinc-700 hover:border-[var(--color-accent-500)]/50 text-zinc-400 hover:text-[var(--color-accent-400)] rounded text-sm transition-colors">
                            <i class="fa-solid fa-arrow-right"></i>
                            {{ __('clips.view_top') }}
                        </a>
                    </div>
                @else
                    <x-ui.empty-state :message="__('clips.no_clips_yet')" icon="trophy" />
                @endif
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 bg-zinc-950">
            <div class="max-w-4xl mx-auto text-center">
                <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-8">
                    <div class="space-y-6">
                        <h2 class="text-3xl font-bold text-zinc-100">{{ __('home.cta_title') }}</h2>
                        <p class="text-xl text-zinc-400">{{ __('home.cta_subtitle') }}</p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            @auth
                                <a href="{{ route('clips.submit') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[var(--color-accent-500)] hover:bg-[var(--color-accent-600)] text-zinc-100 rounded text-sm font-medium transition-colors shadow-lg shadow-[var(--color-accent-500)]/20">
                                    <i class="fa-solid fa-plus"></i>
                                    {{ __('home.submit_clip') }}
                                </a>
                            @else
                                <a href="{{ route('clips.list') }}" class="inline-flex items-center gap-2 px-6 py-3 border border-zinc-700 hover:border-[var(--color-accent-500)]/50 text-zinc-400 hover:text-[var(--color-accent-400)] rounded text-sm font-medium transition-colors">
                                    <i class="fa-solid fa-film"></i>
                                    {{ __('home.browse_clips') }}
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>