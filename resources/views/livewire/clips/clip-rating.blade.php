<div class="bg-gray-900 rounded-md border border-gray-800 p-6">
    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-purple-900/50 border border-purple-700 rounded-md text-purple-200 text-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <button 
                wire:click="vote('upvote')" 
                class="flex items-center gap-2 px-4 py-2 rounded-md transition-colors {{ $userVote === 'upvote' ? 'bg-purple-600 text-white' : 'bg-gray-800 hover:bg-gray-700 text-white' }}"
            >
                <i class="fas fa-thumbs-up"></i>
                <span>{{ $upvotes }}</span>
            </button>
            <button 
                wire:click="vote('downvote')" 
                class="flex items-center gap-2 px-4 py-2 rounded-md transition-colors {{ $userVote === 'downvote' ? 'bg-red-600 text-white' : 'bg-gray-800 hover:bg-gray-700 text-white' }}"
            >
                <i class="fas fa-thumbs-down"></i>
                <span>{{ $downvotes }}</span>
            </button>
        </div>
        <livewire:clips.clip-report :clip="$clip" :key="'report-'.$clip->id" />
    </div>
</div>
