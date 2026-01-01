<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#030712">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="{{ $noIndex ?? false ? 'noindex, nofollow' : 'index, follow' }}">
        <meta name="description" content="{{ __('app.description') }}">

        <title>{{ isset($title) ? $title . ' · ' . config('app.name') : config('app.name') }}</title>

        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts_header')
    </head>
    <body class="min-h-screen flex flex-col bg-gray-950 text-gray-100 font-sans antialiased">
        <!-- Header -->
        <header class="bg-gray-900 border-b border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 text-white hover:text-purple-400 transition-colors" aria-label="{{ __('nav.home') }}">
                            <i class="fas fa-video text-purple-500" aria-hidden="true"></i>
                            <span class="font-bold text-lg">{{ config('app.name') }}</span>
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center gap-1" role="navigation" aria-label="{{ __('nav.main_navigation') }}">
                        <a href="{{ route('home') }}" class="px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}" aria-current="{{ request()->routeIs('home') ? 'page' : null }}">
                            <i class="fas fa-home mr-1.5" aria-hidden="true"></i>
                            {{ __('nav.home') }}
                        </a>
                        <a href="{{ route('clips.list') }}" class="px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('clips.list') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}" aria-current="{{ request()->routeIs('clips.*') ? 'page' : null }}">
                            <i class="fas fa-film mr-1.5" aria-hidden="true"></i>
                            {{ __('nav.clips') }}
                        </a>
                        <a href="{{ route('games.list') }}" class="px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('games.*') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}" aria-current="{{ request()->routeIs('games.*') ? 'page' : null }}">
                            <i class="fas fa-gamepad mr-1.5" aria-hidden="true"></i>
                            {{ __('nav.games') }}
                        </a>
                        @auth
                            <a href="{{ route('clips.submit') }}" class="px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('clips.submit') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}" aria-current="{{ request()->routeIs('clips.submit') ? 'page' : null }}">
                                <i class="fas fa-plus mr-1.5" aria-hidden="true"></i>
                                {{ __('nav.submit') }}
                            </a>
                        @endauth
                    </nav>

                    <!-- User Menu -->
                    <div class="flex items-center">
                        @auth
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center gap-2 text-gray-400 hover:text-white px-3 py-2 text-sm font-medium transition-colors focus:outline-none" aria-expanded="false" aria-haspopup="true">
                                    <i class="fas fa-user" aria-hidden="true"></i>
                                    <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs" aria-hidden="true"></i>
                                </button>

                                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-md border border-gray-700 z-50" role="menu" aria-orientation="vertical">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200" role="menuitem">
                                        <i class="fas fa-user mr-2" aria-hidden="true"></i>
                                        {{ __('nav.profile') }}
                                    </a>
                                    <form method="POST" action="{{ route('auth.twitch.logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200" role="menuitem">
                                            <i class="fas fa-sign-out-alt mr-2" aria-hidden="true"></i>
                                            {{ __('nav.logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('auth.login') }}" class="text-gray-300 hover:text-white px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 focus:ring-offset-gray-900">
                                <i class="fas fa-sign-in-alt mr-1" aria-hidden="true"></i>
                                {{ __('nav.login') }}
                            </a>
                        @endauth

                        <!-- Mobile Menu Button -->
                        <button
                            x-data="{ open: false }"
                            @click="open = !open"
                            class="md:hidden text-gray-400 hover:text-white p-2 transition-colors focus:outline-none"
                            aria-expanded="false"
                            aria-label="{{ __('nav.toggle_menu') }}"
                        >
                            <i class="fas fa-bars" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation -->
                <div
                    x-data="{ open: false }"
                    x-show="open"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="md:hidden border-t border-gray-800 py-2"
                    x-cloak
                >
                    <nav class="flex flex-col gap-1 px-2" role="navigation" aria-label="{{ __('nav.main_navigation') }}">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}" aria-current="{{ request()->routeIs('home') ? 'page' : null }}">
                            <i class="fas fa-home" aria-hidden="true"></i>
                            {{ __('nav.home') }}
                        </a>
                        <a href="{{ route('clips.list') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('clips.list') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}" aria-current="{{ request()->routeIs('clips.*') ? 'page' : null }}">
                            <i class="fas fa-list" aria-hidden="true"></i>
                            {{ __('nav.clips') }}
                        </a>
                        @auth
                            <a href="{{ route('clips.submit') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('clips.submit') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}" aria-current="{{ request()->routeIs('clips.submit') ? 'page' : null }}">
                                <i class="fas fa-plus" aria-hidden="true"></i>
                                {{ __('nav.submit') }}
                            </a>
                        @endauth
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1" role="main">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 border-t border-gray-800 mt-auto" role="contentinfo">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Brand Section -->
                    <div class="text-center md:text-left">
                        <div class="flex items-center space-x-2 mb-4">
                            <i class="fas fa-video text-purple-400" aria-hidden="true"></i>
                            <span class="font-bold text-lg text-white">{{ config('app.name') }}</span>
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            {{ __('footer.description') }}
                        </p>
                    </div>

                    <!-- Links Section -->
                    <div class="text-center md:text-left">
                        <h3 class="text-white font-semibold mb-4">{{ __('footer.links') }}</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors duration-200">{{ __('nav.home') }}</a></li>
                            <li><a href="{{ route('clips.list') }}" class="text-gray-400 hover:text-white transition-colors duration-200">{{ __('nav.clips') }}</a></li>
                            @auth
                                <li><a href="{{ route('clips.submit') }}" class="text-gray-400 hover:text-white transition-colors duration-200">{{ __('nav.submit') }}</a></li>
                            @endauth
                        </ul>
                    </div>

                    <!-- Legal Section -->
                    <div class="text-center md:text-left">
                        <h3 class="text-white font-semibold mb-4">{{ __('footer.legal') }}</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">{{ __('footer.privacy') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">{{ __('footer.terms') }}</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">{{ __('footer.contact') }}</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom Section -->
                <div class="border-t border-gray-800 mt-6 pt-4">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-gray-400 text-sm">
                            {{ __('footer.copyright', ['year' => date('Y'), 'app' => config('app.name')]) }}
                        </p>
                        <p class="text-gray-400 text-sm mt-2 md:mt-0">
                            {{ __('footer.made_with') }}
                            <i class="fas fa-heart text-red-400" aria-hidden="true"></i>
                            {{ __('footer.using') }}
                            <span class="text-purple-400">Laravel</span>
                        </p>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Cookie Banner -->
        <livewire:cookie-banner />

        @livewireScriptConfig
        @vite(['resources/js/livewire.js'])
        @stack('scripts_footer')
    </body>
</html>
