<div class="space-y-8">
    <div>
        <h2 class="text-2xl font-bold text-zinc-100 mb-2">{{ __('Profile Settings') }}</h2>
        <p class="text-zinc-400">{{ __('Manage your avatar and profile information') }}</p>
    </div>

    <!-- Avatar Section -->
    <div class="space-y-6">
        <h3 class="text-lg font-semibold text-zinc-200">{{ __('Avatar') }}</h3>
        
        <!-- Current Avatar -->
        <div class="flex items-center gap-4">
            <img 
                src="{{ $user->getAvatarUrlAttribute() }}" 
                alt="{{ $user->twitch_display_name }}"
                class="size-20 rounded-full border-2 border-zinc-700"
            >
            <div>
                <p class="text-sm text-zinc-400">
                    @if($user->avatar_source === 'custom')
                        {{ __('Using custom avatar') }}
                    @else
                        {{ __('Using Twitch avatar') }}
                    @endif
                </p>
            </div>
        </div>

        <!-- Avatar Upload -->
        <div>
            <label class="block text-sm font-medium text-zinc-300 mb-2">{{ __('Upload Custom Avatar') }}</label>
            <input 
                type="file" 
                wire:model="avatar"
                accept="image/*"
                class="block w-full text-sm text-zinc-400 file:mr-4 file:rounded-lg file:border-0 file:bg-(--color-accent-500)/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-(--color-accent-400) hover:file:bg-(--color-accent-500)/20 transition"
            >
            @error('avatar') <span class="mt-2 block text-sm text-red-400">{{ $message }}</span> @enderror
        </div>

        <!-- Avatar Actions -->
        <div class="flex flex-wrap gap-2">
            @if($avatar)
                <button
                    wire:click="uploadAvatar"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-green-500 disabled:opacity-50"
                >
                    <i class="fa-solid fa-upload"></i>
                    {{ __('Upload Avatar') }}
                </button>
            @endif

            <button
                wire:click="syncTwitchAvatar"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-lg bg-(--color-accent-500) px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-(--color-accent-400) disabled:opacity-50"
            >
                <i class="fa-brands fa-twitch"></i>
                {{ __('Sync from Twitch') }}
            </button>

            @if($user->custom_avatar_path)
                <button
                    wire:click="deleteCustomAvatar"
                    wire:confirm="{{ __('Are you sure you want to delete your custom avatar?') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500"
                >
                    <i class="fa-solid fa-trash"></i>
                    {{ __('Delete Custom Avatar') }}
                </button>
            @endif
        </div>
    </div>

    <!-- Description Section -->
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-zinc-200">{{ __('Description') }}</h3>
        
        <div>
            <textarea
                wire:model="description"
                rows="4"
                maxlength="500"
                class="block w-full rounded-lg border-0 bg-zinc-700/50 px-4 py-3 text-zinc-100 placeholder-zinc-500 shadow-sm focus:ring-2 focus:ring-(--color-accent-500) focus:ring-offset-2 focus:ring-offset-zinc-900"
                placeholder="{{ __('Tell others about yourself...') }}"
            ></textarea>
            <p class="mt-2 text-sm text-zinc-400">{{ strlen($description) }}/500</p>
            @error('description') <span class="mt-2 block text-sm text-red-400">{{ $message }}</span> @enderror
        </div>

        <button
            wire:click="updateDescription"
            wire:loading.attr="disabled"
            class="inline-flex items-center gap-2 rounded-lg bg-(--color-accent-500) px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-(--color-accent-400) disabled:opacity-50"
        >
            <i class="fa-solid fa-save"></i>
            {{ __('Save Description') }}
        </button>
    </div>
</div>
