<x-layouts.app title="{{ __('clips.library_page_title') }}">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gray-950">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2 leading-tight flex items-center justify-center gap-3">
                    <i class="fas fa-video text-purple-400" aria-hidden="true"></i>
                    {{ __('clips.library_page_title') }}
                </h1>
                <p class="text-xl text-gray-300 leading-relaxed">
                    {{ __('clips.library_page_subtitle') }}
                </p>
            </div>

            <!-- Main Content -->
            <div class="bg-gray-900 rounded-md border border-gray-800">
                <div class="p-4 sm:p-6">
                    <livewire:clips.clip-list />
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>