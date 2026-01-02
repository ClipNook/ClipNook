<div class="space-y-8">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-zinc-100 mb-2">{{ __('Streamer Settings') }}</h2>
        <p class="text-zinc-400">{{ __('Manage your streamer mode and clip submission settings') }}</p>
    </div>

    <!-- Streamer Status Toggle -->
    <div class="space-y-6 p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="shrink-0">
                    <i class="fa-solid fa-toggle-on text-(--color-accent-400) text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-zinc-200">{{ __('Streamer Mode') }}</h3>
                    <p class="text-sm text-zinc-400">{{ __('Enable streamer features and clip management') }}</p>
                </div>
            </div>
            <button
                wire:click="toggleStreamerStatus"
                type="button"
                @class([
                    'relative inline-flex h-7 w-14 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900',
                    'bg-(--color-accent-500)' => $isStreamer,
                    'bg-zinc-700 hover:bg-zinc-600' => !$isStreamer,
                ])
                title="{{ $isStreamer ? __('Disable streamer mode') : __('Enable streamer mode') }}"
            >
                <span
                    @class([
                        'pointer-events-none inline-block size-6 transform rounded-full bg-white shadow-lg ring-0 transition duration-300 ease-in-out',
                        'translate-x-7' => $isStreamer,
                        'translate-x-0' => !$isStreamer,
                    ])
                ></span>
            </button>
        </div>
    </div>

    @if($isStreamer)
    <!-- Individual Permissions -->
    <div class="mt-8">
        <livewire:settings.streamer-settings />
    </div>
    @else
    <div class="space-y-6 p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
        <div class="flex items-center gap-4">
            <div class="shrink-0">
                <i class="fa-solid fa-video text-(--color-accent-400) text-2xl"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-lg font-semibold text-zinc-300 mb-1">{{ __('settings.streamer_mode_required') }}</h4>
                <p class="text-sm text-zinc-400">{{ __('settings.enable_streamer_mode_first') }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
