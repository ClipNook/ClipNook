<x-layouts.app title="Login with Twitch">
    <div class="min-h-screen bg-gray-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-white mb-2">Welcome to {{ config('app.name') }}</h2>
                <p class="text-lg text-gray-300">Manage your Twitch clips with ease</p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-gray-800 py-8 px-6 shadow-xl sm:rounded-lg sm:px-10 border border-gray-700">
                <form method="POST" action="{{ route('auth.twitch.login') }}" class="space-y-6">
                    @csrf

                    <!-- General Errors -->
                    @if ($errors->any())
                        <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded-md">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Info Section -->
                    <div class="bg-gray-700 rounded-lg p-4 border border-gray-600">
                        <h3 class="text-sm font-medium text-gray-200 mb-2">Required Permission</h3>
                        <p class="text-xs text-gray-400">
                            {{ config('app.name') }} requires access to your email address for account verification, password resets, and important notifications.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 focus:ring-offset-gray-800">
                            <span class="flex items-center">
                                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                Login with Twitch
                            </span>
                        </button>
                    </div>

                    <!-- Privacy Notice -->
                    <div class="text-center">
                        <p class="text-xs text-gray-500">
                            By logging in, you agree to our <a href="#" class="text-purple-400 hover:text-purple-300">Terms of Service</a> and <a href="#" class="text-purple-400 hover:text-purple-300">Privacy Policy</a>.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>