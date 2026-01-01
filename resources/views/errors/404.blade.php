<x-layouts.app title="{{ __('error.404.title') }}" :noIndex="true">
    <div class="min-h-screen flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-neutral-950">
        <div class="max-w-md mx-auto w-full text-center">
            <!-- Error Icon -->
            <div class="mb-8">
                <i class="fa-solid fa-exclamation-triangle text-red-400 text-6xl"></i>
            </div>

            <!-- Error Title -->
            <h1 class="text-4xl font-bold text-neutral-100 mb-4 leading-tight">
                {{ __('error.404.title') }}
            </h1>

            <!-- Error Message -->
            <p class="text-lg text-neutral-300 mb-8 leading-relaxed">
                {{ __('error.404.text') }}
            </p>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('home') }}"
                   class="inline-flex justify-center items-center px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors focus:outline-none"
                   aria-label="{{ __('error.go_home') }}">
                    <i class="fa-solid fa-home mr-2"></i>
                    <span>{{ __('error.go_home') }}</span>
                </a>

                <button onclick="history.back()"
                        class="inline-flex justify-center items-center px-5 py-2.5 bg-neutral-800 hover:bg-neutral-700 text-white font-medium rounded-md transition-colors focus:outline-none"
                        aria-label="{{ __('error.go_back') }}">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    <span>{{ __('error.go_back') }}</span>
                </button>
            </div>
        </div>
    </div>
</x-layouts.app>