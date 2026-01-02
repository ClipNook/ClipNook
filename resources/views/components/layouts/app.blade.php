<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="h-full {{ 'theme-' . (isset($_COOKIE['clipnook-theme']) && in_array($_COOKIE['clipnook-theme'], ['violet', 'blue', 'green', 'red', 'orange', 'pink', 'cyan', 'amber']) ? $_COOKIE['clipnook-theme'] : 'violet') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#09090b">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="{{ $noIndex ?? false ? 'noindex, nofollow' : 'index, follow' }}">
    <meta name="description" content="{{ __('app.description') }}">

    <title>{{ isset($title) ? $title . ' · ' . config('app.name') : config('app.name') }}</title>

    <script>
        // Load theme immediately to prevent FOUC
        (function() {
            const availableThemes = ['violet', 'blue', 'green', 'red', 'orange', 'pink', 'cyan', 'amber'];
            const savedTheme = localStorage.getItem('clipnook-theme');
            const defaultTheme = 'violet';

            const themeToUse = (savedTheme && availableThemes.includes(savedTheme)) ? savedTheme : defaultTheme;

            // Apply theme class immediately (remove existing theme classes first)
            document.documentElement.classList.forEach(className => {
                if (className.startsWith('theme-')) {
                    document.documentElement.classList.remove(className);
                }
            });
            document.documentElement.classList.add(`theme-${themeToUse}`);
        })();
    </script>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts_header')
</head>

