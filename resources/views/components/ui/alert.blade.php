@props([
    'type' => 'info',
    'icon' => null,
    'dismissible' => false,
    'class' => ''
])

@php
    $typeClasses = [
        'success' => 'bg-green-900 border-green-700 text-green-200',
        'error' => 'bg-red-900 border-red-700 text-red-200',
        'warning' => 'bg-yellow-900 border-yellow-700 text-yellow-200',
        'info' => 'bg-violet-900 border-violet-700 text-violet-200',
    ];

    $iconClasses = [
        'success' => 'fa-check-circle',
        'error' => 'fa-triangle-exclamation',
        'warning' => 'fa-exclamation-triangle',
        'info' => 'fa-info-circle',
    ];

    $defaultIcon = $icon ?: ($iconClasses[$type] ?? 'fa-info-circle');
@endphp

<div class="p-4 {{ $typeClasses[$type] ?? $typeClasses['info'] }} border rounded {{ $class }} relative">
    <div class="flex items-start">
        <i class="fa-solid {{ $defaultIcon }} mr-3 mt-0.5 flex-shrink-0 text-sm"></i>
        <div class="flex-1 text-sm">
            {{ $slot }}
        </div>
        @if($dismissible)
            <button type="button" class="ml-3 flex-shrink-0 text-current hover:opacity-75">
                <i class="fa-solid fa-times text-sm"></i>
            </button>
        @endif
    </div>
</div>