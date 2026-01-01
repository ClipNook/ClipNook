<!-- Cookie Banner Component -->
<div class="fixed bottom-0 left-0 right-0 z-50 bg-zinc-900 border-t border-zinc-800 p-4"
    x-show="$wire.showBanner" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0"
    x-transition:leave-end="translate-y-full" style="display: none;">
    <!-- Main Banner -->
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex-1">
                <h3 class="text-base font-semibold text-white mb-1">
                    <i class="fas fa-cookie-bite mr-1.5 text-violet-400" aria-hidden="true"></i>
                    {{ __('cookies.title') }}
                </h3>
                <p class="text-zinc-400 text-sm">
                    {{ __('cookies.description') }}
                </p>
            </div>

            <div class="flex gap-2">
                <button wire:click="accept"
                    class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors focus:outline-none whitespace-nowrap"
                    aria-label="{{ __('cookies.accept') }}">
                    {{ __('cookies.accept') }}
                </button>
            </div>
        </div>
    </div>
</div>
