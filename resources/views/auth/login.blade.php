<x-layouts.app title="{{ __('auth.login_title') }}">
    <div class="min-h-screen flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-950">
        <div class="max-w-md mx-auto w-full">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2 leading-tight">
                    {{ __('auth.welcome_title', ['app_name' => config('app.name')]) }}
                </h1>
                <p class="text-lg text-gray-300 leading-relaxed">
                    {{ __('auth.welcome_subtitle') }}
                </p>
            </div>

            <!-- Login Card -->
            <div class="bg-gray-900 rounded-md border border-gray-800">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('auth.twitch.login') }}" class="space-y-6">
                        @csrf

                        <!-- General Errors -->
                        @if ($errors->any())
                            <div class="p-4 bg-red-900/50 border border-red-700 text-red-200 rounded-md">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-triangle mr-3 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                                    <div class="flex-1">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Info Section -->
                        <div class="bg-gray-800 rounded-md p-4 border border-gray-700">
                            <h3 class="text-sm font-medium text-white mb-2 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-purple-400" aria-hidden="true"></i>
                                {{ __('auth.required_permission_title') }}
                            </h3>
                            <p class="text-xs text-gray-400 leading-relaxed">
                                {{ __('auth.required_permission_description', ['app_name' => config('app.name')]) }}
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors focus:outline-none"
                                    aria-label="{{ __('auth.login_button') }}">
                                <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i>
                                <span>{{ __('auth.login_button') }}</span>
                            </button>
                        </div>

                        <!-- Privacy Notice -->
                        <div class="text-center">
                            <p class="text-xs text-gray-500 leading-relaxed">
                                {{ __('auth.privacy_notice', ['terms' => '<a href="#" class="text-purple-400 hover:text-purple-300 underline">Terms of Service</a>', 'privacy' => '<a href="#" class="text-purple-400 hover:text-purple-300 underline">Privacy Policy</a>']) }}
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>