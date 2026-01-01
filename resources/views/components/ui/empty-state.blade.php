@props([
    'message' => '',
    'icon' => 'inbox',
    'class' => ''
])

<div class="text-center py-8 bg-zinc-800 border border-zinc-700 rounded-lg {{ $class }}">
    <i class="fa-solid fa-{{ $icon }} text-zinc-600 text-3xl mb-3 block"></i>
    <p class="text-zinc-400">{{ $message }}</p>
</div>