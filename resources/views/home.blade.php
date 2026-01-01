<x-layouts.app title="{{ __('ui.home') }}">
    <div class="min-h-screen flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-950">
        <div class="max-w-md mx-auto w-full">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-4 leading-tight">
                    {{ __('home.welcome_title', ['app_name' => config('app.name')]) }}
                </h1>
                <p class="text-lg text-gray-300 leading-relaxed">
                    {{ __('home.welcome_subtitle') }}
                </p>
            </div>

            <!-- Main Card -->
            <div class="bg-gray-900 rounded-md border border-gray-800">
                <div class="p-6 sm:p-8">
                    @auth
                        <!-- Logged in user -->
                        <div class="text-center">
                            <div class="mb-6">
                                <div class="flex justify-center mb-4">
                                    <div class="w-16 h-16 bg-gray-800 rounded-md flex items-center justify-center">
                                        <i class="fas fa-user text-purple-400 text-2xl" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <h2 class="text-2xl font-semibold text-white mb-2 leading-tight">
                                    {{ __('home.welcome_back', ['name' => auth()->user()->name]) }}
                                </h2>
                                <p class="text-gray-400">
                                    {{ __('home.ready_to_submit') }}
                                </p>
                            </div>

                            <div class="space-y-3">
                                <a href="{{ route('clips.submit') }}"
                                   class="w-full inline-flex justify-center items-center px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors focus:outline-none"
                                   aria-label="{{ __('home.submit_clip') }}">
                                    <i class="fas fa-plus mr-2" aria-hidden="true"></i>
                                    <span>{{ __('home.submit_clip') }}</span>
                                </a>

                                <a href="{{ route('clips.list') }}"
                                   class="w-full inline-flex justify-center items-center px-5 py-2.5 bg-gray-800 hover:bg-gray-700 text-white font-medium rounded-md transition-colors focus:outline-none"
                                   aria-label="{{ __('home.browse_clips') }}">
                                    <i class="fas fa-list mr-2" aria-hidden="true"></i>
                                    <span>{{ __('home.browse_clips') }}</span>
                                </a>

                                <form method="POST" action="{{ route('auth.twitch.logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit"
                                            class="w-full inline-flex justify-center items-center px-5 py-2.5 bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium rounded-md transition-colors focus:outline-none"
                                            aria-label="{{ __('nav.logout') }}">
                                        <i class="fas fa-sign-out-alt mr-2" aria-hidden="true"></i>
                                        <span>{{ __('nav.logout') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest user -->
                        <div class="text-center">
                            <div class="mb-6">
                                <div class="flex justify-center mb-4">
                                    <div class="w-16 h-16 bg-gray-800 rounded-md flex items-center justify-center">
                                        <i class="fas fa-video text-purple-400 text-2xl" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <p class="text-gray-400 mb-6 leading-relaxed">
                                    {{ __('home.guest_description') }}
                                </p>
                            </div>

                            <a href="{{ route('auth.login') }}"
                               class="w-full inline-flex justify-center items-center px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors focus:outline-none"
                               aria-label="{{ __('nav.login') }}">
                                <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i>
                                <span>{{ __('nav.login') }}</span>
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
                                <p>{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mx-8 mb-6 p-4 bg-red-900/50 border border-red-700 text-red-200 rounded-md">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle mr-3 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                            <div class="flex-1">
                                <p>{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>