<x-layouts.app title="{{ __('clips.submit_page_title') }}">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-zinc-950">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <x-ui.hero-section
                :title="__('clips.submit_page_title')"
                :subtitle="__('clips.submit_page_subtitle')"
                class="mb-12"
            >
                <div class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-video text-violet-400 text-2xl"></i>
                </div>
            </x-ui.hero-section>

            <!-- Main Content -->
            <div class="bg-zinc-900 rounded-lg border border-zinc-800 shadow-xl mb-8">
                <div class="p-6 sm:p-8">
                    <livewire:clips.submit-clip />
                </div>
            </div>

            <!-- Feature Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-zinc-900 rounded-lg p-6 border border-zinc-800 text-center">
                    <div class="w-12 h-12 bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-shield text-green-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-100 mb-2">{{ __('clips.feature_secure_title') }}</h3>
                    <p class="text-sm text-zinc-400 leading-relaxed">{{ __('clips.feature_secure_description') }}</p>
                </div>

                <div class="bg-zinc-900 rounded-lg p-6 border border-zinc-800 text-center">
                    <div class="w-12 h-12 bg-violet-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-bolt text-violet-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-100 mb-2">{{ __('clips.feature_fast_title') }}</h3>
                    <p class="text-sm text-zinc-400 leading-relaxed">{{ __('clips.feature_fast_description') }}</p>
                </div>

                <div class="bg-zinc-900 rounded-lg p-6 border border-zinc-800 text-center">
                    <div class="w-12 h-12 bg-violet-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-users text-violet-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-100 mb-2">{{ __('clips.feature_community_title') }}</h3>
                    <p class="text-sm text-zinc-400 leading-relaxed">{{ __('clips.feature_community_description') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>