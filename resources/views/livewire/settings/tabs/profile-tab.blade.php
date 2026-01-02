<div class="space-y-8">
    <div>
        <h2 class="text-2xl font-bold text-zinc-100 mb-2">{{ __('Profile Settings') }}</h2>
        <p class="text-zinc-400">{{ __('Manage your avatar and profile information') }}</p>
    </div>

    <!-- Avatar Section -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-zinc-200">{{ __('Avatar') }}</h3>
            <div class="text-xs text-zinc-500 bg-zinc-800/50 px-2 py-1 rounded">
                {{ __('user.avatar.max_size') }}
            </div>
        </div>

        <!-- Current Avatar -->
        <div class="flex items-start gap-6 p-4 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
            <div class="flex-shrink-0">
                <img
                    src="{{ $user->getAvatarSourceAttribute() }}"
                    alt="{{ $user->twitch_display_name }}"
                    class="size-24 rounded-full border-3 border-zinc-600 shadow-lg"
                >
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2">
                    <h4 class="text-sm font-medium text-zinc-300">{{ __('user.labels.current_avatar') }}</h4>
                    @if($user->hasAvatar())
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20 rounded-full">
                            <i class="fa-solid fa-user-check text-xs"></i>
                            {{ __('user.avatar.custom') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-zinc-500/10 text-zinc-400 border border-zinc-500/20 rounded-full">
                            <i class="fa-solid fa-user text-xs"></i>
                            Default
                        </span>
                    @endif
                </div>
                <p class="text-sm text-zinc-400">
                    @if($user->hasAvatar())
                        {{ __('user.avatar.using_custom') }}
                    @else
                        Using default avatar
                    @endif
                </p>
            </div>
        </div>

        <!-- Avatar Upload Section -->
        <div class="space-y-4">
            <div class="flex items-center gap-2">
                <h4 class="text-sm font-medium text-zinc-300">{{ __('user.labels.upload_new_avatar') }}</h4>
                <div class="flex gap-1">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs bg-zinc-700 text-zinc-300 rounded">
                        <i class="fa-solid fa-file-image text-xs"></i>
                        JPG
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs bg-zinc-700 text-zinc-300 rounded">
                        <i class="fa-solid fa-file-image text-xs"></i>
                        PNG
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs bg-zinc-700 text-zinc-300 rounded">
                        <i class="fa-solid fa-file-image text-xs"></i>
                        GIF
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs bg-zinc-700 text-zinc-300 rounded">
                        <i class="fa-solid fa-file-image text-xs"></i>
                        WebP
                    </span>
                </div>
            </div>

            <!-- File Input -->
            <div class="relative">
                <input
                    type="file"
                    wire:model="avatar"
                    accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                >
                <div class="flex items-center justify-center w-full h-32 border-2 border-dashed border-zinc-600 rounded-lg hover:border-(--color-accent-400) transition-colors bg-zinc-800/20 hover:bg-zinc-800/40">
                    <div class="text-center">
                        @if($avatar)
                            <div class="flex items-center gap-3">
                                <img
                                    src="{{ $avatar->temporaryUrl() }}"
                                    alt="Preview"
                                    class="size-12 rounded-lg border border-zinc-600 object-cover"
                                >
                                <div class="text-left">
                                    <p class="text-sm font-medium text-zinc-200">{{ $avatar->getClientOriginalName() }}</p>
                                    <p class="text-xs text-zinc-400">{{ number_format($avatar->getSize() / 1024, 1) }} KB</p>
                                </div>
                            </div>
                        @else
                            <div>
                                <i class="fa-solid fa-cloud-upload-alt text-2xl text-zinc-500 mb-2"></i>
                                <p class="text-sm text-zinc-400">{{ __('Click to select or drag and drop') }}</p>
                                <p class="text-xs text-zinc-500 mt-1">{{ __('user.avatar.formats_up_to') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @error('avatar')
                <div class="flex items-center gap-2 text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg p-3">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Avatar Actions -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @if($avatar)
                <button
                    wire:click="uploadAvatar"
                    wire:loading.attr="disabled"
                    class="group relative inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-green-600 to-green-500 px-4 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-green-500/25 hover:scale-105 disabled:opacity-50 disabled:hover:scale-100"
                >
                    <div wire:loading.remove wire:target="uploadAvatar" class="flex items-center gap-2">
                        <i class="fa-solid fa-upload"></i>
                        {{ __('user.avatar.upload') }}
                    </div>
                    <div wire:loading wire:target="uploadAvatar" class="flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        {{ __('user.avatar.uploading') }}
                    </div>
                </button>
            @endif

            <button
                wire:click="syncTwitchAvatar"
                wire:loading.attr="disabled"
                class="group relative inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 to-purple-500 px-4 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-purple-500/25 hover:scale-105 disabled:opacity-50 disabled:hover:scale-100"
            >
                <div wire:loading.remove wire:target="syncTwitchAvatar" class="flex items-center gap-2">
                    <i class="fa-brands fa-twitch"></i>
                    {{ __('user.avatar.sync_twitch') }}
                </div>
                <div wire:loading wire:target="syncTwitchAvatar" class="flex items-center gap-2">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    {{ __('user.avatar.syncing') }}
                </div>
            </button>

            @if($user->hasAvatar())
                <button
                    wire:click="resetAvatar"
                    wire:confirm="{{ __('Are you sure you want to reset your avatar to default?') }}"
                    class="group relative inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-amber-600 to-amber-500 px-4 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-amber-500/25 hover:scale-105"
                >
                    <i class="fa-solid fa-undo"></i>
                    {{ __('user.avatar.reset') }}
                </button>
            @endif

            @if($user->hasAvatar())
                <button
                    wire:click="deleteCustomAvatar"
                    wire:confirm="{{ __('Are you sure you want to delete your avatar?') }}"
                    class="group relative inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-500 px-4 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-red-500/25 hover:scale-105"
                >
                    <i class="fa-solid fa-trash"></i>
                    {{ __('user.avatar.delete') }}
                </button>
            @endif
        </div>

        <!-- Avatar Info -->
        <div class="bg-zinc-800/20 border border-zinc-700/50 rounded-lg p-4">
            <h4 class="text-sm font-medium text-zinc-300 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-info-circle text-zinc-400"></i>
                {{ __('user.labels.avatar_requirements') }}
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-zinc-400">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-weight-hanging text-zinc-500"></i>
                    <span>{{ __('user.avatar.max_file_size') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-expand-arrows-alt text-zinc-500"></i>
                    <span>{{ __('user.avatar.max_resolution') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-file-image text-zinc-500"></i>
                    <span>{{ __('user.avatar.formats') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-shield-alt text-zinc-500"></i>
                    <span>{{ __('user.avatar.secure_upload') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Description Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-zinc-200">{{ __('Description') }}</h3>
            <div class="text-xs text-zinc-500 bg-zinc-800/50 px-2 py-1 rounded">
                {{ strlen($description) }}/500
            </div>
        </div>

        <div>
            <textarea
                wire:model="description"
                rows="4"
                maxlength="500"
                class="block w-full rounded-lg border-0 bg-zinc-700/50 px-4 py-3 text-zinc-100 placeholder-zinc-500 shadow-sm focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900 resize-none"
                placeholder="{{ __('Tell others about yourself...') }}"
            ></textarea>
            @error('description')
                <div class="mt-2 flex items-center gap-2 text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg p-3">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="flex justify-end">
            <button
                wire:click="updateDescription"
                wire:loading.attr="disabled"
                class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-(--color-accent-500) to-(--color-accent-400) px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-(--color-accent-500)/25 hover:scale-105 disabled:opacity-50 disabled:hover:scale-100"
            >
                <div wire:loading.remove wire:target="updateDescription" class="flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    {{ __('Save Description') }}
                </div>
                <div wire:loading wire:target="updateDescription" class="flex items-center gap-2">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    {{ __('user.avatar.saving') }}
                </div>
            </button>
        </div>
    </div>
</div>
