<div class="space-y-8">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-zinc-100 mb-2">{{ __('Notification Settings') }}</h2>
        <p class="text-zinc-400">{{ __('Manage how and when you receive notifications') }}</p>
    </div>

    <!-- Email Notifications -->
    <div class="space-y-6 p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <i class="fa-solid fa-envelope text-(--color-accent-400) text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Email Notifications') }}</h3>
                <p class="text-sm text-zinc-400 mb-4">{{ __('Choose what email notifications you want to receive') }}</p>

                <div class="space-y-4">
                    <!-- Clip Approved Notifications -->
                    <label class="flex items-center justify-between p-4 bg-zinc-900/50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-zinc-200">{{ __('Clip Approved') }}</div>
                            <div class="text-xs text-zinc-400">{{ __('Get notified when your clips are approved') }}</div>
                        </div>
                        <input
                            type="checkbox"
                            wire:model.live="email_on_clip_approved"
                            class="size-5 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)"
                        >
                    </label>

                    <!-- Clip Rejected Notifications -->
                    <label class="flex items-center justify-between p-4 bg-zinc-900/50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-zinc-200">{{ __('Clip Rejected') }}</div>
                            <div class="text-xs text-zinc-400">{{ __('Get notified when your clips are rejected') }}</div>
                        </div>
                        <input
                            type="checkbox"
                            wire:model.live="email_on_clip_rejected"
                            class="size-5 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)"
                        >
                    </label>

                    <!-- Comment Notifications -->
                    <label class="flex items-center justify-between p-4 bg-zinc-900/50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-zinc-200">{{ __('New Comments') }}</div>
                            <div class="text-xs text-zinc-400">{{ __('Get notified when someone comments on your clips') }}</div>
                        </div>
                        <input
                            type="checkbox"
                            wire:model.live="email_on_new_comments"
                            class="size-5 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)"
                        >
                    </label>

                    <!-- Featured Clip Notifications -->
                    <label class="flex items-center justify-between p-4 bg-zinc-900/50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-zinc-200">{{ __('Featured Clip') }}</div>
                            <div class="text-xs text-zinc-400">{{ __('Get notified when your clip is featured') }}</div>
                        </div>
                        <input
                            type="checkbox"
                            wire:model.live="email_on_featured_clip"
                            class="size-5 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)"
                        >
                    </label>

                    <!-- Weekly Digest -->
                    <label class="flex items-center justify-between p-4 bg-zinc-900/50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-zinc-200">{{ __('Weekly Digest') }}</div>
                            <div class="text-xs text-zinc-400">{{ __('Receive a weekly summary of activity') }}</div>
                        </div>
                        <input
                            type="checkbox"
                            wire:model.live="email_weekly_digest"
                            class="size-5 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)"
                        >
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Push Notifications -->
    <div class="space-y-6 p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <i class="fa-solid fa-bell text-(--color-accent-400) text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Push Notifications') }}</h3>
                <p class="text-sm text-zinc-400 mb-4">{{ __('Browser push notifications (coming soon)') }}</p>

                <div class="p-4 bg-zinc-900/50 rounded-lg">
                    <div class="text-sm text-zinc-400 text-center">
                        <i class="fa-solid fa-info-circle mr-2"></i>
                        {{ __('Push notifications are not yet implemented') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Frequency -->
    <div class="space-y-6 p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <i class="fa-solid fa-clock text-(--color-accent-400) text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('Notification Frequency') }}</h3>
                <p class="text-sm text-zinc-400 mb-4">{{ __('How often you want to receive notifications') }}</p>

                <div class="p-4 bg-zinc-900/50 rounded-lg">
                    <div class="text-sm text-zinc-400 text-center">
                        <i class="fa-solid fa-info-circle mr-2"></i>
                        {{ __('Advanced frequency settings coming soon') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end pt-6 border-t border-zinc-700/50">
        <button
            wire:click="updateNotifications"
            wire:loading.attr="disabled"
            class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-(--color-accent-500) to-(--color-accent-400) px-8 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-(--color-accent-500)/25 hover:scale-105 disabled:opacity-50 disabled:hover:scale-100 focus:outline-none focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900"
        >
            <div wire:loading.remove wire:target="updateNotifications" class="flex items-center gap-2">
                <i class="fa-solid fa-save"></i>
                {{ __('Save Settings') }}
            </div>
            <div wire:loading wire:target="updateNotifications" class="flex items-center gap-2">
                <i class="fa-solid fa-spinner fa-spin"></i>
                {{ __('Saving...') }}
            </div>
        </button>
    </div>


</div>