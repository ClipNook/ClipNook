<!-- Cookie Banner Component -->
<div class="fixed bottom-0 left-0 right-0 z-50 bg-gray-950 border-t border-gray-800 p-4"
    x-show="$wire.showBanner" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="transform translate-y-full" x-transition:enter-end="transform translate-y-0"
    x-transition:leave="transition ease-in duration-300" x-transition:leave-start="transform translate-y-0"
    x-transition:leave-end="transform translate-y-full" style="display: none;">
    <!-- Main Banner -->
    <div class="max-w-4xl mx-auto">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-100 mb-2">
                    <i class="fas fa-cookie-bite mr-2 text-purple-400"></i>
                    {{ __('cookies.title') }}
                </h3>
                <p class="text-gray-300 text-sm">
                    {{ __('cookies.description') }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-2">
                <button wire:click="accept"
                    class="bg-purple-400 text-gray-900 px-4 py-2 rounded text-sm font-medium">
                    {{ __('cookies.accept') }}
                </button>
            </div>
        </div>
    </div>
</div>
