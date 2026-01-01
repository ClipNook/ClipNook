@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
    'icon' => null,
    'iconType' => 'solid',
    'iconPosition' => 'left'
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-neutral-950 disabled:opacity-50 disabled:cursor-not-allowed';

    $variantClasses = [
        'primary' => 'bg-purple-600 hover:bg-purple-700 text-neutral-100 focus:ring-purple-500',
        'secondary' => 'bg-neutral-800 hover:bg-neutral-700 text-neutral-100 focus:ring-neutral-500 border border-neutral-700',
        'outline' => 'border border-neutral-700 text-neutral-300 hover:text-neutral-100 hover:border-neutral-600 focus:ring-neutral-500',
        'ghost' => 'text-neutral-400 hover:text-neutral-300 hover:bg-neutral-800 focus:ring-neutral-500',
        'danger' => 'bg-red-600 hover:bg-red-700 text-neutral-100 focus:ring-red-500',
    ];

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
        'xl' => 'px-8 py-4 text-lg',
    ];

    $iconClasses = [
        'solid' => 'fa-solid',
        'regular' => 'fa-regular',
        'brand' => 'fab',
    ];

    $classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <i class="{{ $iconClasses[$iconType] ?? 'fa-solid' }} fa-{{ $icon }} mr-2"></i>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <i class="{{ $iconClasses[$iconType] ?? 'fa-solid' }} fa-{{ $icon }} ml-2"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <i class="{{ $iconClasses[$iconType] ?? 'fa-solid' }} fa-{{ $icon }} mr-2"></i>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <i class="{{ $iconClasses[$iconType] ?? 'fa-solid' }} fa-{{ $icon }} ml-2"></i>
        @endif
    </button>
@endif