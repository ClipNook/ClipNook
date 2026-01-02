<div class="space-y-8">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-zinc-100 mb-2">{{ __('Sessions & Tokens') }}</h2>
        <p class="text-zinc-400">{{ __('Manage your API tokens and active browser sessions') }}</p>
    </div>

    <!-- API Tokens -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-zinc-200">{{ __('API Tokens') }}</h3>
                <p class="text-sm text-zinc-400">{{ __('Manage your API access tokens') }}</p>
            </div>
            @if($tokens->isNotEmpty())
            <button
                wire:click="revokeAllTokens"
                wire:confirm="{{ __('Revoke all other tokens?') }}"
                class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-500 px-4 py-2 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-red-500/25 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-zinc-900"
            >
                <i class="fa-solid fa-trash"></i>
                {{ __('Revoke All Others') }}
            </button>
            @endif
        </div>

        @if($tokens->isEmpty())
        <div class="rounded-lg bg-zinc-800/30 border border-zinc-700/50 p-4 backdrop-blur-sm">
            <div class="flex gap-3">
                <div class="shrink-0">
                    <i class="fa-solid fa-key text-(--color-accent-400) text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-zinc-300">{{ __('No active API tokens') }}</p>
                </div>
            </div>
        </div>
        @else
        <div class="overflow-hidden rounded-lg bg-zinc-900 border border-zinc-800">
            <table class="min-w-full divide-y divide-zinc-800">
                <thead class="bg-zinc-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-400">{{ __('Name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-400">{{ __('Created') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-400">{{ __('Last Used') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-400">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800 bg-zinc-900">
                    @foreach($tokens as $token)
                    <tr class="bg-zinc-800/50">
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-zinc-200">{{ $token->name }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-400">{{ $token->created_at->diffForHumans() }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-400">{{ $token->last_used_at?->diffForHumans() ?? __('Never') }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                            <button
                                wire:click="revokeToken({{ $token->id }})"
                                wire:confirm="{{ __('Revoke this token?') }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-red-600/10 border border-red-600/20 px-3 py-1.5 text-sm font-medium text-red-400 transition-all hover:bg-red-600/20 hover:border-red-600/30 hover:text-red-300"
                            >
                                <i class="fa-solid fa-times"></i>
                                {{ __('Revoke') }}
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Browser Sessions -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-zinc-200">{{ __('Browser Sessions') }}</h3>
                <p class="text-sm text-zinc-400">{{ __('Manage your active browser sessions') }}</p>
            </div>
            @if($sessions->count() > 1)
            <button
                wire:click="logoutOtherDevices"
                wire:confirm="{{ __('Logout from all other devices?') }}"
                class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-500 px-4 py-2 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-red-500/25 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-zinc-900"
            >
                <i class="fa-solid fa-sign-out-alt"></i>
                {{ __('Logout Other Devices') }}
            </button>
            @endif
        </div>

        @if($sessions->isEmpty())
        <div class="rounded-lg bg-zinc-800/30 border border-zinc-700/50 p-4 backdrop-blur-sm">
            <div class="flex gap-3">
                <div class="shrink-0">
                    <i class="fa-solid fa-desktop text-(--color-accent-400) text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-zinc-300">{{ __('No active sessions') }}</p>
                </div>
            </div>
        </div>
        @else
        <div class="space-y-3">
            @foreach($sessions as $session)
            <div @class([
                'rounded-lg border p-4',
                'border-(--color-accent-500)/30 bg-(--color-accent-900)/20' => $session->is_current,
                'border-zinc-700 bg-zinc-800/50' => !$session->is_current,
            ])>
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-desktop text-zinc-400"></i>
                            <span class="text-sm font-medium text-zinc-200">{{ $session->user_agent }}</span>
                            @if($session->is_current)
                            <span class="rounded-full bg-(--color-accent-500)/20 border border-(--color-accent-500)/30 px-2 py-0.5 text-xs font-medium text-(--color-accent-300)">{{ __('Current') }}</span>
                            @endif
                        </div>
                        <div class="mt-1 text-sm text-zinc-400">
                            <i class="fa-solid fa-location-dot mr-1"></i>
                            {{ $session->ip_address }}
                            <span class="mx-2">•</span>
                            <i class="fa-solid fa-clock mr-1"></i>
                            {{ __('Last active') }}: {{ $session->last_activity->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
