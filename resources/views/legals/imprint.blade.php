<x-layouts.app title="{{ config('legals.imprint.title') }}">
<div class="min-h-screen bg-zinc-950">
    <!-- Page Header -->
    <div class="bg-zinc-900/80 backdrop-blur-md border-b border-zinc-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex flex-col items-center gap-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-(--color-accent-900)/20 border-2 border-(--color-accent-500) rounded-xl">
                    <i class="fa-solid fa-building text-3xl text-(--color-accent-400)"></i>
                </div>
                <div class="text-center space-y-4">
                    <h1 class="text-4xl sm:text-5xl font-bold text-zinc-100">{{ config('legals.imprint.title') }}</h1>
                    <p class="text-lg text-zinc-400 max-w-2xl">{{ __('legals.imprint_subtitle') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="space-y-8">
            {!! $content !!}
        </div>
    </div>
</div>
</x-layouts.app>