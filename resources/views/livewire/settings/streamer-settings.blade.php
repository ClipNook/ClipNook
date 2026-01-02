<div class="space-y-8">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-zinc-100 mb-2">{{ __('settings.streamer_permissions') }}</h2>
        <p class="text-zinc-400">{{ __('settings.manage_who_can_manage') }}</p>
    </div>

    <!-- Add New Permission -->
    <div class="space-y-6 p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <i class="fa-solid fa-user-plus text-(--color-accent-400) text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('settings.grant_permissions') }}</h3>
                <p class="text-sm text-zinc-400 mb-4">{{ __('settings.add_users_to_grant_access') }}</p>

                <div class="space-y-4">
                    <!-- User Search -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-zinc-200 mb-2">{{ __('Twitch Username') }}</label>
                        <div class="relative">
                            <input type="text" wire:model.live="newUserSearch"
                                placeholder="Search by Twitch username or display name..."
                                class="block w-full rounded-lg border-0 bg-zinc-800/50 px-4 py-3 pr-12 text-zinc-200 shadow-sm ring-1 ring-zinc-700/50 focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class="fa-solid fa-magnifying-glass text-zinc-400"></i>
                            </div>
                        </div>

                        <!-- User Suggestions -->
                        @if ($userSuggestions->isNotEmpty())
                            <div
                                class="absolute z-10 mt-1 w-full bg-zinc-800 border border-zinc-700 rounded-lg shadow-lg max-h-60 overflow-auto">
                                @foreach ($userSuggestions as $suggestion)
                                    <button wire:click="selectUser({{ $suggestion->id }})" type="button"
                                        class="w-full px-4 py-3 text-left hover:bg-zinc-700/50 focus:bg-zinc-700/50 focus:outline-none transition-colors">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $suggestion->getAvatarSourceAttribute() }}"
                                                alt="{{ $suggestion->twitch_display_name }}"
                                                class="w-8 h-8 rounded-full">
                                            <div>
                                                <div class="text-sm font-medium text-zinc-200">
                                                    {{ $suggestion->twitch_display_name ?? $suggestion->twitch_login }}
                                                </div>
                                                <div class="text-xs text-zinc-400">
                                                    {{ $suggestion->twitch_login }}
                                                </div>
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @error('newUserSearch')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Permission Checkboxes -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-200 mb-3">{{ __('settings.permissions') }}</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label
                                class="flex items-center gap-3 p-3 bg-zinc-900/50 rounded-lg cursor-pointer hover:bg-zinc-800/50 transition-colors">
                                <input type="checkbox" wire:model="selectedPermissions.can_submit_clips"
                                    class="size-4 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)">
                                <div>
                                    <div class="text-sm font-medium text-zinc-200">{{ __('settings.submit') }}</div>
                                    <div class="text-xs text-zinc-400">{{ __('settings.upload_clips') }}</div>
                                </div>
                            </label>

                            <label
                                class="flex items-center gap-3 p-3 bg-zinc-900/50 rounded-lg cursor-pointer hover:bg-zinc-800/50 transition-colors">
                                <input type="checkbox" wire:model="selectedPermissions.can_edit_clips"
                                    class="size-4 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)">
                                <div>
                                    <div class="text-sm font-medium text-zinc-200">{{ __('settings.edit') }}</div>
                                    <div class="text-xs text-zinc-400">{{ __('settings.modify_clips') }}</div>
                                </div>
                            </label>

                            <label
                                class="flex items-center gap-3 p-3 bg-zinc-900/50 rounded-lg cursor-pointer hover:bg-zinc-800/50 transition-colors">
                                <input type="checkbox" wire:model="selectedPermissions.can_delete_clips"
                                    class="size-4 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)">
                                <div>
                                    <div class="text-sm font-medium text-zinc-200">{{ __('settings.delete') }}</div>
                                    <div class="text-xs text-zinc-400">{{ __('settings.remove_clips') }}</div>
                                </div>
                            </label>

                            <label
                                class="flex items-center gap-3 p-3 bg-zinc-900/50 rounded-lg cursor-pointer hover:bg-zinc-800/50 transition-colors">
                                <input type="checkbox" wire:model="selectedPermissions.can_moderate_clips"
                                    class="size-4 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)">
                                <div>
                                    <div class="text-sm font-medium text-zinc-200">{{ __('settings.moderate') }}</div>
                                    <div class="text-xs text-zinc-400">{{ __('settings.review_content') }}</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Add Button -->
                    <div class="flex justify-end">
                        <button wire:click="addPermission" wire:loading.attr="disabled"
                            class="group relative inline-flex items-center gap-2 rounded-xl bg-linear-to-r from-(--color-accent-500) to-(--color-accent-400) px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-(--color-accent-500)/25 hover:scale-105 disabled:opacity-50 disabled:hover:scale-100 focus:outline-none focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900">
                            <div wire:loading.remove wire:target="addPermission" class="flex items-center gap-2">
                                <i class="fa-solid fa-plus"></i>
                                {{ __('Add Permission') }}
                            </div>
                            <div wire:loading wire:target="addPermission" class="flex items-center gap-2">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                {{ __('Adding...') }}
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Existing Permissions -->
    <div class="space-y-6 p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <i class="fa-solid fa-users-gear text-(--color-accent-400) text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-zinc-200 mb-2">{{ __('settings.current_permissions') }}</h3>
                <p class="text-sm text-zinc-400 mb-6">{{ __('settings.users_with_permissions') }}</p>

                <!-- All Users Permission Card -->
                <div class="mb-6 p-5 bg-zinc-900/50 rounded-lg border border-zinc-700/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-zinc-800 rounded-full border-2 border-zinc-600">
                                <i class="fa-solid fa-globe text-zinc-400 text-lg"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-base font-semibold text-zinc-100">{{ __('settings.all_users') }}</h4>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                        <i class="fa-solid fa-star mr-1 text-[10px]"></i>
                                        {{ __('settings.global_permission') }}
                                    </span>
                                </div>
                                <p class="text-sm text-zinc-400 mt-1">{{ __('settings.public_clip_submission') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <!-- Permission Badge -->
                            @if($this->getAllUsersSubmissionEnabled())
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-green-600/20 text-green-400 border border-green-600/30">
                                    <i class="fa-solid fa-upload"></i>
                                    {{ __('settings.submit') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-zinc-700/30 text-zinc-500 border border-zinc-700">
                                    <i class="fa-solid fa-ban"></i>
                                    {{ __('settings.disabled') }}
                                </span>
                            @endif

                            <!-- Toggle Button -->
                            <button
                                wire:click="toggleAllUsersSubmission"
                                type="button"
                                @class([
                                    'relative inline-flex h-7 w-14 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900',
                                    'bg-(--color-accent-500)' => $this->getAllUsersSubmissionEnabled(),
                                    'bg-zinc-700' => !$this->getAllUsersSubmissionEnabled(),
                                ])
                                title="{{ $this->getAllUsersSubmissionEnabled() ? __('settings.disable_public_submission') : __('settings.enable_public_submission') }}"
                            >
                                <span
                                    @class([
                                        'pointer-events-none inline-block size-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                        'translate-x-7' => $this->getAllUsersSubmissionEnabled(),
                                        'translate-x-0' => !$this->getAllUsersSubmissionEnabled(),
                                    ])
                                ></span>
                            </button>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($this->getAllUsersSubmissionEnabled())
                        <div class="mt-4 flex items-start gap-3 p-3 bg-green-600/10 border border-green-600/20 rounded-lg">
                            <i class="fa-solid fa-circle-check text-green-400 mt-0.5"></i>
                            <p class="text-sm text-zinc-300">{{ __('settings.all_users_can_submit_desc') }}</p>
                        </div>
                    @else
                        <div class="mt-4 flex items-start gap-3 p-3 bg-zinc-700/20 border border-zinc-700/30 rounded-lg">
                            <i class="fa-solid fa-circle-info text-zinc-400 mt-0.5"></i>
                            <p class="text-sm text-zinc-400">{{ __('settings.public_submission_disabled_desc') }}</p>
                        </div>
                    @endif
                </div>

                @if ($permissions->isEmpty())
                    <div class="text-center py-12">
                        <div class="relative mb-6">
                            <div class="absolute -inset-4 bg-gradient-to-r from-zinc-500/20 to-zinc-400/20 rounded-full blur opacity-50"></div>
                            <div class="relative flex items-center justify-center w-20 h-20 bg-zinc-800/50 rounded-full border border-zinc-600/30 mx-auto">
                                <i class="fa-solid fa-users-slash text-zinc-500 text-3xl"></i>
                            </div>
                        </div>
                        <h4 class="text-lg font-semibold text-zinc-300 mb-2">{{ __('settings.no_permissions_yet') }}</h4>
                        <p class="text-zinc-400 max-w-md mx-auto">{{ __('settings.add_users_above_to_grant_access') }}</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($permissions as $permission)
                            <div class="p-4 bg-zinc-900/50 rounded-lg border border-zinc-700/30">
                                @if ($editingPermissionId === $permission->id)
                                    <!-- Edit Mode -->
                                    <div class="space-y-4">
                                        <div class="flex items-center gap-3 mb-4">
                                            <img src="{{ $permission->user->getAvatarSourceAttribute() }}"
                                                alt="{{ $permission->user->twitch_display_name }}"
                                                class="w-10 h-10 rounded-full">
                                            <div>
                                                <div class="text-sm font-medium text-zinc-200">
                                                    {{ $permission->user->twitch_display_name ?? $permission->user->twitch_login }}
                                                </div>
                                                <div class="text-xs text-zinc-400">@{{ $permission->user->twitch_login }}</div>
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-zinc-200 mb-3">{{ __('settings.edit_permissions_action') }}</label>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                <label
                                                    class="flex items-center gap-3 p-3 bg-zinc-800/50 rounded-lg cursor-pointer hover:bg-zinc-700/50 transition-colors">
                                                    <input type="checkbox"
                                                        wire:model="editingPermissions.can_submit_clips"
                                                        class="size-4 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)">
                                                    <div>
                                                        <div class="text-sm font-medium text-zinc-200">
                                                            {{ __('settings.submit') }}</div>
                                                    </div>
                                                </label>

                                                <label
                                                    class="flex items-center gap-3 p-3 bg-zinc-800/50 rounded-lg cursor-pointer hover:bg-zinc-700/50 transition-colors">
                                                    <input type="checkbox"
                                                        wire:model="editingPermissions.can_edit_clips"
                                                        class="size-4 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)">
                                                    <div>
                                                        <div class="text-sm font-medium text-zinc-200">
                                                            {{ __('settings.edit') }}</div>
                                                    </div>
                                                </label>

                                                <label
                                                    class="flex items-center gap-3 p-3 bg-zinc-800/50 rounded-lg cursor-pointer hover:bg-zinc-700/50 transition-colors">
                                                    <input type="checkbox"
                                                        wire:model="editingPermissions.can_delete_clips"
                                                        class="size-4 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)">
                                                    <div>
                                                        <div class="text-sm font-medium text-zinc-200">
                                                            {{ __('settings.delete') }}</div>
                                                    </div>
                                                </label>

                                                <label
                                                    class="flex items-center gap-3 p-3 bg-zinc-800/50 rounded-lg cursor-pointer hover:bg-zinc-700/50 transition-colors">
                                                    <input type="checkbox"
                                                        wire:model="editingPermissions.can_moderate_clips"
                                                        class="size-4 rounded border-zinc-600 bg-zinc-700 text-(--color-accent-500) focus:ring-(--color-accent-500)">
                                                    <div>
                                                        <div class="text-sm font-medium text-zinc-200">
                                                            {{ __('settings.moderate') }}</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="flex gap-2 justify-end">
                                            <button wire:click="updatePermission"
                                                class="inline-flex items-center gap-2 rounded-lg bg-(--color-accent-500) px-4 py-2 text-sm font-medium text-white hover:bg-(--color-accent-400) focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900">
                                                <i class="fa-solid fa-save"></i>
                                                {{ __('settings.save') }}
                                            </button>
                                            <button wire:click="cancelEditing"
                                                class="inline-flex items-center gap-2 rounded-lg bg-zinc-700 px-4 py-2 text-sm font-medium text-zinc-200 hover:bg-zinc-600 focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 focus:ring-offset-zinc-900">
                                                <i class="fa-solid fa-times"></i>
                                                {{ __('settings.cancel') }}
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <!-- View Mode -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $permission->user->getAvatarSourceAttribute() }}"
                                                alt="{{ $permission->user->twitch_display_name }}"
                                                class="w-10 h-10 rounded-full">
                                            <div>
                                                <div class="text-sm font-medium text-zinc-200">
                                                    {{ $permission->user->twitch_display_name ?? $permission->user->twitch_login }}
                                                </div>
                                                <div class="text-xs text-zinc-400">
                                                    {{ $permission->user->twitch_login }}</div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <!-- Permission Badges -->
                                            <div class="flex gap-1">
                                                @if ($permission->can_submit_clips)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-600/20 text-green-400 border border-green-600/30">
                                                        <i class="fa-solid fa-upload"></i>
                                                        {{ __('settings.submit') }}
                                                    </span>
                                                @endif
                                                @if ($permission->can_edit_clips)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-600/20 text-blue-400 border border-blue-600/30">
                                                        <i class="fa-solid fa-edit"></i>
                                                        {{ __('settings.edit') }}
                                                    </span>
                                                @endif
                                                @if ($permission->can_delete_clips)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-600/20 text-red-400 border border-red-600/30">
                                                        <i class="fa-solid fa-trash"></i>
                                                        {{ __('settings.delete') }}
                                                    </span>
                                                @endif
                                                @if ($permission->can_moderate_clips)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-purple-600/20 text-purple-400 border border-purple-600/30">
                                                        <i class="fa-solid fa-shield"></i>
                                                        {{ __('settings.moderate') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex gap-1">
                                                <button wire:click="startEditing({{ $permission->id }})"
                                                    class="p-2 text-zinc-400 hover:text-zinc-200 hover:bg-zinc-700 rounded-lg transition-colors"
                                                    title="{{ __('settings.edit_permissions_action') }}">
                                                    <i class="fa-solid fa-edit"></i>
                                                </button>

                                                <button wire:click="grantAllPermissions({{ $permission->id }})"
                                                    class="p-2 text-zinc-400 hover:text-green-400 hover:bg-zinc-700 rounded-lg transition-colors"
                                                    title="{{ __('settings.grant_all_permissions') }}">
                                                    <i class="fa-solid fa-check-double"></i>
                                                </button>

                                                <button wire:click="revokeAllPermissions({{ $permission->id }})"
                                                    class="p-2 text-zinc-400 hover:text-yellow-400 hover:bg-zinc-700 rounded-lg transition-colors"
                                                    title="{{ __('settings.revoke_all_permissions') }}">
                                                    <i class="fa-solid fa-ban"></i>
                                                </button>

                                                <button wire:click="removePermission({{ $permission->id }})"
                                                    class="p-2 text-zinc-400 hover:text-red-400 hover:bg-zinc-700 rounded-lg transition-colors"
                                                    title="{{ __('settings.remove_permission') }}"
                                                    onclick="return confirm('{{ __('settings.confirm_remove_permission') }}')">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
