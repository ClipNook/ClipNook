<x-layouts.app title="{{ __('ui.home') }}">
    <div class="max-w-4xl mx-auto">
        <div class="text-center space-y-6">
            <div class="space-y-4">
                <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 dark:text-white">
                    {{ __('ui.welcome_to') }} {{ config('app.name') }}
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    {{ __('ui.home_description') }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                @auth
                    <x-button href="{{ route('clips.submit') }}" variant="primary" size="lg" accent="bg">
                        <i class="fas fa-plus mr-2"></i>
                        {{ __('ui.submit_clip') }}
                    </x-button>
                    <x-button href="#clips" variant="outline" size="lg">
                        <i class="fas fa-list mr-2"></i>
                        {{ __('ui.browse_clips') }}
                    </x-button>
                @else
                    <x-button href="{{ route('login') }}" variant="primary" size="lg" accent="bg">
                        <i class="fab fa-twitch mr-2"></i>
                        {{ __('ui.auth.sign_in_with_twitch') }}
                    </x-button>
                    <x-button href="#clips" variant="outline" size="lg">
                        <i class="fas fa-list mr-2"></i>
                        {{ __('ui.browse_clips') }}
                    </x-button>
                @endauth
            </div>
        </div>
    </div>
</x-layouts.app>