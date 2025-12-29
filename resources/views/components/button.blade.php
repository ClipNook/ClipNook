@props([
    'variant' => 'neutral',
    'size' => 'md',
    'block' => false,
    'loading' => false,
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'external' => false,
    'href' => null,
    'type' => 'button'
])

@php
    // Validate props
    $validVariants = ['primary', 'success', 'neutral', 'outline', 'dark', 'danger', 'icon'];
    $validSizes = ['sm', 'md', 'lg', 'icon'];
    $validIconPositions = ['left', 'right'];

    $variant = in_array($variant, $validVariants) ? $variant : 'neutral';
    $size = in_array($size, $validSizes) ? $size : 'md';
    $iconPosition = in_array($iconPosition, $validIconPositions) ? $iconPosition : 'left';

    // Base classes - always applied
    $baseClasses = [
        'inline-flex',
        'items-center',
        'justify-center',
        'rounded',
        'font-medium',
        'focus:outline-none',
        'focus:ring-2',
        'focus:ring-offset-2',
        'transition-all',
        'duration-200',
        'disabled:opacity-50',
        'disabled:cursor-not-allowed',
        'disabled:pointer-events-none'
    ];

    // Size classes
    $sizeClasses = [
        'sm' => ['px-3', 'py-1.5', 'text-xs', 'gap-1.5'],
        'md' => ['px-5', 'py-2.5', 'text-sm', 'gap-2'],
        'lg' => ['px-6', 'py-3', 'text-base', 'gap-2.5'],
        'icon' => ['w-8', 'h-8', 'p-0', 'text-sm']
    ];

    // Variant classes with proper dark mode support
    $variantClasses = [
        'primary' => [
            'bg-indigo-600', 'hover:bg-indigo-700', 'active:bg-indigo-800',
            'text-white',
            'focus:ring-indigo-500',
            'dark:bg-indigo-500', 'dark:hover:bg-indigo-600', 'dark:active:bg-indigo-700'
        ],
        'success' => [
            'bg-green-600', 'hover:bg-green-700', 'active:bg-green-800',
            'text-white',
            'focus:ring-green-500',
            'dark:bg-green-500', 'dark:hover:bg-green-600', 'dark:active:bg-green-700'
        ],
        'neutral' => [
            'bg-gray-100', 'hover:bg-gray-200', 'active:bg-gray-300',
            'text-gray-900',
            'focus:ring-indigo-500',
            'dark:bg-gray-800', 'dark:hover:bg-gray-700', 'dark:active:bg-gray-600',
            'dark:text-gray-100'
        ],
        'outline' => [
            'bg-transparent',
            'border', 'border-gray-300', 'hover:border-gray-400', 'active:border-gray-500',
            'text-gray-700', 'hover:text-gray-900', 'active:text-gray-900',
            'hover:bg-gray-50', 'active:bg-gray-100',
            'focus:ring-indigo-500',
            'dark:border-gray-600', 'dark:hover:border-gray-500', 'dark:active:border-gray-400',
            'dark:text-gray-300', 'dark:hover:text-gray-100', 'dark:active:text-gray-100',
            'dark:hover:bg-gray-800', 'dark:active:bg-gray-700'
        ],
        'dark' => [
            'bg-gray-900', 'hover:bg-gray-800', 'active:bg-gray-700',
            'text-white',
            'focus:ring-gray-500',
            'dark:bg-gray-700', 'dark:hover:bg-gray-600', 'dark:active:bg-gray-500'
        ],
        'danger' => [
            'bg-red-600', 'hover:bg-red-700', 'active:bg-red-800',
            'text-white',
            'focus:ring-red-500',
            'dark:bg-red-500', 'dark:hover:bg-red-600', 'dark:active:bg-red-700'
        ],
        'icon' => [
            'text-gray-600', 'hover:text-gray-900', 'active:text-gray-900',
            'hover:bg-gray-100', 'active:bg-gray-200',
            'focus:ring-indigo-500',
            'dark:text-gray-400', 'dark:hover:text-gray-100', 'dark:active:text-gray-100',
            'dark:hover:bg-gray-800', 'dark:active:bg-gray-700'
        ]
    ];

    // Accent mapping for dynamic theming
    $accentMapping = [
        'primary' => 'bg',
        'success' => 'bg',
        'dark' => 'bg',
        'danger' => 'bg',
        'neutral' => 'bgLight',
        'outline' => 'border',
        'icon' => null
    ];

    // Build final classes array
    $classes = array_merge(
        $baseClasses,
        $sizeClasses[$size],
        $variantClasses[$variant]
    );

    // Add block class if needed
    if ($block) {
        $classes[] = 'w-full';
    }

    // Add loading class if needed
    if ($loading) {
        $classes[] = 'cursor-wait';
    }

    // Determine accent attribute
    $accentAttr = $attributes->get('accent') ?? ($accentMapping[$variant] ?? null);

    // Handle disabled state
    $isDisabled = $disabled || $loading;

    // Build final class string
    $finalClasses = trim(implode(' ', array_filter(array_merge(
        $classes,
        [$attributes->get('class')]
    ))));
@endphp

@php
    // Prepare element attributes
    $elementAttributes = $attributes
        ->except(['class', 'accent'])
        ->merge(['class' => $finalClasses]);

    // Add accent data attribute if applicable
    if ($accentAttr) {
        $elementAttributes = $elementAttributes->merge(['data-accent' => $accentAttr]);
    }

    // Add disabled attribute for buttons
    if ($isDisabled && !$href) {
        $elementAttributes = $elementAttributes->merge(['disabled' => true]);
    }

    // Add ARIA attributes for loading state
    if ($loading) {
        $elementAttributes = $elementAttributes->merge([
            'aria-busy' => 'true',
            'aria-describedby' => $attributes->get('id') ? $attributes->get('id') . '-loading' : null
        ]);
    }

    // Handle external links
    if ($href && $external) {
        $elementAttributes = $elementAttributes->merge([
            'target' => '_blank',
            'rel' => 'noopener noreferrer'
        ]);
    }

    // Set button type
    if (!$href) {
        $elementAttributes = $elementAttributes->merge(['type' => $type]);
    }
@endphp

@if($href)
    <a href="{{ $href }}" {{ $elementAttributes }}>
        @if($icon && $iconPosition === 'left')
            <x-dynamic-component :component="$icon" class="w-4 h-4 shrink-0" />
        @endif

        @if($loading)
            <svg class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif

        <span class="{{ $loading ? 'opacity-75' : '' }}">
            {{ $slot }}
        </span>

        @if($icon && $iconPosition === 'right')
            <x-dynamic-component :component="$icon" class="w-4 h-4 shrink-0" />
        @endif
    </a>
@else
    <button {{ $elementAttributes }}>
        @if($icon && $iconPosition === 'left')
            <x-dynamic-component :component="$icon" class="w-4 h-4 shrink-0" />
        @endif

        @if($loading)
            <svg class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif

        <span class="{{ $loading ? 'opacity-75' : '' }}">
            {{ $slot }}
        </span>

        @if($icon && $iconPosition === 'right')
            <x-dynamic-component :component="$icon" class="w-4 h-4 shrink-0" />
        @endif
    </button>
@endif

@if($loading)
    <span id="{{ $attributes->get('id') ? $attributes->get('id') . '-loading' : '' }}" class="sr-only">
        Loading...
    </span>
@endif
