<x-layouts.app title="{{ __('ui.home') }}">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-neutral-950">
        <div class="max-w-7xl mx-auto">
            <!-- Hero Section -->
            <x-ui.hero-section
                :title="__('home.welcome_title', ['app_name' => config('app.name')])"
                :subtitle="__('home.welcome_subtitle')"
            >
                @auth
                    <!-- Logged in CTAs -->
                    <x-ui.button href="{{ route('clips.submit') }}" variant="primary" icon="plus">
                        {{ __('home.submit_clip') }}
                    </x-ui.button>
                    <x-ui.button href="{{ route('clips.list') }}" variant="secondary" icon="list">
                        {{ __('home.browse_clips') }}
                    </x-ui.button>
                @else
                    <!-- Guest CTA -->
                    <x-ui.button href="{{ route('auth.login') }}" variant="primary" icon="right-to-bracket">
                        {{ __('nav.login') }}
                    </x-ui.button>
                @endauth
            </x-ui.hero-section>

            <!-- Top Games -->
            <div class="mb-16">
                <x-ui.section-header
                    title="Top Games"
                    icon="gamepad"
                    :view-all-url="route('games.list')"
                />

                @if($topGames->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($topGames as $game)
                            <x-ui.game-card :game="$game" />
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state :message="__('games.no_games_yet')" icon="gamepad" />
                @endif
            </div>

            <!-- Latest Clips -->
            <div class="mb-16">
                <x-ui.section-header
                    title="Latest Clips"
                    icon="clock"
                    :view-all-url="route('clips.list')"
                />

                @if($latestClips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($latestClips as $clip)
                            <x-ui.clip-card :clip="$clip" />
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state :message="__('clips.no_clips_yet')" icon="film" />
                @endif
            </div>

            <!-- Top Clips -->
            <div>
                <x-ui.section-header
                    title="Top Clips"
                    icon="trophy"
                    :view-all-url="route('clips.list', ['sort' => 'top'])"
                />

                @if($topClips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($topClips as $clip)
                            <x-ui.clip-card :clip="$clip" />
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state :message="__('clips.no_clips_yet')" icon="trophy" />
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>