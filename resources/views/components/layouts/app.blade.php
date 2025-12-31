<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#1f2937" media="(prefers-color-scheme: dark)">
        <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="{{ $noIndex ?? false ? 'noindex, nofollow' : 'index, follow' }}">
        <meta name="description" content="{{ __('app.description') }}">

        <title>{{ isset($title) ? $title . ' · ' . config('app.name') : config('app.name') }}</title>

        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts_header')
    </head>
    <body class="min-h-screen flex flex-col bg-gray-950 text-white font-sans antialiased">
        <!-- Header -->
        <header class="bg-gray-900 border-b border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 text-white hover:text-purple-400 transition-colors duration-200">
                            <i class="fas fa-video text-lg"></i>
                            <span class="text-xl font-semibold">{{ config('app.name') }}</span>
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center space-x-6">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('home') ? 'text-purple-400 bg-purple-900/20' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                            <i class="fas fa-home"></i>
                            <span>{{ __('nav.home') }}</span>
                        </a>
                        <a href="{{ route('clips.list') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('clips.list') ? 'text-purple-400 bg-purple-900/20' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                            <i class="fas fa-list"></i>
                            <span>{{ __('nav.library') }}</span>
                        </a>
                        @auth
                            <a href="{{ route('clips.submit') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('clips.submit') ? 'text-purple-400 bg-purple-900/20' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                                <i class="fas fa-plus"></i>
                                <span>{{ __('nav.submit') }}</span>
                            </a>
                            <form method="POST" action="{{ route('auth.twitch.logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-400 hover:text-red-300 hover:bg-gray-800 rounded-md transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>{{ __('nav.logout') }}</span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('auth.login') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('auth.login') ? 'text-purple-400 bg-purple-900/20' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>{{ __('nav.login') }}</span>
                            </a>
                        @endauth
                    </nav>

                    <!-- Mobile Menu Button -->
                    <button
                        x-data="{ open: false }"
                        @click="open = !open"
                        class="md:hidden p-2 text-gray-400 hover:text-white transition-colors duration-200 rounded-md hover:bg-gray-800"
                        aria-label="{{ __('nav.toggle_menu') }}"
                        aria-expanded="false"
                    >
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>

                <!-- Mobile Navigation -->
                <div
                    x-data="{ open: false }"
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    class="md:hidden border-t border-gray-800 py-4"
                    x-cloak
                >
                    <nav class="flex flex-col space-y-2">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('home') ? 'text-purple-400 bg-purple-900/20' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                            <i class="fas fa-home"></i>
                            <span>{{ __('nav.home') }}</span>
                        </a>
                        <a href="{{ route('clips.list') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('clips.list') ? 'text-purple-400 bg-purple-900/20' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                            <i class="fas fa-list"></i>
                            <span>{{ __('nav.library') }}</span>
                        </a>
                        @auth
                            <a href="{{ route('clips.submit') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('clips.submit') ? 'text-purple-400 bg-purple-900/20' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                                <i class="fas fa-plus"></i>
                                <span>{{ __('nav.submit') }}</span>
                            </a>
                            <form method="POST" action="{{ route('auth.twitch.logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-400 hover:text-red-300 hover:bg-gray-800 rounded-md transition-colors duration-200 w-full text-left">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>{{ __('nav.logout') }}</span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('auth.login') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('auth.login') ? 'text-purple-400 bg-purple-900/20' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>{{ __('nav.login') }}</span>
                            </a>
                        @endauth
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 border-t border-gray-800 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Brand Section -->
                    <div class="text-center md:text-left">
                        <div class="flex items-center justify-center md:justify-start gap-2 text-purple-400 mb-3">
                            <i class="fas fa-video text-lg"></i>
                            <span class="text-lg font-semibold">{{ config('app.name') }}</span>
                        </div>
                        <p class="text-sm text-gray-400 leading-relaxed">
                            {{ __('footer.description') }}
                        </p>
                    </div>

                    <!-- Links Section -->
                    <div class="text-center">
                        <h4 class="text-sm font-medium text-white mb-3">{{ __('footer.quick_links') }}</h4>
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-white transition-colors duration-200">{{ __('nav.home') }}</a>
                            <a href="{{ route('clips.list') }}" class="text-sm text-gray-400 hover:text-white transition-colors duration-200">{{ __('nav.library') }}</a>
                            @auth
                                <a href="{{ route('clips.submit') }}" class="text-sm text-gray-400 hover:text-white transition-colors duration-200">{{ __('nav.submit') }}</a>
                            @else
                                <a href="{{ route('auth.login') }}" class="text-sm text-gray-400 hover:text-white transition-colors duration-200">{{ __('nav.login') }}</a>
                            @endauth
                        </div>
                    </div>

                    <!-- Community Section -->
                    <div class="text-center md:text-right">
                        <h4 class="text-sm font-medium text-white mb-3">{{ __('footer.community') }}</h4>
                        <div class="flex flex-col space-y-2">
                            <a href="https://github.com/ClipNook/ClipNook" target="_blank" rel="noopener" class="text-sm text-gray-400 hover:text-white transition-colors duration-200 inline-flex items-center gap-1">
                                <i class="fab fa-github"></i>
                                <span>{{ __('footer.github') }}</span>
                            </a>
                            <a href="https://twitch.tv" target="_blank" rel="noopener" class="text-sm text-gray-400 hover:text-white transition-colors duration-200 inline-flex items-center gap-1">
                                <i class="fab fa-twitch"></i>
                                <span>{{ __('footer.twitch') }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bottom Section -->
                <div class="border-t border-gray-800 mt-6 pt-4">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <p class="text-sm text-gray-400">
                            {{ __('footer.copyright', ['year' => date('Y'), 'app_name' => config('app.name')]) }}
                        </p>
                        <div class="flex items-center gap-4 text-sm text-gray-400">
                            <a href="#" class="hover:text-white transition-colors duration-200">{{ __('footer.privacy') }}</a>
                            <span>•</span>
                            <a href="#" class="hover:text-white transition-colors duration-200">{{ __('footer.terms') }}</a>
                        </div>
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
