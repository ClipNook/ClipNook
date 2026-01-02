<div class="bg-zinc-900 rounded-lg border border-zinc-800 p-6">
    @if (session()->has('message'))
        <x-ui.alert type="success" class="mb-4">
            {{ session('message') }}
        </x-ui.alert>
    @endif

    @if (session()->has('error'))
        <x-ui.alert type="error" class="mb-4">
            {{ session('error') }}
        </x-ui.alert>
    @endif

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- Upvote Button -->
            <x-ui.button
                wire:click="vote('upvote')"
                :variant="$userVote?->value === 'upvote' ? 'primary' : 'secondary'"
                size="md"
                icon="thumbs-up"
                :title="__('clips.upvote')"
            />

            <!-- Divider -->
            <div class="h-8 w-px bg-zinc-700"></div>

            <!-- Downvote Button -->
            <x-ui.button
                wire:click="vote('downvote')"
                :variant="$userVote?->value === 'downvote' ? 'primary' : 'secondary'"
                size="md"
                icon="thumbs-down"
                :title="__('clips.downvote')"
            />
        </div>

        <livewire:clips.clip-report :clip="$clip" :key="'report-'.$clip->id" />
    </div>
</div>
