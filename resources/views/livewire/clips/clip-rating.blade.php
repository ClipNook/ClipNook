<div class="relative bg-zinc-900 border border-zinc-800 hover:border-zinc-700 rounded-xl overflow-hidden transition-all duration-200 group p-6">
    <!-- Subtle accent border -->
    <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500)/30 to-transparent mb-6"></div>
    @if (session()->has('message'))
        <div class="bg-green-900/50 border border-green-800 rounded-lg p-4 mb-4">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-check-circle text-green-400 mt-0.5"></i>
                <span class="text-green-200">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-900/50 border border-red-800 rounded-lg p-4 mb-4">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation text-red-400 mt-0.5"></i>
                <span class="text-red-200">{{ session('error') }}</span>
            </div>
        </div>
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
