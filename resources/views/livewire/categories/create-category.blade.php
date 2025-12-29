<div>
    <form wire:submit.prevent="save" class="space-y-6">
        <div>
            <label for="name" class="block font-medium">{{ __('category.name') }}</label>
            <input type="text" id="name" wire:model.live="name" class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white" required maxlength="255">
            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="slug" class="block font-medium">{{ __('category.slug') }}</label>
            <input type="text" id="slug" wire:model.live="slug" class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white" required maxlength="255">
            @error('slug') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="description" class="block font-medium">{{ __('category.description') }}</label>
            <textarea id="description" wire:model.live="description" class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white" maxlength="1000"></textarea>
            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="icon" class="block font-medium">{{ __('category.icon') }}</label>
            <input type="file" id="icon" wire:model="icon" accept="image/*" class="mt-1 block w-full">
            @if ($icon)
                <img src="{{ $icon->temporaryUrl() }}" class="mt-2 h-16 w-16 object-cover rounded" alt="Preview">
            @endif
            @error('icon') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('category.create') }}</button>
        </div>
        @if (session('success'))
            <div class="text-green-600 mt-2">{{ session('success') }}</div>
        @endif
    </form>
</div>
