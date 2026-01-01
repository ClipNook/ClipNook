@props([
    'type' => 'info',
    'icon' => null,
    'dismissible' => false,
    'class' => ''
])

@php
    $typeClasses = [
        'success' => 'bg-green-900/50 border-green-700 text-green-200',
        'error' => 'bg-red-900/50 border-red-700 text-red-200',
        'warning' => 'bg-yellow-900/50 border-yellow-700 text-yellow-200',
        'info' => 'bg-blue-900/50 border-blue-700 text-blue-200',
    ];

    $iconClasses = [
        'success' => 'fa-check-circle',
        'error' => 'fa-triangle-exclamation',
        'warning' => 'fa-exclamation-triangle',
        'info' => 'fa-info-circle',
    ];

    $defaultIcon = $icon ?: ($iconClasses[$type] ?? 'fa-info-circle');
@endphp

<div class="p-4 {{ $typeClasses[$type] ?? $typeClasses['info'] }} border rounded-md {{ $class }}">
    <div class="flex items-start">
        <i class="fa-solid {{ $defaultIcon }} mr-3 mt-0.5 flex-shrink-0"></i>
        <div class="flex-1">
            {{ $slot }}
        </div>
        @if($dismissible)
            <button type="button" class="ml-3 flex-shrink-0 text-current hover:opacity-75">
                <i class="fa-solid fa-times"></i>
            </button>
        @endif
    </div>
</div>