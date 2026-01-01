@props([
    'title' => '',
    'icon' => null,
    'viewAllUrl' => null,
    'viewAllText' => null,
    'class' => 'mb-6'
])

<div class="flex items-center justify-between {{ $class }}">
    <h2 class="text-2xl font-bold text-neutral-100">
        @if($icon)
            <i class="fa-solid fa-{{ $icon }} mr-2 text-neutral-500"></i>
        @endif
        {{ $title }}
    </h2>
    @if($viewAllUrl)
        <a href="{{ $viewAllUrl }}" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition-colors">
            {{ $viewAllText ?: __('home.view_all') }} →
        </a>
    @endif
</div>