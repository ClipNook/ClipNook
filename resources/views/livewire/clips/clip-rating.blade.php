<div>
    <div class="space-y-4">
        <!-- Vote Buttons -->
        <div class="flex items-center gap-3">
            <!-- Upvote Button -->
            <x-ui.button
                wire:click="vote('upvote')"
                :variant="$userVote?->value === 'upvote' ? 'primary' : 'secondary'"
                size="lg"
                icon="thumbs-up"
                class="flex-1"
            >
                {{ __('clips.upvote') }}
            </x-ui.button>

            <!-- Downvote Button -->
            <x-ui.button
                wire:click="vote('downvote')"
                :variant="$userVote?->value === 'downvote' ? 'primary' : 'secondary'"
                size="lg"
                icon="thumbs-down"
                class="flex-1"
            >
                {{ __('clips.downvote') }}
            </x-ui.button>
        </div>

        <!-- Report Section -->
        <div class="pt-4 border-t border-zinc-800/50">
            <livewire:clips.clip-report :clip="$clip" :key="'report-'.$clip->id" />
        </div>
    </div>
</div>
