<x-layouts.app title="{{ __('ui.home') }}">
    <div class="min-h-screen flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-950">
        <div class="max-w-md mx-auto w-full">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-4 leading-tight">
                    {{ __('home.welcome_title', ['app_name' => config('app.name')]) }}
                </h1>
                <p class="text-xl text-gray-300 leading-relaxed">
                    {{ __('home.welcome_subtitle') }}
                </p>
            </div>

            <!-- Main Card -->
            <div class="bg-gray-900 rounded-lg border border-gray-800 shadow-sm overflow-hidden">
                <div class="p-8">
                    @auth
                        <!-- Logged in user -->
                        <div class="text-center">
                            <div class="mb-6">
                                <div class="flex justify-center mb-4">
                                    <i class="fas fa-user-circle text-purple-400 text-4xl" aria-hidden="true"></i>
                                </div>
                                <h2 class="text-2xl font-semibold text-white mb-2 leading-tight">
                                    {{ __('home.welcome_back', ['name' => Auth::user()->twitch_display_name ?? Auth::user()->name]) }}
                                </h2>
                                <p class="text-gray-400">
                                    {{ __('home.logged_in_with_twitch') }}
                                </p>
                            </div>

                            <div class="space-y-4">
                                <a href="{{ route('clips.submit') }}"
                                   class="w-full inline-flex justify-center items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                                   aria-label="{{ __('home.submit_clip_button') }}">
                                    <i class="fas fa-plus mr-2" aria-hidden="true"></i>
                                    <span>{{ __('home.submit_clip_button') }}</span>
                                </a>

                                <form method="POST" action="{{ route('auth.twitch.logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit"
                                            class="w-full inline-flex justify-center items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                                            aria-label="{{ __('home.logout_button') }}">
                                        <i class="fas fa-sign-out-alt mr-2" aria-hidden="true"></i>
                                        <span>{{ __('home.logout_button') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest user -->
                        <div class="text-center">
                            <div class="mb-6">
                                <div class="flex justify-center mb-4">
                                    <i class="fab fa-twitch text-purple-400 text-4xl" aria-hidden="true"></i>
                                </div>
                                <p class="text-gray-400 mb-6 leading-relaxed">
                                    {{ __('home.sign_in_prompt') }}
                                </p>
                            </div>

                            <a href="{{ route('auth.login') }}"
                               class="w-full inline-flex justify-center items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                               aria-label="{{ __('home.login_with_twitch_button') }}">
                                <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i>
                                <span>{{ __('home.login_with_twitch_button') }}</span>
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Flash messages -->
                @if(session('success'))
                    <div class="mx-8 mb-6 p-4 bg-green-900/50 border border-green-700 text-green-200 rounded-md">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle mr-3 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                            <div class="flex-1">
                                {{ session('success') }}
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mx-8 mb-6 p-4 bg-red-900/50 border border-red-700 text-red-200 rounded-md">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle mr-3 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                            <div class="flex-1">
                                {{ session('error') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>