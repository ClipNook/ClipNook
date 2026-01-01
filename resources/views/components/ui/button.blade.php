@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'loading' => false
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium border transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-950 disabled:opacity-50 disabled:cursor-not-allowed';

    $variantClasses = [
        'primary' => 'bg-violet-600 hover:bg-violet-700 text-zinc-100 border-violet-600 focus:ring-violet-500',
        'secondary' => 'bg-zinc-700 hover:bg-zinc-600 text-zinc-100 border-zinc-700 focus:ring-zinc-500',
        'outline' => 'border-zinc-600 text-zinc-300 hover:text-zinc-100 hover:border-zinc-500 focus:ring-zinc-500',
        'ghost' => 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 border-transparent focus:ring-zinc-500',
        'danger' => 'bg-red-600 hover:bg-red-700 text-zinc-100 border-red-600 focus:ring-red-500',
        'success' => 'bg-green-600 hover:bg-green-700 text-zinc-100 border-green-600 focus:ring-green-500',
    ];

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm gap-1.5 rounded',
        'md' => 'px-4 py-2 text-sm gap-2 rounded-md',
        'lg' => 'px-6 py-3 text-base gap-2 rounded-md',
        'xl' => 'px-8 py-4 text-lg gap-3 rounded-md',
    ];

    $classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <i class="fa-solid fa-{{ $icon }} text-sm"></i>
        @endif
        @if($loading)
            <i class="fa-solid fa-spinner fa-spin text-sm"></i>
        @else
            {{ $slot }}
        @endif
        @if($icon && $iconPosition === 'right')
            <i class="fa-solid fa-{{ $icon }} text-sm"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $disabled || $loading ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <i class="fa-solid fa-{{ $icon }} text-sm"></i>
        @endif
        @if($loading)
            <i class="fa-solid fa-spinner fa-spin text-sm"></i>
        @else
            {{ $slot }}
        @endif
        @if($icon && $iconPosition === 'right')
            <i class="fa-solid fa-{{ $icon }} text-sm"></i>
        @endif
    </button>
@endif