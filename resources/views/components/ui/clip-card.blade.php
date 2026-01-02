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

<a href="{{ route('clips.view', $clip) }}" class="block bg-zinc-800 border border-zinc-700 hover:border-violet-600 rounded-lg overflow-hidden transition-colors {{ $class }}">
    <div class="aspect-video bg-zinc-700 relative overflow-hidden">
        <img
            src="{{ $clip->thumbnail_url }}"
            alt="{{ $clip->title }}"
            class="w-full h-full object-cover"
            loading="lazy"
        >
        <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/70 text-zinc-100 text-xs font-medium rounded">
            {{ $clip->duration }}s
        </div>
    </div>

    <div class="p-4">
        <h3 class="font-semibold text-zinc-100 text-lg mb-2 line-clamp-2 leading-tight">
            {{ $clip->title }}
        </h3>

        <div class="flex items-center justify-between text-sm text-zinc-400 mb-3">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-user text-xs"></i>
                <span class="font-medium">{{ $clip->broadcaster->name }}</span>
            </div>
            <div class="flex items-center gap-1">
                <i class="fa-solid fa-clock text-xs"></i>
                <span>{{ $clip->created_at->shortRelativeDiffForHumans() }}</span>
            </div>
        </div>

        @if($showStats)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-1.5 text-zinc-400">
                        <i class="fa-solid fa-eye text-sm"></i>
                        <span class="text-sm font-medium">{{ number_format($clip->views) }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-zinc-400">
                        <i class="fa-solid fa-thumbs-up text-sm"></i>
                        <span class="text-sm font-medium">{{ number_format($clip->upvotes) }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</a>