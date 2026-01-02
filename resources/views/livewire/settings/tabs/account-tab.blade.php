<div class="space-y-8">
    <div>
        <h2 class="text-2xl font-bold text-zinc-100 mb-2">{{ __('Twitch Account Information') }}</h2>
        <p class="text-zinc-400">{{ __('Your connected Twitch account details') }}</p>
    </div>

    <!-- Twitch User Info -->
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Twitch ID -->
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">{{ __('Twitch ID') }}</label>
                <input
                    type="text"
                    value="{{ $user->twitch_id }}"
                    disabled
                    class="block w-full rounded-lg border-0 bg-zinc-700/50 px-4 py-3 text-zinc-400 shadow-sm"
                >
            </div>

            <!-- Twitch Login -->
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">{{ __('Twitch Username') }}</label>
                <input
                    type="text"
                    value="{{ $user->twitch_login }}"
                    disabled
                    class="block w-full rounded-lg border-0 bg-zinc-700/50 px-4 py-3 text-zinc-400 shadow-sm"
                >
            </div>

            <!-- Display Name -->
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">{{ __('Display Name') }}</label>
                <input
                    type="text"
                    value="{{ $user->twitch_display_name }}"
                    disabled
                    class="block w-full rounded-lg border-0 bg-zinc-700/50 px-4 py-3 text-zinc-400 shadow-sm"
                >
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">{{ __('Email') }}</label>
                <input
                    type="email"
                    value="{{ $user->twitch_email }}"
                    disabled
                    class="block w-full rounded-lg border-0 bg-zinc-700/50 px-4 py-3 text-zinc-400 shadow-sm"
                >
            </div>
        </div>

        <!-- Token Expiration -->
        @if($user->twitch_token_expires_at)
        <div>
            <label class="block text-sm font-medium text-zinc-300 mb-2">{{ __('Token Expires At') }}</label>
            <input
                type="text"
                value="{{ $user->twitch_token_expires_at->format('Y-m-d H:i:s') }}"
                disabled
                class="block w-full rounded-lg border-0 bg-zinc-700/50 px-4 py-3 text-zinc-400 shadow-sm"
            >
        </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="rounded-lg bg-(--color-accent-900)/20 border border-(--color-accent-500)/30 p-4">
        <div class="flex gap-3">
            <div class="shrink-0">
                <i class="fa-solid fa-circle-info text-(--color-accent-400) text-xl"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm text-zinc-300">
                    {{ __('These fields are synchronized from your Twitch account. To change them, update your information on Twitch and click the sync button below.') }}
                </p>
                @if($lastSyncTime)
                <p class="text-xs text-zinc-400 mt-2">
                    <i class="fa-solid fa-clock mr-1"></i>
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
            class="inline-flex items-center gap-2 rounded-lg px-6 py-3 text-sm font-semibold text-white shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-900 disabled:opacity-50 {{ $canSync ? 'bg-(--color-accent-500) hover:bg-(--color-accent-400) focus:ring-(--color-accent-500)' : 'bg-zinc-600 cursor-not-allowed' }}"
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
