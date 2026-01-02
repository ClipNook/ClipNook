<div class="space-y-8">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-zinc-100 mb-2">{{ __('Privacy & Data Management') }}</h2>
        <p class="text-zinc-400">{{ __('Manage your data, privacy settings, and account deletion') }}</p>
    </div>

    <!-- Data Export -->
    <div class="space-y-6 p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <i class="fa-solid fa-download text-(--color-accent-400) text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Export Your Data') }}</h3>
                <p class="text-sm text-zinc-400 mb-4">{{ __('Download a copy of all your data (GDPR Art. 20)') }}</p>
                <button
                    wire:click="exportData"
                    wire:loading.attr="disabled"
                    class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-(--color-accent-500) to-(--color-accent-400) px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-(--color-accent-500)/25 hover:scale-105 disabled:opacity-50 disabled:hover:scale-100 focus:outline-none focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900"
                >
                    <div wire:loading.remove wire:target="exportData" class="flex items-center gap-2">
                        <i class="fa-solid fa-download"></i>
                        {{ __('Export Data') }}
                    </div>
                    <div wire:loading wire:target="exportData" class="flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        {{ __('Exporting...') }}
                    </div>
                </button>
            </div>
        </div>
    </div>


    <!-- Delete Clips -->
    <div class="space-y-6 p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <i class="fa-solid fa-video text-red-400 text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Delete All Clips') }}</h3>
                <p class="text-sm text-zinc-400 mb-4">{{ __('Permanently delete all clips where you are the broadcaster') }}</p>
                
                <label class="flex items-center gap-3 mb-4">
                    <input
                        type="checkbox"
                        wire:model="confirmDeleteClips"
                        class="size-4 rounded border-zinc-600 bg-zinc-700 text-red-600 focus:ring-red-500"
                    >
                    <span class="text-sm text-zinc-300">{{ __('I confirm that I want to delete all my clips') }}</span>
                </label>
                
                <button
                    wire:click="deleteAllClips"
                    wire:loading.attr="disabled"
                    wire:confirm="{{ __('Are you absolutely sure? This action cannot be undone!') }}"
                    class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-500 px-4 py-2 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-red-500/25 hover:scale-105 disabled:opacity-50 disabled:hover:scale-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-zinc-900"
                >
                    <div wire:loading.remove wire:target="deleteAllClips" class="flex items-center gap-2">
                        <i class="fa-solid fa-trash"></i>
                        {{ __('Delete All Clips') }}
                    </div>
                    <div wire:loading wire:target="deleteAllClips" class="flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        {{ __('Deleting...') }}
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Comments -->
    <div class="space-y-6 p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <i class="fa-solid fa-comments text-red-400 text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Delete All Comments') }}</h3>
                <p class="text-sm text-zinc-400 mb-4">{{ __('Permanently delete all comments you have made') }}</p>
                
                <label class="flex items-center gap-3 mb-4">
                    <input
                        type="checkbox"
                        wire:model="confirmDeleteComments"
                        class="size-4 rounded border-zinc-600 bg-zinc-700 text-red-600 focus:ring-red-500"
                    >
                    <span class="text-sm text-zinc-300">{{ __('I confirm that I want to delete all my comments') }}</span>
                </label>
                
                <button
                    wire:click="deleteAllComments"
                    wire:loading.attr="disabled"
                    wire:confirm="{{ __('Are you absolutely sure? This action cannot be undone!') }}"
                    class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-500 px-4 py-2 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-red-500/25 hover:scale-105 disabled:opacity-50 disabled:hover:scale-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-zinc-900"
                >
                    <div wire:loading.remove wire:target="deleteAllComments" class="flex items-center gap-2">
                        <i class="fa-solid fa-trash"></i>
                        {{ __('Delete All Comments') }}
                    </div>
                    <div wire:loading wire:target="deleteAllComments" class="flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        {{ __('Deleting...') }}
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Ratings -->
    <div class="space-y-6 p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <i class="fa-solid fa-star text-red-400 text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Delete All Ratings') }}</h3>
                <p class="text-sm text-zinc-400 mb-4">{{ __('Permanently delete all ratings you have given') }}</p>
                
                <label class="flex items-center gap-3 mb-4">
                    <input
                        type="checkbox"
                        wire:model="confirmDeleteRatings"
                        class="size-4 rounded border-zinc-600 bg-zinc-700 text-red-600 focus:ring-red-500"
                    >
                    <span class="text-sm text-zinc-300">{{ __('I confirm that I want to delete all my ratings') }}</span>
                </label>
                
                <button
                    wire:click="deleteAllRatings"
                    wire:loading.attr="disabled"
                    wire:confirm="{{ __('Are you absolutely sure? This action cannot be undone!') }}"
                    class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-500 px-4 py-2 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-red-500/25 hover:scale-105 disabled:opacity-50 disabled:hover:scale-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-zinc-900"
                >
                    <div wire:loading.remove wire:target="deleteAllRatings" class="flex items-center gap-2">
                        <i class="fa-solid fa-trash"></i>
                        {{ __('Delete All Ratings') }}
                    </div>
                    <div wire:loading wire:target="deleteAllRatings" class="flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        {{ __('Deleting...') }}
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Account -->
    <div class="rounded-lg bg-red-900/20 border border-red-700/30 p-6 space-y-6 backdrop-blur-sm">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <i class="fa-solid fa-user-xmark text-red-400 text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-red-300 mb-2">{{ __('Delete Account') }}</h3>
                <p class="text-sm text-red-400 mb-4">{{ __('Permanently delete your account and all associated data. This action cannot be undone.') }}</p>
                
                <label class="flex items-center gap-3 mb-6">
                    <input
                        type="checkbox"
                        wire:model="confirmDeleteAccount"
                        class="size-4 rounded border-zinc-600 bg-zinc-700 text-red-600 focus:ring-red-500"
                    >
                    <span class="text-sm font-medium text-red-300">{{ __('I understand that this will permanently delete my account') }}</span>
                </label>
                
                <button
                    wire:click="deleteAccount"
                    wire:loading.attr="disabled"
                    wire:confirm="{{ __('FINAL WARNING: Are you absolutely certain you want to delete your account? This cannot be undone!') }}"
                    class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-700 to-red-600 px-4 py-2 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-red-600/25 hover:scale-105 disabled:opacity-50 disabled:hover:scale-100 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-zinc-900"
                >
                    <div wire:loading.remove wire:target="deleteAccount" class="flex items-center gap-2">
                        <i class="fa-solid fa-user-xmark"></i>
                        {{ __('Delete My Account') }}
                    </div>
                    <div wire:loading wire:target="deleteAccount" class="flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        {{ __('Deleting...') }}
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('download-data', (event) => {
        const data = event.data;
        const blob = new Blob([data], { type: 'application/json' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `clipnook-data-export-${new Date().toISOString()}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    });
</script>
@endscript
