<x-layouts.app title="{{ __('ui.home') }}">
    <div class="min-h-screen bg-gray-900 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md mx-auto w-full">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-4">
                    {{ __('home.welcome_title', ['app_name' => config('app.name')]) }}
                </h1>
                <p class="text-xl text-gray-300">
                    {{ __('home.welcome_subtitle') }}
                </p>
            </div>

            <!-- Main Card -->
            <div class="bg-gray-800 rounded-lg shadow-2xl border border-gray-700 overflow-hidden">
                <div class="p-8">
                    @auth
                        <!-- Logged in user -->
                        <div class="text-center">
                            <div class="mb-6">
                                <i class="fas fa-user-circle text-blue-400 text-4xl mb-4"></i>
                                <h2 class="text-2xl font-semibold text-white mb-2">
                                    {{ __('home.welcome_back', ['name' => Auth::user()->twitch_display_name ?? Auth::user()->name]) }}
                                </h2>
                                <p class="text-gray-400">
                                    {{ __('home.logged_in_with_twitch') }}
                                </p>
                            </div>

                            <div class="space-y-4">
                                <a href="{{ route('clips.submit') }}" class="w-full inline-flex justify-center items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    {{ __('home.submit_clip_button') }}
                                </a>

                                <form method="POST" action="{{ route('auth.twitch.logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        {{ __('home.logout_button') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest user -->
                        <div class="text-center">
                            <div class="mb-6">
                                <i class="fab fa-twitch text-purple-400 text-4xl mb-4"></i>
                                <p class="text-gray-400 mb-6">
                                    {{ __('home.sign_in_prompt') }}
                                </p>
                            </div>

                            <a href="{{ route('auth.login') }}" class="w-full inline-flex justify-center items-center px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                                </svg>
                                {{ __('home.login_with_twitch_button') }}
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Flash messages -->
                @if(session('success'))
                    <div class="mx-8 mb-6 p-4 bg-green-900 border border-green-700 text-green-200 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mx-8 mb-6 p-4 bg-red-900 border border-red-700 text-red-200 rounded-lg">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>