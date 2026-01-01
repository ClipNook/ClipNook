<x-layouts.app title="{{ __('clips.submit_page_title') }}">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gray-950">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2 leading-tight flex items-center justify-center gap-3">
                    <i class="fas fa-video text-purple-400" aria-hidden="true"></i>
                    {{ __('clips.submit_page_title') }}
                </h1>
                <p class="text-lg text-gray-300 leading-relaxed">
                    {{ __('clips.submit_page_subtitle') }}
                </p>
            </div>

            <!-- Main Content -->
            <div class="bg-gray-900 rounded-md border border-gray-800 mb-6">
                <div class="p-4 sm:p-6">
                    <livewire:clips.submit-clip />
                </div>
            </div>

            <!-- Feature Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-900 rounded-md p-5 border border-gray-800 text-center">
                    <i class="fas fa-shield-alt text-green-400 text-xl mb-2" aria-hidden="true"></i>
                    <h3 class="text-base font-medium text-white mb-1.5">{{ __('clips.feature_secure_title') }}</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">{{ __('clips.feature_secure_description') }}</p>
                </div>
                <div class="bg-gray-900 rounded-md p-5 border border-gray-800 text-center">
                    <i class="fas fa-rocket text-blue-400 text-xl mb-2" aria-hidden="true"></i>
                    <h3 class="text-base font-medium text-white mb-1.5">{{ __('clips.feature_fast_title') }}</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">{{ __('clips.feature_fast_description') }}</p>
                </div>
                <div class="bg-gray-900 rounded-md p-5 border border-gray-800 text-center">
                    <i class="fas fa-users text-purple-400 text-xl mb-2" aria-hidden="true"></i>
                    <h3 class="text-base font-medium text-white mb-1.5">{{ __('clips.feature_community_title') }}</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">{{ __('clips.feature_community_description') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>