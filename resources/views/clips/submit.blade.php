<x-layouts.app title="{{ __('clips.submit_page_title') }}">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-zinc-950">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-12">
                <div class="max-w-2xl mx-auto text-center mb-8">
                    <h1 class="text-3xl font-bold text-zinc-100 mb-4">
                        {{ __('clips.submit_page_title') }}
                    </h1>
                    <p class="text-lg text-zinc-400 mb-8">
                        {{ __('clips.submit_page_subtitle') }}
                    </p>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-green-900/50 border border-green-800 rounded-lg p-4 max-w-2xl mx-auto mb-6">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-check-circle text-green-400 mt-0.5"></i>
                            <span class="text-green-200">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-900/50 border border-red-800 rounded-lg p-4 max-w-2xl mx-auto">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation text-red-400 mt-0.5"></i>
                            <span class="text-red-200">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Main Content -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-lg mb-8">
                <div class="p-6 sm:p-8">
                    <livewire:clips.submit-clip />
                </div>
            </div>

            <!-- Feature Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-6 text-center hover:border-violet-600 transition-colors">
                    <div class="flex justify-center mb-4">
                        <i class="fa-solid fa-shield text-violet-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-100 mb-2">{{ __('clips.feature_secure_title') }}</h3>
                    <p class="text-sm text-zinc-400">{{ __('clips.feature_secure_description') }}</p>
                </div>

                <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-6 text-center hover:border-violet-600 transition-colors">
                    <div class="flex justify-center mb-4">
                        <i class="fa-solid fa-bolt text-violet-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-100 mb-2">{{ __('clips.feature_fast_title') }}</h3>
                    <p class="text-sm text-zinc-400">{{ __('clips.feature_fast_description') }}</p>
                </div>

                <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-6 text-center hover:border-violet-600 transition-colors">
                    <div class="flex justify-center mb-4">
                        <i class="fa-solid fa-users text-violet-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-100 mb-2">{{ __('clips.feature_community_title') }}</h3>
                    <p class="text-sm text-zinc-400">{{ __('clips.feature_community_description') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>