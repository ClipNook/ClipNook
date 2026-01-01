@props([
    'game' => null,
    'class' => ''
])

@php
    if (!$game) {
        return;
    }
@endphp

<a href="{{ route('games.view', $game) }}" class="block bg-zinc-800 border border-zinc-700 hover:border-zinc-600 rounded-lg overflow-hidden transition-colors {{ $class }}">
    <div class="aspect-[3/4] bg-zinc-700 relative">
        @if($game->local_box_art_path)
            <img
                src="{{ Storage::url($game->local_box_art_path) }}"
                alt="{{ $game->name }}"
                class="w-full h-full object-cover"
                loading="lazy"
            >
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fa-solid fa-gamepad text-zinc-600 text-3xl"></i>
            </div>
        @endif
        <div class="absolute bottom-0 left-0 right-0 bg-zinc-900/80 p-3">
            <span class="text-xs text-zinc-300">{{ $game->clips_count }} {{ Str::plural('clip', $game->clips_count) }}</span>
        </div>
    </div>
    <div class="p-3">
        <p class="text-sm font-medium text-zinc-300 truncate">{{ $game->name }}</p>
    </div>
</a>