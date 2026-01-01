<div class="bg-gray-900 rounded-lg border border-gray-800 p-6">
    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-900/50 border border-green-800 rounded-lg text-green-200 text-sm">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-3 bg-red-900/50 border border-red-800 rounded-lg text-red-200 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- Upvote Button with Count (Large - Primary Action) -->
            <button 
                wire:click="vote('upvote')" 
                class="group flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all {{ $userVote?->value === 'upvote' ? 'bg-gray-700 text-white border border-gray-600' : 'bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white border border-gray-700' }}"
                title="{{ __('clips.upvote') }}"
            >
                <i class="fas fa-thumbs-up group-hover:scale-110 transition-transform" aria-hidden="true"></i>
            </button>

            <!-- Divider -->
            <div class="h-8 w-px bg-gray-700"></div>

            <!-- Downvote Button (Small - Icon only) -->
            <button 
                wire:click="vote('downvote')" 
                class="group flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all {{ $userVote?->value === 'downvote' ? 'bg-gray-700 text-white border border-gray-600' : 'bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white border border-gray-700' }}"
                title="{{ __('clips.downvote') }}"
            >
                <i class="fas fa-thumbs-down group-hover:scale-110 transition-transform" aria-hidden="true"></i>
            </button>
        </div>

        <livewire:clips.clip-report :clip="$clip" :key="'report-'.$clip->id" />
    </div>
</div>
