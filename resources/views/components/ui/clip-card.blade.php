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

<a href="{{ route('clips.view', $clip) }}" class="group relative bg-zinc-900/50 backdrop-blur-sm border border-zinc-800/50 hover:border-(--color-accent-500)/30 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg hover:shadow-(--color-accent-500)/10 {{ $class }}">
    <!-- Subtle accent border on hover -->
    <div class="absolute inset-0 bg-gradient-to-br from-(--color-accent-500)/0 via-transparent to-(--color-accent-600)/0 group-hover:from-(--color-accent-500)/5 group-hover:to-(--color-accent-600)/5 transition-all duration-300 rounded-xl pointer-events-none"></div>

    <!-- Thumbnail Section -->
    <div class="relative aspect-video bg-zinc-800 overflow-hidden">
        @if ($clip->hasLocalThumbnail())
            <img
                src="{{ $clip->getThumbnailUrlAttribute() }}"
                alt="{{ $clip->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
            >
        @else
            <div class="w-full h-full flex items-center justify-center bg-zinc-800">
                <i class="fa-solid fa-video text-zinc-500 text-4xl"></i>
            </div>
        @endif

        <!-- Duration Badge -->
        <div class="absolute bottom-3 right-3 bg-black/90 backdrop-blur-sm text-white text-xs font-semibold px-2.5 py-1 rounded-full border border-white/10">
            {{ round($clip->duration, 1) }}s
        </div>

        <!-- Play Overlay -->
        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center">
            <div class="w-12 h-12 bg-(--color-accent-500)/90 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <i class="fa-solid fa-play text-white text-sm ml-0.5"></i>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="p-4 space-y-3">
        <!-- Title -->
        <h3 class="text-zinc-100 font-semibold text-sm leading-tight line-clamp-2 group-hover:text-(--color-accent-400) transition-colors" title="{{ $clip->title }}">
            {{ $clip->title }}
        </h3>

        <!-- Meta Information -->
        <div class="space-y-2">
            <!-- Broadcaster -->
            <div class="flex items-center gap-2 text-xs">
                <div class="w-4 h-4 bg-zinc-800 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-user text-zinc-500 text-xs"></i>
                </div>
                <span class="text-zinc-400 truncate">{{ $clip->broadcaster?->twitch_display_name ?? __('clips.unknown') }}</span>
            </div>

            <!-- Game -->
            @if ($clip->game)
                <div class="flex items-center gap-2 text-xs">
                    <div class="w-4 h-4 bg-zinc-800 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-gamepad text-zinc-500 text-xs"></i>
                    </div>
                    <span class="text-zinc-400 truncate">{{ $clip->game->name }}</span>
                </div>
            @endif
        </div>

        <!-- Stats & Date -->
        <div class="flex items-center justify-between pt-3 border-t border-zinc-800/50">
            <div class="flex items-center gap-4 text-xs text-zinc-500">
                <div class="flex items-center gap-1.5">
                    <i class="fa-solid fa-eye text-zinc-600"></i>
                    <span>{{ number_format($clip->view_count) }}</span>
                </div>
                @if ($clip->upvotes > 0 || $clip->downvotes > 0)
                    <div class="flex items-center gap-1.5">
                        <i class="fa-solid fa-thumbs-up text-zinc-600"></i>
                        <span>{{ $clip->upvotes }}</span>
                    </div>
                @endif
            </div>
            <time class="text-xs text-zinc-500" datetime="{{ $clip->created_at_twitch?->toISOString() ?? $clip->created_at->toISOString() }}">
                {{ $clip->created_at_twitch?->format('M j') ?? $clip->created_at->format('M j') }}
            </time>
        </div>
    </div>
</a>
