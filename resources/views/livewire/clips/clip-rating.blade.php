<div>
    @if (session()->has('message'))
        <div class="bg-green-900/50 border border-green-800 rounded-lg p-3 mb-4">
            <div class="flex items-start gap-2">
                <i class="fa-solid fa-check-circle text-green-400 text-sm mt-0.5"></i>
                <span class="text-green-200 text-sm">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-900/50 border border-red-800 rounded-lg p-3 mb-4">
            <div class="flex items-start gap-2">
                <i class="fa-solid fa-triangle-exclamation text-red-400 text-sm mt-0.5"></i>
                <span class="text-red-200 text-sm">{{ session('error') }}</span>
            </div>
        </div>
    @endif

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
