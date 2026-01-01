<x-layouts.app title="{{ __('ui.home') }}">
    <div class="min-h-screen py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-20">
            <!-- Hero Section -->
            <x-ui.hero-section
                :title="__('home.welcome_title', ['app_name' => config('app.name')])"
                :subtitle="__('home.welcome_subtitle')"
            >
                @auth
                    <!-- Logged in CTAs -->
                    <x-ui.button href="{{ route('clips.submit') }}" variant="primary" icon="plus" size="lg">
                        {{ __('home.submit_clip') }}
                    </x-ui.button>
                    <x-ui.button href="{{ route('clips.list') }}" variant="secondary" icon="list" size="lg">
                        {{ __('home.browse_clips') }}
                    </x-ui.button>
                @else
                    <!-- Guest CTA -->
                    <x-ui.button href="{{ route('auth.login') }}" variant="primary" icon="twitch" size="lg">
                        {{ __('nav.login') }}
                    </x-ui.button>
                @endauth
            </x-ui.hero-section>

            <!-- Top Games -->
            <section class="space-y-8">
                <x-ui.section-header
                    title="Top Games"
                    icon="gamepad"
                    :view-all-url="route('games.list')"
                    subtitle="Discover the most popular games on our platform"
                />

                @if($topGames->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                        @foreach($topGames as $game)
                            <x-ui.game-card :game="$game" />
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state :message="__('games.no_games_yet')" icon="gamepad" />
                @endif
            </section>

            <!-- Latest Clips -->
            <section class="space-y-8">
                <x-ui.section-header
                    title="Latest Clips"
                    icon="clock"
                    :view-all-url="route('clips.list')"
                    subtitle="Fresh content from our community"
                />

                @if($latestClips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($latestClips as $clip)
                            <x-ui.clip-card :clip="$clip" />
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state :message="__('clips.no_clips_yet')" icon="film" />
                @endif
            </section>

            <!-- Top Clips -->
            <section class="space-y-8">
                <x-ui.section-header
                    title="Top Clips"
                    icon="trophy"
                    :view-all-url="route('clips.list', ['sort' => 'top'])"
                    subtitle="Most upvoted clips of all time"
                />

                @if($topClips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($topClips as $clip)
                            <x-ui.clip-card :clip="$clip" />
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state :message="__('clips.no_clips_yet')" icon="trophy" />
                @endif
            </section>

            <!-- Call to Action Section -->
            <section class="py-20">
                <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-12 text-center">
                    <div class="max-w-2xl mx-auto space-y-6">
                        <h2 class="text-4xl font-bold text-zinc-100">{{ __('home.cta_title') }}</h2>
                        <p class="text-xl text-zinc-400">{{ __('home.cta_subtitle') }}</p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            @auth
                                <x-ui.button href="{{ route('clips.submit') }}" variant="primary" icon="plus" size="lg">
                                    {{ __('home.submit_clip') }}
                                </x-ui.button>
                            @else
                                <x-ui.button href="{{ route('clips.list') }}" variant="outline" icon="film" size="lg">
                                    {{ __('home.browse_clips') }}
                                </x-ui.button>
                            @endauth
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>