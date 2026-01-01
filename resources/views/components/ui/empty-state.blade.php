@props([
    'message' => '',
    'icon' => 'inbox',
    'class' => ''
])

<div class="text-center py-8 bg-neutral-800/50 rounded-md border border-neutral-700 {{ $class }}">
    <i class="fa-solid fa-{{ $icon }} text-neutral-600 text-3xl mb-3 block"></i>
    <p class="text-neutral-400">{{ $message }}</p>
</div>