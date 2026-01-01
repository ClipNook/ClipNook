<x-layouts.app title="{{ __('games.list_page_title') }}">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-neutral-950">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-neutral-100 mb-1">
                    {{ __('games.list_page_title') }}
                </h1>
                <p class="text-sm text-neutral-400">{{ __('games.list_page_subtitle') }}</p>
            </div>

            <!-- Main Content -->
            <div class="bg-neutral-900 rounded-md border border-neutral-800 p-6">
                <livewire:games.game-list />
            </div>
        </div>
    </div>
</x-layouts.app>
