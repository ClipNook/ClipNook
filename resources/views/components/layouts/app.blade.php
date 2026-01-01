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
        <header class="bg-gray-900 border-b border-gray-800" x-data="{ mobileMenuOpen: false, userMenuOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 text-white hover:text-gray-300 transition-colors" aria-label="{{ __('nav.home') }}">
                            <i class="fas fa-video text-gray-400" aria-hidden="true"></i>
                            <span class="font-semibold text-lg">{{ config('app.name') }}</span>
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center gap-1" role="navigation" aria-label="{{ __('nav.main_navigation') }}">
                        <a href="{{ route('home') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}" aria-current="{{ request()->routeIs('home') ? 'page' : null }}">
                            <i class="fas fa-home mr-2" aria-hidden="true"></i>
                            {{ __('nav.home') }}
                        </a>
                        <a href="{{ route('clips.list') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('clips.*') && !request()->routeIs('clips.submit') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}" aria-current="{{ request()->routeIs('clips.*') ? 'page' : null }}">
                            <i class="fas fa-film mr-2" aria-hidden="true"></i>
                            {{ __('nav.clips') }}
                        </a>
                        <a href="{{ route('games.list') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('games.*') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}" aria-current="{{ request()->routeIs('games.*') ? 'page' : null }}">
                            <i class="fas fa-gamepad mr-2" aria-hidden="true"></i>
                            {{ __('nav.games') }}
                        </a>
                        @auth
                            <a href="{{ route('clips.submit') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('clips.submit') ? 'text-white bg-purple-600' : 'text-gray-400 hover:text-white hover:bg-gray-800 border border-gray-700' }}" aria-current="{{ request()->routeIs('clips.submit') ? 'page' : null }}">
                                <i class="fas fa-plus-circle mr-2" aria-hidden="true"></i>
                                {{ __('nav.submit') }}
                            </a>
                        @endauth
                    </nav>

                    <!-- User Menu -->
                    <div class="flex items-center gap-3">
                        @auth
                            <div class="relative hidden md:block">
                                <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 text-gray-400 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-gray-700" aria-expanded="false" aria-haspopup="true">
                                    <img 
                                        src="{{ auth()->user()->avatar_url }}" 
                                        alt="{{ auth()->user()->twitch_display_name }}" 
                                        class="w-8 h-8 rounded-full object-cover border border-gray-600"
                                    >
                                    <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': userMenuOpen }" aria-hidden="true"></i>
                                </button>

                                <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-gray-800 rounded-lg shadow-lg border border-gray-700 z-50 overflow-hidden" role="menu" aria-orientation="vertical" x-cloak>
                                    <div class="px-4 py-3 border-b border-gray-700">
                                        <p class="text-sm text-gray-400">{{ __('nav.signed_in_as') }}</p>
                                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->twitch_display_name }}</p>
                                    </div>
                                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-gray-700 transition-colors" role="menuitem">
                                        <i class="fas fa-user w-4 text-center" aria-hidden="true"></i>
                                        {{ __('nav.profile') }}
                                    </a>
                                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-gray-700 transition-colors" role="menuitem">
                                        <i class="fas fa-cog w-4 text-center" aria-hidden="true"></i>
                                        {{ __('nav.settings') }}
                                    </a>
                                    @if(auth()->user()->isStaff())
                                        <div class="border-t border-gray-700">
                                            <a href="{{ route('admin.clips') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-purple-400 hover:text-purple-300 hover:bg-gray-700 transition-colors" role="menuitem">
                                                <i class="fas fa-shield-alt w-4 text-center" aria-hidden="true"></i>
                                                {{ __('nav.admin') }}
                                            </a>
                                        </div>
                                    @endif
                                    <div class="border-t border-gray-700">
                                        <form method="POST" action="{{ route('auth.twitch.logout') }}">
                                            @csrf
                                            <button type="submit" class="flex items-center gap-3 w-full text-left px-4 py-3 text-sm text-red-400 hover:text-red-300 hover:bg-gray-700 transition-colors" role="menuitem">
                                                <i class="fas fa-sign-out-alt w-4 text-center" aria-hidden="true"></i>
                                                {{ __('nav.logout') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('auth.login') }}" class="hidden md:flex items-center gap-2 text-white bg-purple-600 hover:bg-purple-700 px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i class="fab fa-twitch" aria-hidden="true"></i>
                                {{ __('nav.login') }}
                            </a>
                        @endauth

                        <!-- Mobile Menu Button -->
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden text-gray-300 hover:text-white p-2 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500"
                            aria-expanded="false"
                            aria-label="{{ __('nav.toggle_menu') }}"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" x-cloak />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation -->
                <div
                    x-show="mobileMenuOpen"
                    @click.away="mobileMenuOpen = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="md:hidden border-t border-gray-800 py-3"
                    x-cloak
                >
                    <nav class="flex flex-col gap-2 px-3" role="navigation" aria-label="{{ __('nav.main_navigation') }}">
                        <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}" aria-current="{{ request()->routeIs('home') ? 'page' : null }}">
                            <i class="fas fa-home w-5 text-center" aria-hidden="true"></i>
                            {{ __('nav.home') }}
                        </a>
                        <a href="{{ route('clips.list') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('clips.*') && !request()->routeIs('clips.submit') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}" aria-current="{{ request()->routeIs('clips.*') ? 'page' : null }}">
                            <i class="fas fa-film w-5 text-center" aria-hidden="true"></i>
                            {{ __('nav.clips') }}
                        </a>
                        <a href="{{ route('games.list') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('games.*') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}" aria-current="{{ request()->routeIs('games.*') ? 'page' : null }}">
                            <i class="fas fa-gamepad w-5 text-center" aria-hidden="true"></i>
                            {{ __('nav.games') }}
                        </a>
                        @auth
                            <a href="{{ route('clips.submit') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('clips.submit') ? 'text-white bg-purple-600' : 'text-gray-400 hover:text-white hover:bg-gray-800 border border-gray-700' }}" aria-current="{{ request()->routeIs('clips.submit') ? 'page' : null }}">
                                <i class="fas fa-plus-circle w-5 text-center" aria-hidden="true"></i>
                                {{ __('nav.submit') }}
                            </a>
                            
                            <div class="border-t border-gray-800 mt-2 pt-2">
                                <div class="px-4 py-2">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('nav.account') }}</p>
                                </div>
                                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-800 transition-colors">
                                    <i class="fas fa-user w-5 text-center" aria-hidden="true"></i>
                                    {{ __('nav.profile') }}
                                </a>
                                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-800 transition-colors">
                                    <i class="fas fa-cog w-5 text-center" aria-hidden="true"></i>
                                    {{ __('nav.settings') }}
                                </a>
                                @if(auth()->user()->isStaff())
                                    <a href="{{ route('admin.clips') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm text-purple-400 hover:text-purple-300 hover:bg-gray-800 transition-colors">
                                        <i class="fas fa-shield-alt w-5 text-center" aria-hidden="true"></i>
                                        {{ __('nav.admin') }}
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('auth.twitch.logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 rounded-lg text-sm text-red-400 hover:text-red-300 hover:bg-gray-800 transition-colors">
                                        <i class="fas fa-sign-out-alt w-5 text-center" aria-hidden="true"></i>
                                        {{ __('nav.logout') }}
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="border-t border-gray-800 mt-2 pt-3">
                                <a href="{{ route('auth.login') }}" class="flex items-center justify-center gap-2 w-full text-white bg-purple-600 hover:bg-purple-700 px-5 py-3 rounded-lg text-sm font-medium transition-colors">
                                    <i class="fab fa-twitch" aria-hidden="true"></i>
                                    {{ __('nav.login') }}
                                </a>
                            </div>
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
                        <div class=\"flex items-center gap-2 mb-4\">
                            <i class=\"fas fa-video text-gray-400\" aria-hidden=\"true\"></i>
                            <span class=\"font-semibold text-lg text-white\">{{ config('app.name') }}</span>
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
                            <span class="text-gray-300">Laravel</span>
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
