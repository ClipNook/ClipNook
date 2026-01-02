<div class="space-y-8">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-zinc-100 mb-2">{{ __('Privacy & Data Management') }}</h2>
        <p class="text-zinc-400">{{ __('Manage your data, privacy settings, and account deletion') }}</p>
    </div>

    <!-- Data Export -->
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Export Your Data') }}</h3>
            <p class="text-sm text-zinc-400">{{ __('Download a copy of all your data (GDPR Art. 20)') }}</p>
        </div>
        <button
            wire:click="exportData"
            wire:loading.attr="disabled"
            class="rounded-lg bg-(--color-accent-500) px-6 py-3 text-sm font-semibold text-white hover:bg-(--color-accent-400) disabled:opacity-50"
        >
            <i class="fa-solid fa-download mr-2"></i>
            {{ __('Export Data') }}
        </button>
    </div>

    <hr class="border-zinc-800">

    <!-- Delete Clips -->
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Delete All Clips') }}</h3>
            <p class="text-sm text-zinc-400">{{ __('Permanently delete all clips where you are the broadcaster') }}</p>
        </div>
        
        <label class="flex items-center gap-2">
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
            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500 disabled:opacity-50"
        >
            <i class="fa-solid fa-trash mr-2"></i>
            {{ __('Delete All Clips') }}
        </button>
    </div>

    <!-- Delete Comments -->
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Delete All Comments') }}</h3>
            <p class="text-sm text-zinc-400">{{ __('Permanently delete all comments you have made') }}</p>
        </div>
        
        <label class="flex items-center gap-2">
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
            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500 disabled:opacity-50"
        >
            <i class="fa-solid fa-trash mr-2"></i>
            {{ __('Delete All Comments') }}
        </button>
    </div>

    <!-- Delete Ratings -->
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Delete All Ratings') }}</h3>
            <p class="text-sm text-zinc-400">{{ __('Permanently delete all ratings you have given') }}</p>
        </div>
        
        <label class="flex items-center gap-2">
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
            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500 disabled:opacity-50"
        >
            <i class="fa-solid fa-trash mr-2"></i>
            {{ __('Delete All Ratings') }}
        </button>
    </div>

    <hr class="border-zinc-800">

    <!-- Delete Account -->
    <div class="rounded-lg bg-red-900/20 border border-red-700/30 p-6 space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-red-300 mb-2">{{ __('Delete Account') }}</h3>
            <p class="text-sm text-red-400">{{ __('Permanently delete your account and all associated data. This action cannot be undone.') }}</p>
        </div>
        
        <label class="flex items-center gap-2">
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
            class="rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600 disabled:opacity-50"
        >
            <i class="fa-solid fa-user-xmark mr-2"></i>
            {{ __('Delete My Account') }}
        </button>
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
