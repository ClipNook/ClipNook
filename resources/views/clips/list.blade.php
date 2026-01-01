<x-layouts.app title="{{ __('clips.library_page_title') }}">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-gray-950">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-white mb-1">
                    {{ __('clips.library_page_title') }}
                </h1>
                <p class="text-sm text-gray-400">
                    {{ __('clips.library_page_subtitle') }}
                </p>
            </div>

            <!-- Main Content -->
            <div class="bg-gray-900 rounded-lg border border-gray-800">
                <div class="p-6">
                    <livewire:clips.clip-list />
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>