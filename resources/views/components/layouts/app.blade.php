<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f0f0f">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="{{ $noIndex ?? false ? 'noindex, nofollow' : 'index, follow' }}">
    <meta name="description" content="{{ __('app.description') }}">

    <title>{{ isset($title) ? $title . ' · ' . config('app.name') : config('app.name') }}</title>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts_header')
</head>
<body class="min-h-screen bg-neutral-950 text-neutral-100 font-roboto antialiased" x-data="{ mobileMenuOpen: false, userMenuOpen: false }">
    <!-- Header -->
    <header class="border-b border-neutral-800 bg-neutral-900/50 backdrop-blur-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 text-neutral-100 hover:text-neutral-200 transition-colors">
                        <i class="fa-solid fa-video text-lg text-neutral-400"></i>
                        <span class="font-semibold text-xl">{{ config('app.name') }}</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('home') ? 'text-neutral-100 bg-neutral-800' : 'text-neutral-400 hover:text-neutral-100 hover:bg-neutral-800' }}">
                        <i class="fa-solid fa-house text-xs"></i>
                        {{ __('nav.home') }}
                    </a>
                    <a href="{{ route('clips.list') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('clips.*') && !request()->routeIs('clips.submit') ? 'text-neutral-100 bg-neutral-800' : 'text-neutral-400 hover:text-neutral-100 hover:bg-neutral-800' }}">
                        <i class="fa-solid fa-film text-xs"></i>
                        {{ __('nav.clips') }}
                    </a>
                    <a href="{{ route('games.list') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('games.*') ? 'text-neutral-100 bg-neutral-800' : 'text-neutral-400 hover:text-neutral-100 hover:bg-neutral-800' }}">
                        <i class="fa-solid fa-gamepad text-xs"></i>
                        {{ __('nav.games') }}
                    </a>
                    @auth
                        <a href="{{ route('clips.submit') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-colors ml-2 {{ request()->routeIs('clips.submit') ? 'text-neutral-100 bg-purple-600' : 'text-neutral-400 hover:text-neutral-100 border border-neutral-700 hover:border-neutral-600' }}">
                            <i class="fa-solid fa-plus text-xs"></i>
                            {{ __('nav.submit') }}
                        </a>
                    @endauth
                </nav>

                <!-- User Menu / Auth -->
                <div class="flex items-center gap-4">
                    @auth
                        <!-- User Menu -->
                        <div class="relative hidden md:block">
                            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-3 text-neutral-400 hover:text-neutral-100 px-3 py-2 rounded-md text-sm transition-colors">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->twitch_display_name }}" class="w-8 h-8 rounded-md object-cover border border-neutral-700">
                                <span class="hidden lg:block">{{ auth()->user()->twitch_display_name }}</span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': userMenuOpen }"></i>
                            </button>

                            <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition class="absolute right-0 mt-2 w-64 bg-neutral-800 rounded-md shadow-lg border border-neutral-700 z-50" x-cloak>
                                <div class="px-4 py-3 border-b border-neutral-700">
                                    <p class="text-xs text-neutral-500 uppercase tracking-wide">{{ __('nav.signed_in_as') }}</p>
                                    <p class="text-sm font-medium text-neutral-100 truncate">{{ auth()->user()->twitch_display_name }}</p>
                                </div>
                                <div class="py-1">
                                    <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-neutral-300 hover:text-neutral-100 hover:bg-neutral-700 transition-colors">
                                        <i class="fa-solid fa-user w-4 text-center"></i>
                                        {{ __('nav.profile') }}
                                    </a>
                                    <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-neutral-300 hover:text-neutral-100 hover:bg-neutral-700 transition-colors">
                                        <i class="fa-solid fa-gear w-4 text-center"></i>
                                        {{ __('nav.settings') }}
                                    </a>
                                    @if(auth()->user()->isStaff())
                                        <div class="border-t border-neutral-700 my-1"></div>
                                        <a href="{{ route('admin.clips') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-purple-400 hover:text-purple-300 hover:bg-neutral-700 transition-colors">
                                            <i class="fa-solid fa-shield w-4 text-center"></i>
                                            {{ __('nav.admin') }}
                                        </a>
                                    @endif
                                </div>
                                <div class="border-t border-neutral-700 py-1">
                                    <form method="POST" action="{{ route('auth.twitch.logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-3 w-full text-left px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-neutral-700 transition-colors">
                                            <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                                            {{ __('nav.logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('auth.login') }}" class="hidden md:flex items-center gap-2 text-neutral-100 bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fa-brands fa-twitch"></i>
                            {{ __('nav.login') }}
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-neutral-400 hover:text-neutral-100 p-2 rounded-md transition-colors">
                        <i class="fa-solid fa-bars text-lg" :class="{ 'fa-xmark': mobileMenuOpen }"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-neutral-800 py-4" x-cloak>
                <nav class="flex flex-col gap-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('home') ? 'text-neutral-100 bg-neutral-800' : 'text-neutral-400 hover:text-neutral-100 hover:bg-neutral-800' }}">
                        <i class="fa-solid fa-house w-5 text-center"></i>
                        {{ __('nav.home') }}
                    </a>
                    <a href="{{ route('clips.list') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('clips.*') && !request()->routeIs('clips.submit') ? 'text-neutral-100 bg-neutral-800' : 'text-neutral-400 hover:text-neutral-100 hover:bg-neutral-800' }}">
                        <i class="fa-solid fa-film w-5 text-center"></i>
                        {{ __('nav.clips') }}
                    </a>
                    <a href="{{ route('games.list') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('games.*') ? 'text-neutral-100 bg-neutral-800' : 'text-neutral-400 hover:text-neutral-100 hover:bg-neutral-800' }}">
                        <i class="fa-solid fa-gamepad w-5 text-center"></i>
                        {{ __('nav.games') }}
                    </a>
                    @auth
                        <a href="{{ route('clips.submit') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('clips.submit') ? 'text-neutral-100 bg-purple-600' : 'text-neutral-400 hover:text-neutral-100 border border-neutral-700' }}">
                            <i class="fa-solid fa-plus w-5 text-center"></i>
                            {{ __('nav.submit') }}
                        </a>

                        <div class="border-t border-neutral-800 mt-4 pt-4">
                            <div class="px-4 mb-2">
                                <p class="text-xs text-neutral-500 uppercase tracking-wide">{{ __('nav.account') }}</p>
                            </div>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm text-neutral-400 hover:text-neutral-100 hover:bg-neutral-800 transition-colors rounded-md">
                                <i class="fa-solid fa-user w-5 text-center"></i>
                                {{ __('nav.profile') }}
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm text-neutral-400 hover:text-neutral-100 hover:bg-neutral-800 transition-colors rounded-md">
                                <i class="fa-solid fa-gear w-5 text-center"></i>
                                {{ __('nav.settings') }}
                            </a>
                            @if(auth()->user()->isStaff())
                                <a href="{{ route('admin.clips') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-purple-400 hover:text-purple-300 hover:bg-neutral-800 transition-colors rounded-md">
                                    <i class="fa-solid fa-shield w-5 text-center"></i>
                                    {{ __('nav.admin') }}
                                </a>
                            @endif
                            <form method="POST" action="{{ route('auth.twitch.logout') }}" class="mt-2">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 text-sm text-red-400 hover:text-red-300 hover:bg-neutral-800 transition-colors rounded-md">
                                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                                    {{ __('nav.logout') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="border-t border-neutral-800 mt-4 pt-4">
                            <a href="{{ route('auth.login') }}" class="flex items-center justify-center gap-2 w-full text-neutral-100 bg-purple-600 hover:bg-purple-700 px-4 py-3 rounded-md text-sm font-medium transition-colors">
                                <i class="fa-brands fa-twitch"></i>
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
    <footer class="bg-neutral-900/50 border-t border-neutral-800/50 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col items-center justify-center space-y-6">
                <!-- Brand & Description -->
                <div class="text-center">
                    <div class="flex items-center justify-center gap-3 mb-3">
                        <i class="fa-solid fa-video text-xl text-neutral-400"></i>
                        <span class="font-semibold text-xl text-neutral-100">{{ config('app.name') }}</span>
                    </div>
                    <p class="text-neutral-400 text-sm max-w-md mx-auto leading-relaxed">
                        {{ __('footer.description') }}
                    </p>
                </div>

                <!-- GitHub & Open Source -->
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <a href="https://github.com/ClipNook/ClipNook" target="_blank" rel="noopener noreferrer"
                       class="flex items-center gap-2 px-4 py-2 bg-neutral-800 hover:bg-neutral-700 text-neutral-300 hover:text-neutral-100 rounded-md transition-colors border border-neutral-700">
                        <i class="fa-brands fa-github text-lg"></i>
                        <span class="text-sm font-medium">{{ __('footer.view_source') }}</span>
                    </a>
                    <div class="flex items-center gap-2 px-3 py-1 bg-green-900/20 border border-green-700/30 rounded-full">
                        <i class="fa-solid fa-code-branch text-green-400 text-xs"></i>
                        <span class="text-xs text-green-300 font-medium">{{ __('footer.open_source') }}</span>
                    </div>
                </div>

                <!-- Bottom Links & Copyright -->
                <div class="flex flex-col sm:flex-row items-center justify-between w-full pt-6 border-t border-neutral-800/50">
                    <div class="flex items-center gap-6 mb-4 sm:mb-0">
                        <a href="{{ route('home') }}" class="text-neutral-500 hover:text-neutral-300 text-sm transition-colors">{{ __('nav.home') }}</a>
                        <a href="{{ route('clips.list') }}" class="text-neutral-500 hover:text-neutral-300 text-sm transition-colors">{{ __('nav.clips') }}</a>
                        @auth
                            <a href="{{ route('clips.submit') }}" class="text-neutral-500 hover:text-neutral-300 text-sm transition-colors">{{ __('nav.submit') }}</a>
                        @endauth
                    </div>

                    <div class="flex items-center gap-4">
                        <p class="text-neutral-500 text-xs">
                            {{ __('footer.made_with') }}
                            <i class="fa-solid fa-heart text-red-500 mx-1"></i>
                            <span class="text-neutral-400">Laravel</span>
                        </p>
                        <p class="text-neutral-500 text-xs border-l border-neutral-800 pl-4">
                            © {{ date('Y') }} {{ config('app.name') }}
                        </p>
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
