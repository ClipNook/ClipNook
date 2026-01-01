<div class="bg-neutral-900 rounded-md border border-neutral-800 p-6">
    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-900/50 border border-green-800 rounded-md text-green-200 text-sm">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-3 bg-red-900/50 border border-red-800 rounded-md text-red-200 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- Upvote Button with Count (Large - Primary Action) -->
            <button
                wire:click="vote('upvote')"
                class="group flex items-center gap-2 px-4 py-2.5 rounded-md transition-all {{ $userVote?->value === 'upvote' ? 'bg-neutral-700 text-white border border-neutral-600' : 'bg-neutral-800 hover:bg-neutral-700 text-neutral-400 hover:text-white border border-neutral-700' }}"
                title="{{ __('clips.upvote') }}"
            >
                <i class="fa-solid fa-thumbs-up group-hover:scale-110 transition-transform"></i>
            </button>

            <!-- Divider -->
            <div class="h-8 w-px bg-neutral-700"></div>

            <!-- Downvote Button (Small - Icon only) -->
            <button
                wire:click="vote('downvote')"
                class="group flex items-center gap-2 px-4 py-2.5 rounded-md transition-all {{ $userVote?->value === 'downvote' ? 'bg-neutral-700 text-white border border-neutral-600' : 'bg-neutral-800 hover:bg-neutral-700 text-neutral-400 hover:text-white border border-neutral-700' }}"
                title="{{ __('clips.downvote') }}"
            >
                <i class="fa-solid fa-thumbs-down group-hover:scale-110 transition-transform"></i>
            </button>
        </div>

        <livewire:clips.clip-report :clip="$clip" :key="'report-'.$clip->id" />
    </div>
</div>
