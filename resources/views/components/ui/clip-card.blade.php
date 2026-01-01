@props([
    'clip' => null,
    'showStats' => true,
    'class' => ''
])

@php
    if (!$clip) {
        return;
    }
@endphp

<a href="{{ route('clips.view', $clip) }}" class="group block bg-neutral-800 rounded-md overflow-hidden border border-neutral-700 hover:border-neutral-600 transition-colors {{ $class }}">
    <div class="aspect-video bg-neutral-700 relative overflow-hidden">
        <img
            src="{{ $clip->thumbnail_url }}"
            alt="{{ $clip->title }}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform"
            loading="lazy"
        >
        <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/70 rounded text-xs text-neutral-300 font-medium">
            {{ $clip->duration }}s
        </div>
    </div>
    <div class="p-4">
        <h3 class="font-medium text-neutral-100 truncate mb-2">{{ $clip->title }}</h3>
        <div class="flex items-center justify-between text-xs text-neutral-400 mb-3">
            <span>{{ $clip->broadcaster->name }}</span>
            <span>{{ $clip->created_at->shortRelativeDiffForHumans() }}</span>
        </div>
        @if($showStats)
            <div class="flex items-center justify-between text-xs text-neutral-500">
                <span><i class="fa-solid fa-eye mr-1"></i>{{ number_format($clip->views) }}</span>
                <span><i class="fa-solid fa-thumbs-up mr-1"></i>{{ number_format($clip->upvotes) }}</span>
            </div>
        @endif
    </div>
</a>