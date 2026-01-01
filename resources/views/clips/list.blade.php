<x-layouts.app title="{{ __('clips.library_page_title') }}">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-zinc-950">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <x-ui.hero-section
                :title="__('clips.library_page_title')"
                :subtitle="__('clips.library_page_subtitle')"
                class="mb-8"
            />

            <!-- Main Content -->
            <div class="bg-zinc-900 rounded-lg border border-zinc-800 shadow-xl">
                <div class="p-6">
                    <livewire:clips.clip-list />
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>