<body class="theme-{{ session('theme', 'violet') }} bg-zinc-950 text-zinc-100 min-h-screen font-roboto antialiased"
    x-data="{ mobileMenuOpen: false, userMenuOpen: false, currentTheme: '{{ session('theme', 'violet') }}' }" @theme-changed.window="updateTheme($event.detail.theme)" x-init="loadTheme()">
    <!-- Header -->
    <header class="border-b border-zinc-800/50 bg-zinc-900/80 backdrop-blur-md sticky top-0 z-40">
        <!-- Subtle accent border at top -->
        <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500)/30 to-transparent"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}"
                        class="flex items-center gap-3 text-zinc-100 hover:text-(--color-accent-400) transition-colors group">
                        <i
                            class="fa-solid fa-video text-lg text-(--color-accent-400) group-hover:text-(--color-accent-500) transition-colors"></i>
                        <span class="font-semibold text-xl">{{ config('app.name') }}</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded transition-all duration-200 {{ request()->routeIs('home') ? 'text-zinc-100 bg-zinc-800 border border-(--color-accent-500)/50' : 'text-zinc-400 hover:text-(--color-accent-400) hover:bg-zinc-800/50 hover:border hover:border-(--color-accent-500)/30' }}">
                        <i
                            class="fa-solid fa-house text-xs {{ request()->routeIs('home') ? 'text-(--color-accent-400)' : '' }}"></i>
                        {{ __('nav.home') }}
                    </a>
                    <a href="{{ route('clips.list') }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded transition-all duration-200 {{ request()->routeIs('clips.*') && !request()->routeIs('clips.submit') ? 'text-zinc-100 bg-zinc-800 border border-(--color-accent-500)/50' : 'text-zinc-400 hover:text-(--color-accent-400) hover:bg-zinc-800/50 hover:border hover:border-(--color-accent-500)/30' }}">
                        <i
                            class="fa-solid fa-film text-xs {{ request()->routeIs('clips.*') && !request()->routeIs('clips.submit') ? 'text-(--color-accent-400)' : '' }}"></i>
                        {{ __('nav.clips') }}
                    </a>
                    <a href="{{ route('games.list') }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded transition-all duration-200 {{ request()->routeIs('games.*') ? 'text-zinc-100 bg-zinc-800 border border-(--color-accent-500)/50' : 'text-zinc-400 hover:text-(--color-accent-400) hover:bg-zinc-800/50 hover:border hover:border-(--color-accent-500)/30' }}">
                        <i
                            class="fa-solid fa-gamepad text-xs {{ request()->routeIs('games.*') ? 'text-(--color-accent-400)' : '' }}"></i>
                        {{ __('nav.games') }}
                    </a>
                    @auth
                        <a href="{{ route('clips.submit') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded transition-all duration-200 ml-2 {{ request()->routeIs('clips.submit') ? 'text-zinc-100 bg-(--color-accent-500) border border-(--color-accent-600) shadow-lg shadow-(--color-accent-500)/20' : 'text-zinc-400 hover:text-zinc-100 border border-zinc-700 hover:border-(--color-accent-500)/50 hover:shadow-md hover:shadow-(--color-accent-500)/10' }}">
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
                            <button @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center gap-3 text-zinc-400 hover:text-(--color-accent-400) px-3 py-2 rounded text-sm transition-colors hover:bg-zinc-800/50">
                                <img src="{{ auth()->user()->getAvatarSourceAttribute() }}"
                                    alt="{{ auth()->user()->twitch_display_name }}"
                                    class="w-8 h-8 rounded-full object-cover border border-zinc-700 hover:border-(--color-accent-500)/50 transition-colors">
                                <span class="hidden lg:block">{{ auth()->user()->twitch_display_name }}</span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform"
                                    :class="{ 'rotate-180': userMenuOpen }"></i>
                            </button>

                            <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition
                                class="absolute right-0 mt-2 w-72 bg-zinc-900/95 backdrop-blur-xl rounded-xl shadow-2xl border border-zinc-700/50 z-50 ring-1 ring-zinc-600/20"
                                x-cloak>
                                <div class="px-5 py-4 border-b border-zinc-700/50 bg-gradient-to-r from-zinc-800/50 to-zinc-900/50 rounded-t-xl">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ auth()->user()->getAvatarSourceAttribute() }}"
                                            alt="{{ auth()->user()->twitch_display_name }}"
                                            class="w-10 h-10 rounded-full object-cover border-2 border-(--color-accent-500)/30 shadow-sm">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-zinc-500 uppercase tracking-wider font-medium">{{ __('nav.signed_in_as') }}</p>
                                            <p class="text-sm font-semibold text-zinc-100 truncate">{{ auth()->user()->twitch_display_name }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="py-2">
                                    <a href="#" @click="userMenuOpen = false"
                                        class="flex items-center gap-3 px-5 py-3 text-sm text-zinc-300 hover:text-(--color-accent-400) hover:bg-(--color-accent-500)/10 transition-all duration-200 group">
                                        <div class="w-8 h-8 bg-zinc-800 group-hover:bg-(--color-accent-500)/20 rounded-lg flex items-center justify-center transition-colors">
                                            <i class="fa-solid fa-user text-xs text-zinc-400 group-hover:text-(--color-accent-400) transition-colors"></i>
                                        </div>
                                        <span class="font-medium">{{ __('nav.profile') }}</span>
                                    </a>
                                    <a href="{{ route('settings') }}" @click="userMenuOpen = false"
                                        class="flex items-center gap-3 px-5 py-3 text-sm text-zinc-300 hover:text-(--color-accent-400) hover:bg-(--color-accent-500)/10 transition-all duration-200 group">
                                        <div class="w-8 h-8 bg-zinc-800 group-hover:bg-(--color-accent-500)/20 rounded-lg flex items-center justify-center transition-colors">
                                            <i class="fa-solid fa-gear text-xs text-zinc-400 group-hover:text-(--color-accent-400) transition-colors"></i>
                                        </div>
                                        <span class="font-medium">{{ __('nav.settings') }}</span>
                                    </a>
                                    @if (auth()->user()->isStaff())
                                        <div class="border-t border-zinc-700/50 my-2 mx-3"></div>
                                        <a href="{{ route('admin.clips') }}" @click="userMenuOpen = false"
                                            class="flex items-center gap-3 px-5 py-3 text-sm text-violet-400 hover:text-violet-300 hover:bg-violet-500/10 transition-all duration-200 group">
                                            <div class="w-8 h-8 bg-zinc-800 group-hover:bg-violet-500/20 rounded-lg flex items-center justify-center transition-colors">
                                                <i class="fa-solid fa-shield text-xs text-zinc-400 group-hover:text-violet-400 transition-colors"></i>
                                            </div>
                                            <span class="font-medium">{{ __('nav.admin') }}</span>
                                        </a>
                                    @endif
                                </div>
                                <div class="border-t border-zinc-700/50 py-2 rounded-b-xl">
                                    <form method="POST" action="{{ route('auth.twitch.logout') }}">
                                        @csrf
                                        <button type="submit" @click="userMenuOpen = false"
                                            class="flex items-center gap-3 w-full px-5 py-3 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-all duration-200 group rounded-b-xl">
                                            <div class="w-8 h-8 bg-zinc-800 group-hover:bg-red-500/20 rounded-lg flex items-center justify-center transition-colors">
                                                <i class="fa-solid fa-right-from-bracket text-xs text-zinc-400 group-hover:text-red-400 transition-colors"></i>
                                            </div>
                                            <span class="font-medium">{{ __('nav.logout') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('auth.login') }}"
                            class="hidden md:flex items-center gap-2 text-zinc-100 bg-(--color-accent-500) hover:bg-(--color-accent-600) px-4 py-2 rounded text-sm font-medium transition-all duration-200 shadow-lg shadow-(--color-accent-500)/20 hover:shadow-xl hover:shadow-(--color-accent-500)/30">
                            <i class="fa-brands fa-twitch"></i>
                            {{ __('nav.login') }}
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden text-zinc-400 hover:text-(--color-accent-400) p-2 rounded transition-colors hover:bg-zinc-800/50">
                        <i class="fa-solid fa-bars text-lg" :class="{ 'fa-xmark': mobileMenuOpen }"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-zinc-800 py-4" x-cloak>
                <nav class="flex flex-col gap-2">
                    <a href="{{ route('home') }}"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded transition-all duration-200 {{ request()->routeIs('home') ? 'text-zinc-100 bg-zinc-800 border-l-4 border-(--color-accent-500)' : 'text-zinc-400 hover:text-(--color-accent-400) hover:bg-zinc-800/50' }}">
                        <i
                            class="fa-solid fa-house w-5 text-center {{ request()->routeIs('home') ? 'text-(--color-accent-400)' : '' }}"></i>
                        {{ __('nav.home') }}
                    </a>
                    <a href="{{ route('clips.list') }}"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded transition-all duration-200 {{ request()->routeIs('clips.*') && !request()->routeIs('clips.submit') ? 'text-zinc-100 bg-zinc-800 border-l-4 border-(--color-accent-500)' : 'text-zinc-400 hover:text-(--color-accent-400) hover:bg-zinc-800/50' }}">
                        <i
                            class="fa-solid fa-film w-5 text-center {{ request()->routeIs('clips.*') && !request()->routeIs('clips.submit') ? 'text-(--color-accent-400)' : '' }}"></i>
                        {{ __('nav.clips') }}
                    </a>
                    <a href="{{ route('games.list') }}"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded transition-all duration-200 {{ request()->routeIs('games.*') ? 'text-zinc-100 bg-zinc-800 border-l-4 border-(--color-accent-500)' : 'text-zinc-400 hover:text-(--color-accent-400) hover:bg-zinc-800/50' }}">
                        <i
                            class="fa-solid fa-gamepad w-5 text-center {{ request()->routeIs('games.*') ? 'text-(--color-accent-400)' : '' }}"></i>
                        {{ __('nav.games') }}
                    </a>
                    @auth
                        <a href="{{ route('clips.submit') }}"
                            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded transition-all duration-200 {{ request()->routeIs('clips.submit') ? 'text-zinc-100 bg-(--color-accent-500) border-l-4 border-(--color-accent-600)' : 'text-zinc-400 hover:text-zinc-100 border border-zinc-700 hover:border-(--color-accent-500)/50' }}">
                            <i class="fa-solid fa-plus w-5 text-center"></i>
                            {{ __('nav.submit') }}
                        </a>

                        <div class="border-t border-zinc-800 mt-4 pt-4">
                            <div class="px-4 mb-2">
                                <p class="text-xs text-zinc-500 uppercase tracking-wide">{{ __('nav.account') }}</p>
                            </div>
                            <a href="#"
                                class="flex items-center gap-3 px-4 py-3 text-sm text-zinc-400 hover:text-(--color-accent-400) hover:bg-zinc-800 transition-colors rounded">
                                <i class="fa-solid fa-user w-5 text-center"></i>
                                {{ __('nav.profile') }}
                            </a>
                            <a href="{{ route('settings') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm text-zinc-400 hover:text-(--color-accent-400) hover:bg-zinc-800 transition-colors rounded">
                                <i class="fa-solid fa-gear w-5 text-center"></i>
                                {{ __('nav.settings') }}
                            </a>
                            @if (auth()->user()->isStaff())
                                <a href="{{ route('admin.clips') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-sm text-violet-400 hover:text-violet-300 hover:bg-zinc-800 transition-colors rounded">
                                    <i class="fa-solid fa-shield w-5 text-center"></i>
                                    {{ __('nav.admin') }}
                                </a>
                            @endif
                            <form method="POST" action="{{ route('auth.twitch.logout') }}" class="mt-2">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-3 w-full px-4 py-3 text-sm text-red-400 hover:text-red-300 hover:bg-zinc-800 transition-colors rounded">
                                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                                    {{ __('nav.logout') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="border-t border-zinc-800 mt-4 pt-4">
                            <a href="{{ route('auth.login') }}"
                                class="flex items-center justify-center gap-2 w-full text-zinc-100 bg-(--color-accent-500) hover:bg-(--color-accent-600) px-4 py-3 rounded text-sm font-medium transition-all duration-200 shadow-lg shadow-(--color-accent-500)/20 hover:shadow-xl hover:shadow-(--color-accent-500)/30">
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

    <!-- Enhanced Accent Border -->
    <div class="relative">
        <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500) to-transparent opacity-60">
        </div>
        <div
            class="h-px bg-linear-to-r from-transparent via-(--color-accent-400)/30 to-transparent opacity-40 -mt-px">
        </div>
        <div
            class="absolute inset-0 h-px bg-linear-to-r from-transparent via-(--color-accent-500)/20 to-transparent opacity-20 blur-sm">
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-zinc-900 border-t border-zinc-800 mt-auto relative">
        <!-- Subtle accent border at top -->
        <div class="h-px bg-linear-to-r from-transparent via-(--color-accent-500)/40 to-transparent"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                <!-- Brand Section -->
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-zinc-800 rounded flex items-center justify-center ring-1 ring-(--color-accent-500)/20">
                            <i class="fa-solid fa-video text-xl text-(--color-accent-400)"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-zinc-100">{{ config('app.name') }}</h3>
                            <p class="text-xs text-zinc-500 uppercase tracking-wide">{{ __('footer.open_source') }}
                            </p>
                        </div>
                    </div>
                    <p class="text-zinc-400 text-sm leading-relaxed">
                        {{ __('footer.description') }}
                    </p>
                    <!-- Social Links with accent -->
                    <div class="flex gap-3 mt-4">
                        <a href="https://github.com/ClipNook/ClipNook" target="_blank" rel="noopener noreferrer"
                            class="w-8 h-8 bg-zinc-800 hover:bg-(--color-accent-500)/10 rounded flex items-center justify-center text-zinc-400 hover:text-(--color-accent-400) transition-all duration-200 ring-1 ring-transparent hover:ring-(--color-accent-500)/30">
                            <i class="fa-brands fa-github text-sm"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-zinc-300 font-medium mb-4 flex items-center gap-2">
                        <div class="w-1 h-4 bg-(--color-accent-500) rounded-full"></div>
                        {{ __('footer.quick_links') }}
                    </h4>
                    <div class="flex flex-col gap-3">
                        <a href="{{ route('home') }}"
                            class="text-zinc-500 hover:text-(--color-accent-400) text-sm transition-colors flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-xs text-transparent group-hover:text-(--color-accent-500) transition-colors"></i>
                            {{ __('nav.home') }}
                        </a>
                        <a href="{{ route('clips.list') }}"
                            class="text-zinc-500 hover:text-(--color-accent-400) text-sm transition-colors flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-xs text-transparent group-hover:text-(--color-accent-500) transition-colors"></i>
                            {{ __('nav.clips') }}
                        </a>
                        <a href="{{ route('games.list') }}"
                            class="text-zinc-500 hover:text-(--color-accent-400) text-sm transition-colors flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-xs text-transparent group-hover:text-(--color-accent-500) transition-colors"></i>
                            {{ __('nav.games') }}
                        </a>
                        @auth
                            <a href="{{ route('clips.submit') }}"
                                class="text-zinc-500 hover:text-(--color-accent-400) text-sm transition-colors flex items-center gap-2 group">
                                <i
                                    class="fa-solid fa-chevron-right text-xs text-transparent group-hover:text-(--color-accent-500) transition-colors"></i>
                                {{ __('nav.submit') }}
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="text-zinc-300 font-medium mb-4 flex items-center gap-2">
                        <div class="w-1 h-4 bg-(--color-accent-500) rounded-full"></div>
                        {{ __('footer.legal') }}
                    </h4>
                    <div class="flex flex-col gap-3">
                        <a href="{{ route('legal.imprint') }}"
                            class="text-zinc-500 hover:text-(--color-accent-400) text-sm transition-colors flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-xs text-transparent group-hover:text-(--color-accent-500) transition-colors"></i>
                            {{ __('footer.imprint') }}
                        </a>
                        <a href="{{ route('legal.privacy') }}"
                            class="text-zinc-500 hover:text-(--color-accent-400) text-sm transition-colors flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-xs text-transparent group-hover:text-(--color-accent-500) transition-colors"></i>
                            {{ __('footer.privacy_policy') }}
                        </a>
                        <a href="{{ route('legal.terms') }}"
                            class="text-zinc-500 hover:text-(--color-accent-400) text-sm transition-colors flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-xs text-transparent group-hover:text-(--color-accent-500) transition-colors"></i>
                            {{ __('footer.terms_of_service') }}
                        </a>
                    </div>
                </div>

                <!-- Community -->
                <div>
                    <h4 class="text-zinc-300 font-medium mb-4 flex items-center gap-2">
                        <div class="w-1 h-4 bg-(--color-accent-500) rounded-full"></div>
                        {{ __('footer.community') }}
                    </h4>
                    <div class="flex flex-col gap-3">
                        <a href="https://github.com/ClipNook/ClipNook" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-zinc-100 rounded transition-all duration-200 border border-zinc-700 hover:border-(--color-accent-500)/30 text-sm group">
                            <i
                                class="fa-brands fa-github group-hover:text-(--color-accent-400) transition-colors"></i>
                            {{ __('footer.view_source') }}
                        </a>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-code-branch text-(--color-accent-400) text-sm"></i>
                            <span
                                class="text-(--color-accent-400) text-sm font-medium">{{ __('footer.open_source') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="border-t border-zinc-800 mt-12 pt-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-zinc-500 text-sm">
                        © {{ date('Y') }} {{ config('app.name') }}. {{ __('footer.all_rights_reserved') }}
                    </p>
                    <div class="flex items-center gap-4">
                        <p class="text-zinc-500 text-sm">
                            {{ __('footer.made_with') }}
                            <i class="fa-solid fa-heart text-(--color-accent-500) mx-1 animate-pulse"></i>
                            <span class="text-zinc-400 font-medium">{{ config('app.name') }}</span>
                        </p>
                        <!-- Theme Selector -->
                        <div class="flex items-center">
                            <livewire:theme-selector />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @livewireScriptConfig
    @vite(['resources/js/livewire.js'])

    <script>
        function updateTheme(newTheme) {
            // Remove all theme classes
            document.body.classList.forEach(className => {
                if (className.startsWith('theme-')) {
                    document.body.classList.remove(className);
                }
            });

            // Add new theme class
            document.body.classList.add(`theme-${newTheme}`);

            // Update Alpine data
            if (window.Alpine) {
                Alpine.store('theme', newTheme);
            }
        }

        function loadTheme() {
            const availableThemes = ['violet', 'blue', 'green', 'red', 'orange', 'pink', 'cyan', 'amber'];
            const savedTheme = localStorage.getItem('clipnook-theme');
            const sessionTheme = '{{ session('theme', 'violet') }}';

            // Priority: localStorage > session > default
            const themeToUse = (savedTheme && availableThemes.includes(savedTheme)) ? savedTheme :
                (sessionTheme && availableThemes.includes(sessionTheme)) ? sessionTheme : 'violet';

            // Apply theme (should already be applied from head script, but ensure it's correct)
            updateTheme(themeToUse);
        }
    </script>

    @stack('scripts_footer')

    <!-- Notification System -->
    <x-notification-system />

</body>

</html>
