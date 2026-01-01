<x-layouts.app title="{{ __('clips.submit_page_title') }}">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-neutral-950">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-neutral-100 mb-2 leading-tight flex items-center justify-center gap-3">
                    <i class="fa-solid fa-video text-purple-400"></i>
                    {{ __('clips.submit_page_title') }}
                </h1>
                <p class="text-lg text-neutral-300 leading-relaxed">
                    {{ __('clips.submit_page_subtitle') }}
                </p>
            </div>

            <!-- Main Content -->
            <div class="bg-neutral-900 rounded-md border border-neutral-800 mb-6">
                <div class="p-4 sm:p-6">
                    <livewire:clips.submit-clip />
                </div>
            </div>

            <!-- Feature Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-neutral-900 rounded-md p-5 border border-neutral-800 text-center">
                    <i class="fa-solid fa-shield text-green-400 text-xl mb-2"></i>
                    <h3 class="text-base font-medium text-neutral-100 mb-1.5">{{ __('clips.feature_secure_title') }}</h3>
                    <p class="text-sm text-neutral-400 leading-relaxed">{{ __('clips.feature_secure_description') }}</p>
                </div>
                <div class="bg-neutral-900 rounded-md p-5 border border-neutral-800 text-center">
                    <i class="fa-solid fa-bolt text-blue-400 text-xl mb-2"></i>
                    <h3 class="text-base font-medium text-neutral-100 mb-1.5">{{ __('clips.feature_fast_title') }}</h3>
                    <p class="text-sm text-neutral-400 leading-relaxed">{{ __('clips.feature_fast_description') }}</p>
                </div>
                <div class="bg-neutral-900 rounded-md p-5 border border-neutral-800 text-center">
                    <i class="fa-solid fa-users text-purple-400 text-xl mb-2"></i>
                    <h3 class="text-base font-medium text-neutral-100 mb-1.5">{{ __('clips.feature_community_title') }}</h3>
                    <p class="text-sm text-neutral-400 leading-relaxed">{{ __('clips.feature_community_description') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>