<div wire:poll.60s class="relative">
    <a href="{{ route('home') }}#notifications" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white" aria-label="Notifications">
        <i class="fas fa-bell"></i>
        @if(!empty($unread) && $unread > 0)
            <span class="ml-1 inline-flex items-center justify-center w-5 h-5 text-xs font-semibold rounded-full bg-red-600 text-white">{{ $unread > 99 ? '99+' : $unread }}</span>
        @endif
    </a>
</div>
