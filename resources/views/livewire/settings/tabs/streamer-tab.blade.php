<div class="space-y-8">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-zinc-100 mb-2">{{ __('Streamer Settings') }}</h2>
        <p class="text-zinc-400">{{ __('Manage your streamer mode and clip submission settings') }}</p>
    </div>

    <!-- Streamer Status Toggle -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-zinc-200">{{ __('Streamer Mode') }}</h3>
                <p class="text-sm text-zinc-400">{{ __('Enable streamer features and clip management') }}</p>
            </div>
            <button
                wire:click="toggleStreamerStatus"
                type="button"
                @class([
                    'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2',
                    'bg-(--color-accent-500)' => $isStreamer,
                    'bg-zinc-700' => !$isStreamer,
                ])
            >
                <span
                    @class([
                        'pointer-events-none inline-block size-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                        'translate-x-5' => $isStreamer,
                        'translate-x-0' => !$isStreamer,
                    ])
                ></span>
            </button>
        </div>
    </div>

    @if($isStreamer)
    <!-- Clip Submission Permissions -->
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Clip Submission Permissions') }}</h3>
            <p class="text-sm text-zinc-400">{{ __('Control who can submit clips for your channel') }}</p>
        </div>

        <div class="space-y-3">
            <!-- Everyone -->
            <label class="flex items-center">
                <input
                    type="radio"
                    wire:model="clipSubmissionPermission"
                    value="everyone"
                    class="size-4 border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)"
                >
                <span class="ml-3">
                    <span class="block text-sm font-medium text-zinc-200">{{ __('Everyone') }}</span>
                    <span class="block text-sm text-zinc-400">{{ __('Anyone can submit clips') }}</span>
                </span>
            </label>

            <!-- Followers -->
            <label class="flex items-center">
                <input
                    type="radio"
                    wire:model="clipSubmissionPermission"
                    value="followers"
                    class="size-4 border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)"
                >
                <span class="ml-3">
                    <span class="block text-sm font-medium text-zinc-200">{{ __('Followers Only') }}</span>
                    <span class="block text-sm text-zinc-400">{{ __('Only your Twitch followers can submit clips') }}</span>
                </span>
            </label>

            <!-- Subscribers -->
            <label class="flex items-center">
                <input
                    type="radio"
                    wire:model="clipSubmissionPermission"
                    value="subscribers"
                    class="size-4 border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)"
                >
                <span class="ml-3">
                    <span class="block text-sm font-medium text-zinc-200">{{ __('Subscribers Only') }}</span>
                    <span class="block text-sm text-zinc-400">{{ __('Only your Twitch subscribers can submit clips') }}</span>
                </span>
            </label>

            <!-- None -->
            <label class="flex items-center">
                <input
                    type="radio"
                    wire:model="clipSubmissionPermission"
                    value="none"
                    class="size-4 border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)"
                >
                <span class="ml-3">
                    <span class="block text-sm font-medium text-zinc-200">{{ __('Disabled') }}</span>
                    <span class="block text-sm text-zinc-400">{{ __('No one can submit clips') }}</span>
                </span>
            </label>
        </div>

        <div>
            <button
                wire:click="updateClipPermission"
                wire:loading.attr="disabled"
                class="rounded-lg bg-(--color-accent-500) px-6 py-3 text-sm font-semibold text-white hover:bg-(--color-accent-400) disabled:opacity-50"
            >
                <i class="fa-solid fa-save mr-2"></i>
                {{ __('Save Permissions') }}
            </button>
        </div>
    </div>
    @else
    <div class="rounded-lg bg-(--color-accent-900)/20 border border-(--color-accent-500)/30 p-4">
        <p class="text-sm text-zinc-300">{{ __('Enable streamer mode to access clip management features.') }}</p>
    </div>
    @endif
</div>
