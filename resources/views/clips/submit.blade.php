<x-layouts.app title="{{ __('clips.submit_page_title') }}">
    <div class="min-h-screen bg-zinc-950 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8 border-b border-zinc-800/50 bg-zinc-900/80 backdrop-blur-md rounded-lg p-6">

                <div class="flex items-center gap-4">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 bg-zinc-800 border border-(--color-accent-500)/50 rounded-lg">
                        <i class="fa-solid fa-plus text-xl text-(--color-accent-400)"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold text-zinc-100">{{ __('clips.submit_page_title') }}</h1>
                        <!-- Subtle accent border at top -->
                        <div class="h-px bg-linear-to-r from-(--color-accent-500)/30 to-transparent my-2"></div>
                        <p class="text-sm text-zinc-400">{{ __('clips.submit_page_subtitle') }}</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="border border-zinc-800 rounded-lg p-6 bg-zinc-900/50">
                <livewire:clips.submit-clip />
            </div>
        </div>
    </div>
</x-layouts.app>
