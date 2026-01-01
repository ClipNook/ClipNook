@props([
    'title' => '',
    'subtitle' => '',
    'class' => 'mb-16'
])

<div class="{{ $class }}">
    <div class="max-w-2xl mx-auto text-center mb-8">
        <h1 class="text-4xl sm:text-5xl font-bold text-zinc-100 mb-4 leading-tight">
            {{ $title }}
        </h1>
        <p class="text-lg text-zinc-400 leading-relaxed mb-8">
            {{ $subtitle }}
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            {{ $slot }}
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <x-ui.alert type="success" class="max-w-2xl mx-auto mb-6">
            {{ session('success') }}
        </x-ui.alert>
    @endif

    @if(session('error'))
        <x-ui.alert type="error" class="max-w-2xl mx-auto">
            {{ session('error') }}
        </x-ui.alert>
    @endif
</div>