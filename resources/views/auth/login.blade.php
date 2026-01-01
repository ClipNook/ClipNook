<x-layouts.app title="{{ __('auth.login_title') }}">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-zinc-950">
        <div class="max-w-md w-full space-y-8">
            <!-- Header Section -->
            <x-ui.hero-section
                :title="__('auth.welcome_title', ['app_name' => config('app.name')])"
                :subtitle="__('auth.welcome_subtitle')"
                class="text-center"
            >
                <div class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-video text-violet-400 text-3xl"></i>
                </div>
            </x-ui.hero-section>

            <!-- Login Card -->
            <div class="bg-zinc-900 rounded-lg border border-zinc-800 shadow-2xl">
                <div class="p-8">
                    <form method="POST" action="{{ route('auth.twitch.login') }}" class="space-y-6">
                        @csrf

                        <!-- General Errors -->
                        @if ($errors->any())
                            <x-ui.alert type="error" class="mb-6">
                                <h4 class="font-medium mb-2">{{ __('auth.error_occurred') }}</h4>
                                <ul class="space-y-1 text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </x-ui.alert>
                        @endif

                        <!-- Info Section -->
                        <div class="bg-zinc-800 rounded-lg p-6 border border-zinc-700">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-violet-600/20 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-circle-info text-violet-400"></i>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-zinc-100 mb-2">
                                        {{ __('auth.required_permission_title') }}
                                    </h3>
                                    <p class="text-zinc-300 leading-relaxed">
                                        {{ __('auth.required_permission_description', ['app_name' => config('app.name')]) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Features List -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 p-3 bg-zinc-800/50 rounded-lg border border-zinc-700/50">
                                <div class="w-8 h-8 bg-green-600/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-shield text-green-400 text-sm"></i>
                                </div>
                                <span class="text-zinc-200 font-medium">{{ __('auth.feature_secure') }}</span>
                            </div>
                            <div class="flex items-center gap-4 p-3 bg-zinc-800/50 rounded-lg border border-zinc-700/50">
                                <div class="w-8 h-8 bg-violet-600/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-bolt text-violet-400 text-sm"></i>
                                </div>
                                <span class="text-zinc-200 font-medium">{{ __('auth.feature_authentication') }}</span>
                            </div>
                            <div class="flex items-center gap-4 p-3 bg-zinc-800/50 rounded-lg border border-zinc-700/50">
                                <div class="w-8 h-8 bg-zinc-600/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-shield-halved text-zinc-400 text-sm"></i>
                                </div>
                                <span class="text-zinc-200 font-medium">{{ __('auth.feature_privacy') }}</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <x-ui.button
                                type="submit"
                                variant="primary"
                                size="lg"
                                icon="twitch"
                                icon-type="brand"
                                class="w-full justify-center"
                            >
                                {{ __('auth.login_button') }}
                            </x-ui.button>
                        </div>

                        <!-- Privacy Notice -->
                        <div class="text-center pt-4 border-t border-zinc-800">
                            <p class="text-sm text-zinc-500 leading-relaxed">
                                {!! __('auth.privacy_notice', [
                                    'terms' => '<a href="#" class="text-violet-400 hover:text-violet-300 underline transition-colors">Terms of Service</a>',
                                    'privacy' => '<a href="#" class="text-violet-400 hover:text-violet-300 underline transition-colors">Privacy Policy</a>'
                                ]) !!}
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="text-center">
                <p class="text-sm text-zinc-500">
                    {{ __('auth.no_account_needed') }}
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>