@props(["variant" => 'neutral', "size" => 'md', "block" => false])
@php
    $base = 'inline-flex items-center justify-center rounded focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors disabled:opacity-70 disabled:cursor-not-allowed';

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
        'icon' => 'w-8 h-8 p-0 text-sm'
    ];

    $variants = [
        'primary' => 'bg-indigo-600 hover:bg-indigo-700 text-white focus:ring-indigo-500',
        'success' => 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
        'neutral' => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 focus:ring-indigo-500',
        'outline' => 'bg-transparent border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50',
        'dark' => 'bg-gray-900 hover:bg-gray-800 text-white dark:bg-gray-700 dark:hover:bg-gray-600',
        'icon' => 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-900'
    ];

    $sizeClasses = $sizes[$size] ?? $sizes['md'];
    $variantClasses = $variants[$variant] ?? $variants['neutral'];

    $blockClass = $block ? 'w-full' : '';

    $classes = trim(implode(' ', array_filter([$base, $size === 'icon' ? $variants['icon'] : $variantClasses, $sizeClasses, $blockClass, $attributes->get('class')] )));
@endphp

@if($attributes->has('href'))
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
