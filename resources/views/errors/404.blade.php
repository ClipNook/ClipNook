<x-layouts.app title="{{ __('error.404.title') }}" :noIndex="true">
    <div class="min-h-screen flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-950">
        <div class="max-w-md mx-auto w-full text-center">
            <!-- Error Icon -->
            <div class="mb-8">
                <i class="fas fa-exclamation-triangle text-red-400 text-6xl" aria-hidden="true"></i>
            </div>

            <!-- Error Title -->
            <h1 class="text-4xl font-bold text-white mb-4 leading-tight">
                {{ __('error.404.title') }}
            </h1>

            <!-- Error Message -->
            <p class="text-lg text-gray-300 mb-8 leading-relaxed">
                {{ __('error.404.text') }}
            </p>

            <!-- Actions -->
            <div class="space-y-4">
                <a href="{{ route('home') }}"
                   class="inline-flex justify-center items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-950 mr-4"
                   aria-label="{{ __('error.go_home') }}">
                    <i class="fas fa-home mr-2" aria-hidden="true"></i>
                    <span>{{ __('error.go_home') }}</span>
                </a>

                <button onclick="history.back()"
                        class="inline-flex justify-center items-center px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-gray-950"
                        aria-label="{{ __('error.go_back') }}">
                    <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
                    <span>{{ __('error.go_back') }}</span>
                </button>
            </div>
        </div>
    </div>
</x-layouts.app>