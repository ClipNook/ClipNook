@props([
    'game' => null,
    'class' => ''
])

@php
    if (!$game) {
        return;
    }
@endphp

<a href="{{ route('games.view', $game) }}" class="group block bg-neutral-800 rounded-md border border-neutral-700 hover:border-neutral-600 transition-colors overflow-hidden {{ $class }}">
    <div class="aspect-[3/4] bg-neutral-700 relative">
        @if($game->local_box_art_path)
            <img
                src="{{ Storage::url($game->local_box_art_path) }}"
                alt="{{ $game->name }}"
                class="w-full h-full object-cover"
                loading="lazy"
            >
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fa-solid fa-gamepad text-neutral-600 text-3xl"></i>
            </div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
            <span class="text-xs text-neutral-300">{{ $game->clips_count }} {{ Str::plural('clip', $game->clips_count) }}</span>
        </div>
    </div>
    <div class="p-3">
        <p class="text-sm font-medium text-neutral-300 truncate">{{ $game->name }}</p>
    </div>
</a>