@props([
    'items' => [],
    'class' => 'flex items-center gap-2 text-sm mb-6',
    'currentClass' => 'text-zinc-300',
    'linkClass' => 'text-zinc-500 hover:text-zinc-400 transition-colors',
    'separatorClass' => 'text-xs text-zinc-500',
    'separator' => 'fa-chevron-right',
])

@if(count($items) > 0)
    <nav {{ $attributes->merge(['class' => $class, 'aria-label' => 'Breadcrumb']) }}>
        @foreach($items as $index => $item)
            @if($index > 0)
                <i class="fa-solid {{ $separator }} {{ $separatorClass }}"></i>
            @endif

            @if(isset($item['current']))
                <span class="{{ $currentClass }} {{ isset($item['truncate']) ? 'truncate' : '' }}">
                    {{ isset($item['truncate']) ? Str::limit($item['label'], $item['truncate']) : $item['label'] }}
                </span>
            @else
                <a href="{{ $item['url'] }}" class="{{ $linkClass }}">{{ $item['label'] }}</a>
            @endif
        @endforeach
    </nav>
@endif

{{ $slot }}
