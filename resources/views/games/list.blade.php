<x-layouts.app title="{{ __('games.list_page_title') }}">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-zinc-950">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8 border-b border-zinc-800/50 bg-zinc-900/80 backdrop-blur-md rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-zinc-800 border border-(--color-accent-500)/50 rounded-lg">
                            <i class="fa-solid fa-gamepad text-xl text-(--color-accent-400)"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-zinc-100">{{ __('games.list_page_title') }}</h1>
                            <!-- Subtle accent border at top -->
                            <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500)/30 to-transparent my-2"></div>
                            <p class="text-sm text-zinc-400">{{ __('games.list_page_subtitle') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mt-6 p-4 bg-green-900/50 border border-green-800 rounded-lg">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-check-circle text-green-400 mt-0.5"></i>
                            <span class="text-green-200 text-sm">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mt-6 p-4 bg-red-900/50 border border-red-800 rounded-lg">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation text-red-400 mt-0.5"></i>
                            <span class="text-red-200 text-sm">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Content -->
            <div class="border border-zinc-800 rounded-lg p-6 bg-zinc-900/50">
                <livewire:games.game-list />
            </div>
        </div>
    </div>
</x-layouts.app>
