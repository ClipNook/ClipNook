<x-layouts.app title="{{ __('ui.home') }}">
    <div class="min-h-screen bg-gray-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                {{ __('home.welcome_title', ['app_name' => config('app.name')]) }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-300">
                {{ __('home.welcome_subtitle') }}
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-gray-800 py-8 px-6 shadow-xl sm:rounded-lg sm:px-10 border border-gray-700">
                @auth
                    <!-- Logged in user -->
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-200">
                            {{ __('home.welcome_back', ['name' => Auth::user()->twitch_display_name ?? Auth::user()->name]) }}
                        </h3>
                        <p class="mt-2 text-sm text-gray-400">
                            {{ __('home.logged_in_with_twitch') }}
                        </p>
                        <div class="mt-6">
                            <form method="POST" action="{{ route('auth.twitch.logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 focus:ring-offset-gray-800">
                                    {{ __('home.logout_button') }}
                                </button>
                            </form>

                            <a href="{{ route('clips.submit') }}" class="mt-4 w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-gray-800">
                                {{ __('home.submit_clip_button') }}
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Guest user -->
                    <div class="text-center">
                        <p class="text-sm text-gray-400 mb-6">
                            {{ __('home.sign_in_prompt') }}
                        </p>
                        <a href="{{ route('auth.login') }}" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 focus:ring-offset-gray-800">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                            </svg>
                            {{ __('home.login_with_twitch_button') }}
                        </a>
                    </div>
                @endauth

                <!-- Flash messages -->
                @if(session('success'))
                    <div class="mt-4 p-4 bg-green-900 border border-green-700 text-green-200 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mt-4 p-4 bg-red-900 border border-red-700 text-red-200 rounded-md">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>