@props([
    'clip' => null,
    'showStats' => true,
    'class' => '',
])

@php
    if (!$clip) {
        return;
    }
@endphp

<a href="{{ route('clips.view', $clip) }}"
    class="group block bg-zinc-800 rounded-md overflow-hidden border border-zinc-700 hover:border-violet-600 transition-colors relative">
    <!-- Subtle accent border -->
    <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500)/30 to-transparent"></div>
    <!-- Thumbnail -->
    <div class="aspect-video bg-zinc-700 relative overflow-hidden">
        @if ($clip->hasLocalThumbnail())
            <img src="{{ $clip->getThumbnailUrlAttribute() }}" alt="{{ $clip->title }}" class="w-full h-full object-cover"
                loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-zinc-600">
                <i class="fa-solid fa-video text-3xl"></i>
            </div>
        @endif

        <!-- Duration Badge -->
        <div class="absolute bottom-2 right-2 bg-black/80 text-white text-xs font-medium px-2 py-0.5 rounded">
            {{ round($clip->duration, 1) }}s
        </div>
    </div>

    <!-- Content -->
    <div class="p-4">
        <h3 class="text-zinc-100 font-medium text-sm mb-3 line-clamp-2 leading-snug" title="{{ $clip->title }}">
            {{ $clip->title }}
        </h3>

        <div class="space-y-2">
            <!-- Broadcaster -->
            <div class="flex items-center gap-2 text-xs">
                <i class="fa-solid fa-user text-zinc-500 w-3"></i>
                <span class="text-zinc-400 truncate">{{ $clip->broadcaster?->twitch_display_name ?? __('clips.unknown') }}</span>
            </div>

            <!-- Game -->
            @if ($clip->game)
                <div class="flex items-center gap-2 text-xs">
                    <i class="fa-solid fa-gamepad text-zinc-500 w-3"></i>
                    <span class="text-zinc-400 truncate">{{ $clip->game->name }}</span>
                </div>
            @endif

            <!-- Stats Row -->
            <div class="flex items-center justify-between pt-2 border-t border-zinc-700">
                <div class="flex items-center gap-3 text-xs text-zinc-500">
                    <span class="flex items-center gap-1">
                        <i class="fa-solid fa-eye"></i>
                        {{ number_format($clip->view_count) }}
                    </span>
                    @if ($clip->upvotes > 0 || $clip->downvotes > 0)
                        <span class="flex items-center gap-1 text-zinc-400">
                            <i class="fa-solid fa-thumbs-up"></i>
                            {{ $clip->upvotes }}
                        </span>
                    @endif
                </div>
                <span class="text-xs text-zinc-500">
                    {{ $clip->created_at_twitch?->format('M j') ?? $clip->created_at->format('M j') }}
                </span>
            </div>
        </div>
    </div>
</a>
