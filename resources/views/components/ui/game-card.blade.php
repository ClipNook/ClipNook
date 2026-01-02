@props([
    'game' => null,
    'class' => ''
])

@php
    if (!$game) {
        return;
    }
@endphp

<a href="{{ route('games.view', $game) }}" class="group relative bg-zinc-900/50 backdrop-blur-sm border border-zinc-800/50 hover:border-(--color-accent-500)/30 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg hover:shadow-(--color-accent-500)/10 hover:-translate-y-1 {{ $class }}">
    <!-- Subtle accent border on hover -->
    <div class="absolute inset-0 bg-gradient-to-br from-(--color-accent-500)/0 via-transparent to-(--color-accent-600)/0 group-hover:from-(--color-accent-500)/5 group-hover:to-(--color-accent-600)/5 transition-all duration-300 rounded-xl pointer-events-none"></div>

    <!-- Box Art Section -->
    <div class="relative aspect-square bg-zinc-800 overflow-hidden">
        @if($game->local_box_art_path)
            <img
                src="{{ Storage::url($game->local_box_art_path) }}"
                alt="{{ $game->name }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
            >
        @else
            <div class="w-full h-full flex items-center justify-center bg-zinc-800">
                <i class="fa-solid fa-gamepad text-zinc-500 text-4xl"></i>
            </div>
        @endif

        <!-- Clips Count Overlay -->
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 via-black/60 to-transparent p-4">
            <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5">
                    <i class="fa-solid fa-video text-zinc-300"></i>
                    <span class="text-zinc-200 font-medium">{{ number_format($game->clips_count) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="p-4">
        <h3 class="text-zinc-100 font-semibold text-sm leading-tight line-clamp-2 group-hover:text-(--color-accent-400) transition-colors" title="{{ $game->name }}">
            {{ $game->name }}
        </h3>
    </div>
</a>