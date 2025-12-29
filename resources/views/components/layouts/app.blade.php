<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full"
    @if (session('theme')) data-theme="{{ session('theme') }}" @endif>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0f172a" media="(prefers-color-scheme: dark)">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="{{ $noIndex ?? false ? 'noindex, nofollow' : 'index, follow' }}">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="user-accent" content="{{ auth()->user()->accent_color ?? 'purple' }}">
    @endauth

    <title>{{ isset($title) ? $title . ' · ' . config('app.name') : config('app.name') }}</title>

    {{-- Dark mode JS: client override (localStorage) --}}
    <script>
        (function() {
            'use strict';

            // Theme mode (auto/dark/light)
            try {
            var theme = localStorage.theme;
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (theme === 'auto') {
                if (prefersDark) {
                document.documentElement.classList.add('dark');
                } else {
                document.documentElement.classList.remove('dark');
                }
            } else if (theme === 'dark' || (!theme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            } catch (e) {}
        })();
    </script>
    {{-- Accent color JS: client override (localStorage) --}}
    <script>
        (function() {
            'use strict';
            // Set global flags
            window.appAuthenticated = @json(auth()->check());
            window.appDebug = @json(config('app.debug'));
            // Accent color override from localStorage (if set)
            try {
                const accentKey = localStorage.getItem('accentColor');
                if (accentKey && accentKey !== 'purple') {
                    const accentStyles = document.getElementById('accent-variables');
                    if (accentStyles) {
                        const root = document.documentElement;
                        const computed = getComputedStyle(root);
                        const currentHue = computed.getPropertyValue('--accent-hue').trim();
                        if (parseInt(currentHue) !== {{ $c['h'] ?? 252 }}) {
                            fetch('/api/color/' + encodeURIComponent(accentKey))
                                .then(r => r.json())
                                .then(data => {
                                    if (data.css) {
                                        const style = document.createElement('style');
                                        style.textContent = data.css;
                                        document.head.appendChild(style);
                                    }
                                })
                                .catch(() => {});
                        }
                    }
                }
            } catch (e) {}
        })();
    </script>

    {{-- Critical accent color styles --}}
    <style>
        /* Screen reader only utility */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Smooth accent color transitions */
        [data-accent] {
            transition: background-color 0.15s, color 0.15s, border-color 0.15s;
        }

        /* Accent color system */
        [data-accent="bg"] {
            background-color: var(--accent-bg) !important;
            color: var(--accent-text) !important;
            border-color: var(--accent-bg) !important;
        }

        [data-accent="border"] {
            border-color: var(--accent-border) !important;
            color: var(--accent-border) !important;
            background-color: transparent !important;
        }

        [data-accent="border"]:hover:not(:disabled) {
            background-color: var(--accent-bg) !important;
            color: var(--accent-text) !important;
        }

        [data-accent="text"] {
            color: var(--accent-border) !important;
        }

        [data-accent="bgLight"] {
            background-color: var(--accent-bgLight) !important;
            color: var(--accent-border) !important;
        }
    </style>


    {{-- Server-side accent color variables --}}
    @php
        $accentColor = auth()->check() && auth()->user()->accent_color
            ? auth()->user()->accent_color
            : request()->cookie('accentColor', 'purple');

        $colorMap = [
            'purple' => ['h' => 252, 's' => 83, 'l' => 65],
            'blue' => ['h' => 221, 's' => 83, 'l' => 65],
            'green' => ['h' => 142, 's' => 76, 'l' => 45],
            'red' => ['h' => 0, 's' => 84, 'l' => 53],
            'orange' => ['h' => 25, 's' => 95, 'l' => 47],
            'pink' => ['h' => 330, 's' => 81, 'l' => 60],
            'indigo' => ['h' => 238, 's' => 75, 'l' => 59],
            'teal' => ['h' => 173, 's' => 80, 'l' => 36],
            'amber' => ['h' => 38, 's' => 92, 'l' => 45],
            'slate' => ['h' => 215, 's' => 13, 'l' => 55],
        ];

        $accentData = $colorMap[$accentColor] ?? $colorMap['purple'];
        $darkLightness = min($accentData['l'] + 10, 85);
    @endphp

    @if($accentData)
        <style id="accent-variables">
            :root {
                --accent-hue: {{ $accentData['h'] }};
                --accent-saturation: {{ $accentData['s'] }}%;
                --accent-lightness: {{ $accentData['l'] }}%;
                --accent-bg: hsl({{ $accentData['h'] }}, {{ $accentData['s'] }}%, {{ $accentData['l'] }}%);
                --accent-border: hsl({{ $accentData['h'] }}, {{ $accentData['s'] }}%, {{ $accentData['l'] }}%);
                --accent-bgLight: hsl({{ $accentData['h'] }}, 83%, 96%);
                --accent-text: #ffffff;
                --accent-bg-dark: hsl({{ $accentData['h'] }}, {{ $accentData['s'] }}%, {{ $darkLightness }}%);
                --accent-border-dark: hsl({{ $accentData['h'] }}, {{ $accentData['s'] }}%, {{ $darkLightness }}%);
                --accent-bgLight-dark: hsl({{ $accentData['h'] }}, 83%, 15%);
                --accent-text-dark: #ffffff;
            }

            .dark {
                --accent-bg: var(--accent-bg-dark);
                --accent-border: var(--accent-border-dark);
                --accent-bgLight: var(--accent-bgLight-dark);
                --accent-text: var(--accent-text-dark);
            }
        </style>
    @endif

    {{-- Global app flags --}}
    <script>
        window.appAuthenticated = @json(auth()->check());
        window.appDebug = @json(config('app.debug'));
    </script>

    @livewireStyles
    @stack('scripts_header')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
    <body
        class="antialiased font-roboto bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen flex flex-col"
        @if (request()->cookie('theme') === 'dark') data-theme="dark" @endif>

        {{-- Skip to main content link --}}
        <a href="#main-content"
            class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:px-4 focus:py-2 focus:bg-white dark:focus:bg-gray-900 focus:text-gray-900 dark:focus:text-white focus:rounded-lg focus:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('ui.skip_to_content') }}
        </a>

        {{-- Header --}}
        <header
            class="sticky top-0 z-40 bg-white/95 dark:bg-gray-950/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 border-b border-gray-200 dark:border-gray-800"
            x-data="{ mobileMenuOpen: false }" @keydown.escape="mobileMenuOpen = false">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    {{-- Logo --}}
                    <div class="flex items-center min-w-0 flex-1">
                        <a href="{{ config('ui.brand.href', '/') }}" class="flex items-center gap-3 group"
                            aria-label="{{ config('ui.brand.name', config('app.name')) }}">
                            <div class="w-9 h-9 rounded flex items-center justify-center shrink-0" data-accent="bg">
                                <i class="fas fa-video text-white dark:text-gray-900 text-base"></i>
                            </div>

                            <div class="hidden sm:block min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-base font-bold text-gray-900 dark:text-white truncate">
                                        {{ config('ui.brand.name', config('app.name')) }}
                                    </span>
                                    @if (config('ui.brand.show_beta', false))
                                        <span
                                            class="px-1.5 py-0.5 text-xs font-bold uppercase bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded">
                                            {{ config('ui.brand.beta_label', 'Beta') }}
                                        </span>
                                    @endif
                                </div>
                                @if (config('ui.brand.tagline'))
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        {{ config('ui.brand.tagline') }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    </div>

                    {{-- Desktop Navigation --}}
                    <nav class="hidden lg:flex items-center gap-1" aria-label="Main navigation">
                        @foreach (config('ui.nav', []) as $item)
                            @php
                                $link = $item['href'] ?? '#';
                                $label = __($item['label'] ?? '');
                                $isActive = !empty($item['route']) && Route::currentRouteName() === $item['route'];
                            @endphp
                            <a href="{{ $link }}"
                                class="px-3 py-2 text-sm font-semibold rounded transition-colors whitespace-nowrap
                                  {{ $isActive ? 'text-white dark:text-gray-900' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                                @if ($isActive) data-accent="bg" @endif
                                aria-current="{{ $isActive ? 'page' : 'false' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </nav>

                    {{-- Action Bar --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        {{-- Desktop Switchers --}}
                        <div class="hidden md:flex items-center gap-1">
                            <x-lang-switcher />
                            <x-theme-switcher />
                        </div>

                        @auth
                            {{-- Notifications --}}
                            <div class="hidden sm:block">
                                @livewire('notifications')
                            </div>

                            {{-- User Menu (Desktop) --}}
                            <div class="hidden lg:block relative" x-data="{ open: false }" @keydown.escape="open = false">
                                <button @click="open = !open" type="button" :aria-expanded="open"
                                    aria-label="{{ __('ui.user_menu') }}"
                                    class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->display_name }}"
                                        class="w-8 h-8 rounded object-cover" loading="lazy">
                                    <span
                                        class="text-sm font-semibold text-gray-900 dark:text-white hidden xl:inline max-w-32 truncate">
                                        {{ Auth::user()->display_name }}
                                    </span>
                                    <i class="fas fa-chevron-down text-xs text-gray-500 dark:text-gray-400 transition-transform duration-200"
                                        :class="open ? 'rotate-180' : ''"></i>
                                </button>

                                {{-- Dropdown --}}
                                <div x-show="open" x-cloak @click.outside="open = false"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-lg z-50 overflow-hidden">

                                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ Auth::user()->avatar_url }}"
                                                alt="{{ Auth::user()->display_name }}"
                                                class="w-10 h-10 rounded object-cover" loading="lazy">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                                    {{ Auth::user()->display_name }}
                                                </p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">
                                                    {{ mask_email(Auth::user()->email) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="py-1">
                                        <a href="#profile"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                            <i class="fas fa-user w-4 text-gray-400"></i>
                                            {{ __('ui.profile') }}
                                        </a>
                                        <a href="{{ route('settings.edit') }}"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                            <i class="fas fa-cog w-4 text-gray-400"></i>
                                            {{ __('ui.settings') }}
                                        </a>
                                    </div>

                                    <div class="border-t border-gray-200 dark:border-gray-800 py-1">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950 transition-colors">
                                                <i class="fas fa-sign-out-alt w-4"></i>
                                                {{ __('ui.auth.sign_out') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Button (Desktop) --}}
                            <x-button href="{{ route('clips.submit') }}" variant="primary" size="sm"
                                class="hidden lg:inline-flex ml-2" accent="bg">
                                <i class="fas fa-plus text-xs"></i>
                                <span>{{ __('ui.submit') }}</span>
                            </x-button>
                        @else
                            {{-- Login Button (Desktop) --}}
                            <x-button href="{{ route('login') }}" variant="primary" size="sm"
                                class="hidden lg:inline-flex ml-2" accent="bg">
                                <i class="fas fa-sign-in-alt text-sm"></i>
                                <span>{{ __('ui.auth.sign_in') }}</span>
                            </x-button>
                        @endauth

                        {{-- Mobile Menu Toggle --}}
                        <button type="button" @click="mobileMenuOpen = !mobileMenuOpen"
                            :aria-expanded="mobileMenuOpen" aria-controls="mobile-menu"
                            :aria-label="mobileMenuOpen ? '{{ __('ui.close_menu') }}' : '{{ __('ui.open_menu') }}'"
                            class="lg:hidden w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-bars text-lg" x-show="!mobileMenuOpen"></i>
                            <i class="fas fa-times text-lg" x-show="mobileMenuOpen"></i>
                        </button>
                    </div>
                </div>

                {{-- Mobile Menu --}}
                <div x-show="mobileMenuOpen" x-cloak id="mobile-menu"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="lg:hidden pb-4 border-t border-gray-200 dark:border-gray-800">

                    {{-- Mobile Switchers --}}
                    <div
                        class="flex items-center justify-center gap-2 px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                        <x-lang-switcher />
                        <x-theme-switcher />
                    </div>

                    {{-- Mobile Navigation --}}
                    <nav class="px-4 pt-3 space-y-1" aria-label="Mobile navigation">
                        @foreach (config('ui.nav', []) as $item)
                            @php
                                $link = $item['href'] ?? '#';
                                $label = __($item['label'] ?? '');
                                $isActive = !empty($item['route']) && Route::currentRouteName() === $item['route'];
                            @endphp
                            <a href="{{ $link }}"
                                class="block px-4 py-2.5 text-sm font-semibold rounded transition-colors 
                                  {{ $isActive ? 'text-white dark:text-gray-900' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900' }}"
                                @if ($isActive) data-accent="bg" @endif
                                aria-current="{{ $isActive ? 'page' : 'false' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </nav>

                    @auth
                        {{-- Mobile User Section --}}
                        <div class="px-4 pt-3 mt-3 border-t border-gray-200 dark:border-gray-800 space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->display_name }}"
                                    class="w-10 h-10 rounded object-cover" loading="lazy">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                        {{ Auth::user()->display_name }}
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 truncate">
                                        {{ mask_email(Auth::user()->email) }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-1" role="menu" aria-label="{{ __('ui.user_menu') }}">
                                <a href="{{ route('clips.submit') }}"
                                    class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-bold rounded text-white dark:text-gray-900 transition-colors"
                                    data-accent="bg" role="menuitem">
                                    <i class="fas fa-plus text-xs"></i>
                                    {{ __('ui.submit') }}
                                </a>

                                <a href="#profile"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 rounded transition-colors"
                                    role="menuitem">
                                    <i class="fas fa-user w-4 text-gray-400"></i>
                                    {{ __('ui.profile') }}
                                </a>

                                <a href="{{ route('settings.edit') }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 rounded transition-colors"
                                    role="menuitem">
                                    <i class="fas fa-cog w-4 text-gray-400"></i>
                                    {{ __('ui.settings') }}
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950 rounded transition-colors"
                                        role="menuitem">
                                        <i class="fas fa-sign-out-alt w-4"></i>
                                        {{ __('ui.auth.sign_out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="px-4 pt-3 mt-3 border-t border-gray-200 dark:border-gray-800">
                            <x-button href="{{ route('login') }}" variant="primary" size="md" block
                                accent="bg">
                                <i class="fas fa-sign-in-alt text-sm"></i>
                                {{ __('ui.auth.sign_in') }}
                            </x-button>
                        </div>
                    @endauth
                </div>
            </div>
        </header>

        {{-- Main Content --}}
        <main id="main-content" class="flex-1">
            {{-- Page Header --}}
            @isset($header)
                <div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 sm:gap-6">
                            <div class="min-w-0 flex-1">
                                @isset($breadcrumbs)
                                    <nav class="text-sm text-gray-600 dark:text-gray-400 mb-2" aria-label="Breadcrumb">
                                        {{ $breadcrumbs }}
                                    </nav>
                                @endisset

                                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ $header }}
                                </h1>

                                @isset($subheader)
                                    <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-400">
                                        {{ $subheader }}
                                    </p>
                                @endisset
                            </div>

                            @isset($headerActions)
                                <div class="flex items-center gap-2 shrink-0 flex-wrap">
                                    {{ $headerActions }}
                                </div>
                            @endisset
                        </div>
                    </div>
                </div>
            @endisset

            {{-- Flash Messages --}}
            <x-flash-messages />

            {{-- Page Content --}}
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                {{ $slot }}
            </div>
        </main>

        {{-- Accent Bar --}}
        <div class="h-1" data-accent="bg" aria-hidden="true"></div>

        {{-- Footer --}}
        <footer class="bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex flex-col items-center md:items-start gap-3">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">
                            &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('ui.footer.rights') }}
                        </p>
                        <x-color-picker />
                    </div>

                    <nav class="flex flex-wrap items-center justify-center gap-4 text-xs font-semibold"
                        aria-label="Footer navigation">
                        @foreach (config('ui.footer.links', [['href' => 'https://github.com/ClipNook/ClipNook', 'label' => 'GitHub', 'icon' => 'fab fa-github'], ['href' => 'https://github.com/ClipNook/ClipNook/blob/main/LICENSE', 'label' => 'AGPL-3.0'], ['href' => '#terms', 'label' => 'Terms of Service'], ['href' => '#privacy', 'label' => 'Privacy Policy'], ['href' => '#imprint', 'label' => 'Imprint']]) as $link)
                            <a href="{{ $link['href'] }}"
                                @if (str_starts_with($link['href'], 'http')) target="_blank" rel="noopener noreferrer nofollow" @endif
                                class="inline-flex items-center gap-1.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                @if (isset($link['icon']))
                                    <i class="{{ $link['icon'] }} text-base"></i>
                                @endif
                                <span>{{ $link['label'] }}</span>
                            </a>
                            @if (!$loop->last)
                                <span class="text-gray-300 dark:text-gray-700" aria-hidden="true">·</span>
                            @endif
                        @endforeach
                    </nav>
                </div>
            </div>
        </footer>

        @livewireScriptConfig
        @vite(['resources/js/livewire.js'])
        @stack('scripts_footer')
    </body>

</html>
