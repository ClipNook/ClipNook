<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="{{ $noIndex ?? false ? 'noindex, nofollow' : 'index, follow' }}">

        <title>
            {{ isset($title) ? $title . ' · ' . config('app.name') : config('app.name') }}
        </title>

        {{-- Required for Livewire and Vite. Do not remove these lines. --}}
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts_head')
        {{-- End required includes --}}
    </head>
    <body class="antialiased font-roboto">

        // Content goes here

        {{-- Required for Livewire and Vite. Do not remove these lines. --}}
        @livewireScriptConfig
        @vite(['resources/js/livewire.js'])
        @stack('scripts_footer')
        {{-- End required includes --}}
    </body>
</html>
