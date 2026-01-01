<x-layouts.app title="{{ __('auth.login_title') }}">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-neutral-950">
        <div class="max-w-md w-full">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-md bg-neutral-800 mb-4 border border-neutral-700">
                    <i class="fa-solid fa-video text-neutral-300 text-xl"></i>
                </div>
                <h1 class="text-3xl font-semibold text-neutral-100 mb-2">
                    {{ __('auth.welcome_title', ['app_name' => config('app.name')]) }}
                </h1>
                <p class="text-base text-neutral-400">
                    {{ __('auth.welcome_subtitle') }}
                </p>
            </div>

            <!-- Login Card -->
            <div class="bg-neutral-900 rounded-md border border-neutral-800 shadow-sm">
                <div class="p-8">
                    <form method="POST" action="{{ route('auth.twitch.login') }}" class="space-y-6">
                        @csrf

                        <!-- General Errors -->
                        @if ($errors->any())
                            <div class="p-4 bg-red-950/50 border border-red-900/50 text-red-200 rounded-md">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <i class="fa-solid fa-exclamation-circle text-red-400"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-red-300 mb-2">{{ __('auth.error_occurred') }}</h4>
                                        <ul class="space-y-1 text-sm">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Info Section -->
                        <div class="bg-neutral-800/50 rounded-md p-4 border border-neutral-700">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    <i class="fa-solid fa-circle-info text-neutral-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-neutral-100 mb-1">
                                        {{ __('auth.required_permission_title') }}
                                    </h3>
                                    <p class="text-sm text-neutral-400 leading-relaxed">
                                        {{ __('auth.required_permission_description', ['app_name' => config('app.name')]) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Features List -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 text-sm text-neutral-300">
                                <i class="fa-solid fa-shield text-neutral-400 w-4"></i>
                                <span>{{ __('auth.feature_secure') }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-neutral-300">
                                <i class="fa-solid fa-bolt text-neutral-400 w-4"></i>
                                <span>{{ __('auth.feature_authentication') }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-neutral-300">
                                <i class="fa-solid fa-shield-halved text-neutral-400 w-4"></i>
                                <span>{{ __('auth.feature_privacy') }}</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-neutral-900"
                                    aria-label="{{ __('auth.login_button') }}">
                                <i class="fab fa-twitch"></i>
                                <span>{{ __('auth.login_button') }}</span>
                            </button>
                        </div>

                        <!-- Privacy Notice -->
                        <div class="text-center pt-2">
                            <p class="text-xs text-neutral-500">
                                {!! __('auth.privacy_notice', ['terms' => '<a href="#" class="text-neutral-400 hover:text-neutral-300 underline">Terms of Service</a>', 'privacy' => '<a href="#" class="text-neutral-400 hover:text-neutral-300 underline">Privacy Policy</a>']) !!}
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-6 text-center">
                <p class="text-sm text-neutral-500">
                    {{ __('auth.no_account_needed') }}
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>