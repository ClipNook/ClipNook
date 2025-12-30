<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
        <meta name="theme-color" content="#0f172a" media="(prefers-color-scheme: dark)">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="{{ $noIndex ?? false ? 'noindex, nofollow' : 'index, follow' }}">

        <title>{{ isset($title) ? $title . ' · ' . config('app.name') : config('app.name') }}</title>

        @livewireStyles
        @stack('scripts_header')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-roboto">

            {{ $slot }}

            @livewireScriptConfig
            @vite(['resources/js/livewire.js'])
            @stack('scripts_footer')
    </body>
</html>
