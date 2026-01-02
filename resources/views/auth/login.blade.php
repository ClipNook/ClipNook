<x-layouts.app title="{{ __('auth.login_title') }}">
    <div class="min-h-screen flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8 bg-zinc-950">
        <div class="max-w-md w-full">
            <!-- Icon Badge -->
            <div class="flex justify-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-zinc-900 border-2 border-(--color-accent-500) rounded-2xl">
                    <i class="fa-brands fa-twitch text-3xl text-(--color-accent-400)"></i>
                </div>
            </div>

            <!-- Login Card -->
            <div class="relative bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
                <!-- Subtle accent border at top -->
                <div
                    class="absolute top-0 left-0 right-0 h-px bg-linear-to-r from-transparent via-(--color-accent-500)/30 to-transparent">
                </div>
                <div class="p-10">
                    <form method="POST" action="{{ route('auth.twitch.login') }}" class="space-y-6">
                        @csrf

                        <!-- General Errors -->
                        @if ($errors->any())
                            <div class="bg-red-900/50 border border-red-800 rounded-lg p-4 mb-6">
                                <h4 class="font-medium text-red-200 mb-2">{{ __('auth.error_occurred') }}</h4>
                                <ul class="space-y-1 text-sm text-red-300">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Info Section -->
                        <div class="bg-zinc-800 border border-zinc-700 rounded-lg p-6">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <i class="fa-solid fa-circle-info text-zinc-400 text-lg"></i>
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
                        <div class="space-y-3">
                            <div class="flex items-center gap-4 p-3 bg-zinc-800 border border-zinc-700 rounded-lg">
                                <i class="fa-solid fa-shield text-zinc-400 text-sm"></i>
                                <span class="text-zinc-200 font-medium">{{ __('auth.feature_secure') }}</span>
                            </div>
                            <div class="flex items-center gap-4 p-3 bg-zinc-800 border border-zinc-700 rounded-lg">
                                <i class="fa-solid fa-bolt text-zinc-400 text-sm"></i>
                                <span class="text-zinc-200 font-medium">{{ __('auth.feature_authentication') }}</span>
                            </div>
                            <div class="flex items-center gap-4 p-3 bg-zinc-800 border border-zinc-700 rounded-lg">
                                <i class="fa-solid fa-shield-halved text-zinc-400 text-sm"></i>
                                <span class="text-zinc-200 font-medium">{{ __('auth.feature_privacy') }}</span>
                            </div>
                        </div>

                        <!-- Subtle accent border at top -->
                        <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500)/30 to-transparent my-2"></div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit"
                                class="w-full bg-(--color-accent-500) hover:bg-(--color-accent-600) text-zinc-100 rounded-lg px-4 py-3 font-medium transition-colors flex items-center justify-center gap-2">
                                <i class="fa-brands fa-twitch"></i>
                                <span>{{ __('auth.login_button') }}</span>
                            </button>
                        </div>
                        
                        <!-- Subtle accent border at top -->
                        <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500)/30 to-transparent my-2"></div>

                        <!-- Privacy Notice -->
                        <div class="text-center pt-4">
                            <p class="text-sm text-zinc-500 leading-relaxed">
                                {!! __('auth.privacy_notice', [
                                    'terms' =>
                                        '<a href="' . route('legal.terms') . '" class="text-(--color-accent-400) hover:text-(--color-accent-300) underline transition-colors">Terms of Service</a>',
                                    'privacy' =>
                                        '<a href="' . route('legal.privacy') . '" class="text-(--color-accent-400) hover:text-(--color-accent-300) underline transition-colors">Privacy Policy</a>',
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
