<div class="space-y-8">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-zinc-100 mb-2">{{ __('Streamer Settings') }}</h2>
        <p class="text-zinc-400">{{ __('Manage your streamer mode and clip submission settings') }}</p>
    </div>

    <!-- Streamer Status Toggle -->
    <div class="space-y-6">
        <div class="flex items-center justify-between p-4 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
            <div>
                <h3 class="text-lg font-semibold text-zinc-200">{{ __('Streamer Mode') }}</h3>
                <p class="text-sm text-zinc-400">{{ __('Enable streamer features and clip management') }}</p>
            </div>
            <button
                wire:click="toggleStreamerStatus"
                type="button"
                @class([
                    'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900',
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Everyone -->
            <label class="relative">
                <input
                    type="radio"
                    wire:model="clipSubmissionPermission"
                    value="everyone"
                    class="sr-only peer"
                >
                <div class="p-4 bg-zinc-800/30 border border-zinc-700/50 rounded-lg cursor-pointer transition-all peer-checked:border-(--color-accent-500) peer-checked:bg-(--color-accent-900)/20 hover:bg-zinc-800/50">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-5 h-5 border-2 border-zinc-600 rounded-full flex items-center justify-center peer-checked:border-(--color-accent-500)">
                            <div class="w-3 h-3 bg-(--color-accent-500) rounded-full scale-0 peer-checked:scale-100 transition-transform"></div>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-zinc-200">{{ __('Everyone') }}</span>
                            <span class="block text-sm text-zinc-400 mt-1">{{ __('Anyone can submit clips') }}</span>
                        </div>
                    </div>
                </div>
            </label>

            <!-- Followers -->
            <label class="relative">
                <input
                    type="radio"
                    wire:model="clipSubmissionPermission"
                    value="followers"
                    class="sr-only peer"
                >
                <div class="p-4 bg-zinc-800/30 border border-zinc-700/50 rounded-lg cursor-pointer transition-all peer-checked:border-(--color-accent-500) peer-checked:bg-(--color-accent-900)/20 hover:bg-zinc-800/50">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-5 h-5 border-2 border-zinc-600 rounded-full flex items-center justify-center peer-checked:border-(--color-accent-500)">
                            <div class="w-3 h-3 bg-(--color-accent-500) rounded-full scale-0 peer-checked:scale-100 transition-transform"></div>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-zinc-200">{{ __('Followers Only') }}</span>
                            <span class="block text-sm text-zinc-400 mt-1">{{ __('Only your Twitch followers can submit clips') }}</span>
                        </div>
                    </div>
                </div>
            </label>

            <!-- Subscribers -->
            <label class="relative">
                <input
                    type="radio"
                    wire:model="clipSubmissionPermission"
                    value="subscribers"
                    class="sr-only peer"
                >
                <div class="p-4 bg-zinc-800/30 border border-zinc-700/50 rounded-lg cursor-pointer transition-all peer-checked:border-(--color-accent-500) peer-checked:bg-(--color-accent-900)/20 hover:bg-zinc-800/50">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-5 h-5 border-2 border-zinc-600 rounded-full flex items-center justify-center peer-checked:border-(--color-accent-500)">
                            <div class="w-3 h-3 bg-(--color-accent-500) rounded-full scale-0 peer-checked:scale-100 transition-transform"></div>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-zinc-200">{{ __('Subscribers Only') }}</span>
                            <span class="block text-sm text-zinc-400 mt-1">{{ __('Only your Twitch subscribers can submit clips') }}</span>
                        </div>
                    </div>
                </div>
            </label>

            <!-- None -->
            <label class="relative">
                <input
                    type="radio"
                    wire:model="clipSubmissionPermission"
                    value="none"
                    class="sr-only peer"
                >
                <div class="p-4 bg-zinc-800/30 border border-zinc-700/50 rounded-lg cursor-pointer transition-all peer-checked:border-red-500 peer-checked:bg-red-900/20 hover:bg-zinc-800/50">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-5 h-5 border-2 border-zinc-600 rounded-full flex items-center justify-center peer-checked:border-red-500">
                            <div class="w-3 h-3 bg-red-500 rounded-full scale-0 peer-checked:scale-100 transition-transform"></div>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-zinc-200">{{ __('Disabled') }}</span>
                            <span class="block text-sm text-zinc-400 mt-1">{{ __('No one can submit clips') }}</span>
                        </div>
                    </div>
                </div>
            </label>
        </div>

        <div class="flex justify-end">
            <button
                wire:click="updateClipPermission"
                wire:loading.attr="disabled"
                class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-(--color-accent-500) to-(--color-accent-400) px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-(--color-accent-500)/25 hover:scale-105 disabled:opacity-50 disabled:hover:scale-100 focus:outline-none focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900"
            >
                <div wire:loading.remove wire:target="updateClipPermission" class="flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    {{ __('Save Permissions') }}
                </div>
                <div wire:loading wire:target="updateClipPermission" class="flex items-center gap-2">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    {{ __('Saving...') }}
                </div>
            </button>
        </div>
    </div>
    @else
    <div class="rounded-lg bg-zinc-800/30 border border-zinc-700/50 p-4 backdrop-blur-sm">
        <div class="flex gap-3">
            <div class="shrink-0">
                <i class="fa-solid fa-video text-(--color-accent-400) text-xl"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm text-zinc-300">{{ __('Enable streamer mode to access clip management features.') }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
