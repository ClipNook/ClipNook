<div class="space-y-8">
    <div>
        <h2 class="text-2xl font-bold text-zinc-100 mb-2">{{ __('Twitch Account Information') }}</h2>
        <p class="text-zinc-400">{{ __('Your connected Twitch account details') }}</p>
    </div>

    <!-- Twitch User Info -->
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Twitch ID -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-300">{{ __('Twitch ID') }}</label>
                <div class="relative">
                    <input
                        type="text"
                        value="{{ $user->twitch_id }}"
                        disabled
                        class="block w-full rounded-lg border-0 bg-zinc-800/50 px-4 py-3 text-zinc-400 shadow-sm ring-1 ring-zinc-700/50 focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900"
                    >
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fa-solid fa-hashtag text-zinc-500 text-sm"></i>
                    </div>
                </div>
            </div>

            <!-- Twitch Login -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-300">{{ __('Twitch Username') }}</label>
                <div class="relative">
                    <input
                        type="text"
                        value="{{ $user->twitch_login }}"
                        disabled
                        class="block w-full rounded-lg border-0 bg-zinc-800/50 px-4 py-3 text-zinc-400 shadow-sm ring-1 ring-zinc-700/50 focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900"
                    >
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fa-brands fa-twitch text-zinc-500 text-sm"></i>
                    </div>
                </div>
            </div>

            <!-- Display Name -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-300">{{ __('Display Name') }}</label>
                <div class="relative">
                    <input
                        type="text"
                        value="{{ $user->twitch_display_name }}"
                        disabled
                        class="block w-full rounded-lg border-0 bg-zinc-800/50 px-4 py-3 text-zinc-400 shadow-sm ring-1 ring-zinc-700/50 focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900"
                    >
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fa-solid fa-user text-zinc-500 text-sm"></i>
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-300">{{ __('Email') }}</label>
                <div class="relative">
                    <input
                        type="email"
                        value="{{ mask_email($user->twitch_email) }}"
                        disabled
                        class="block w-full rounded-lg border-0 bg-zinc-800/50 px-4 py-3 text-zinc-400 shadow-sm ring-1 ring-zinc-700/50 focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900"
                    >
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fa-solid fa-envelope text-zinc-500 text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Token Expiration -->
        @if($user->twitch_token_expires_at)
        <div class="space-y-2">
            <label class="block text-sm font-medium text-zinc-300">{{ __('Token Expires At') }}</label>
            <div class="relative">
                <input
                    type="text"
                    value="{{ $user->twitch_token_expires_at->format('Y-m-d H:i:s') }}"
                    disabled
                    class="block w-full rounded-lg border-0 bg-zinc-800/50 px-4 py-3 text-zinc-400 shadow-sm ring-1 ring-zinc-700/50 focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900"
                >
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fa-solid fa-clock text-zinc-500 text-sm"></i>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="rounded-lg bg-zinc-800/30 border border-zinc-700/50 p-4 backdrop-blur-sm">
        <div class="flex gap-3">
            <div class="shrink-0">
                <i class="fa-solid fa-circle-info text-(--color-accent-400) text-xl"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm text-zinc-300">
                    {{ __('These fields are synchronized from your Twitch account. To change them, update your information on Twitch and click the sync button below.') }}
                </p>
                @if($lastSyncTime)
                <p class="text-xs text-zinc-400 mt-2 flex items-center gap-2">
                    <i class="fa-solid fa-clock"></i>
                    {{ __('Last synchronized: :time', ['time' => $lastSyncTime]) }}
                </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Sync Button -->
    <div class="space-y-3">
        <button
            wire:click="syncTwitchData"
            wire:loading.attr="disabled"
            @if(!$canSync) disabled @endif
            class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-(--color-accent-500) to-(--color-accent-400) px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-(--color-accent-500)/25 hover:scale-105 disabled:opacity-50 disabled:hover:scale-100 focus:outline-none focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900 {{ !$canSync ? 'bg-zinc-600 cursor-not-allowed' : '' }}"
        >
            <i class="fa-solid fa-sync" wire:loading.class="fa-spin" wire:target="syncTwitchData"></i>
            <span wire:loading.remove wire:target="syncTwitchData">
                @if($canSync)
                    {{ __('Sync from Twitch') }}
                @else
                    {{ __('Sync available :time', ['time' => $nextSyncTime]) }}
                @endif
            </span>
            <span wire:loading wire:target="syncTwitchData">{{ __('Syncing...') }}</span>
        </button>

        @if(!$canSync)
        <p class="text-xs text-zinc-400 flex items-center gap-2">
            <i class="fa-solid fa-info-circle"></i>
            {{ __('Rate limited to prevent spam. Next sync available :time.', ['time' => $nextSyncTime]) }}
        </p>
        @endif
    </div>
</div>
