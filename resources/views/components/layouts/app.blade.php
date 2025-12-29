<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="{{ $noIndex ?? false ? 'noindex, nofollow' : 'index, follow' }}">

    <title>{{ isset($title) ? $title . ' · ' . config('app.name') : config('app.name') }}</title>

    {{-- Theme initialization --}}
    <script>
        (function() {
            try {
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                var savedTheme = localStorage.getItem('theme');
                var isDark = savedTheme === 'dark' || (savedTheme === null && prefersDark);
                
                if (isDark) {
                    document.documentElement.classList.add('dark');
                }
                
                window.setTheme = function(mode) {
                    if (mode === 'dark') {
                        localStorage.setItem('theme', 'dark');
                        document.documentElement.classList.add('dark');
                    } else if (mode === 'light') {
                        localStorage.setItem('theme', 'light');
                        document.documentElement.classList.remove('dark');
                    } else {
                        localStorage.removeItem('theme');
                        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    }
                };
            } catch(e) {}
        })();
    </script>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts_head')
</head>

<body class="antialiased font-sans bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen flex flex-col">
    
    {{-- Skip to content --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2.5 focus:text-sm focus:font-semibold focus:rounded-md text-white dark:text-gray-900" data-accent="bg" aria-label="{{ __('ui.skip') }}">
        {{ __('ui.skip') }}
    </a>

    {{-- Header --}}
    <header class="sticky top-0 z-40 bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ mobileMenuOpen: false }">
            <div class="flex items-center justify-between h-16">
                
                {{-- Logo --}}
                @php
                    $ui = config('ui') ?? [];
                    $brand = $ui['brand'] ?? [];
                @endphp
                <div class="flex items-center min-w-0">
                    <a href="{{ $brand['href'] ?? '/' }}" class="flex items-center gap-3 group" aria-label="{{ $brand['name'] ?? config('app.name') }}">
                        <div class="w-9 h-9 rounded flex items-center justify-center shrink-0" data-accent="bg">
                            <i class="fas fa-video text-white dark:text-gray-900 text-base"></i>
                        </div>
                        
                        <div class="hidden sm:block min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-base font-bold text-gray-900 dark:text-white">{{ $brand['name'] ?? config('app.name') }}</span>
                                @if(!empty($brand['show_beta']))
                                    <span class="px-1.5 py-0.5 text-xs font-bold uppercase bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded">
                                        {{ $brand['beta_label'] ?? 'Beta' }}
                                    </span>
                                @endif
                            </div>
                            @if(!empty($brand['tagline']))
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $brand['tagline'] }}</p>
                            @endif
                        </div>
                    </a>
                </div>

                {{-- Desktop Navigation --}}
                <nav class="hidden lg:flex items-center gap-1" role="navigation" aria-label="Main navigation">
                    @foreach(config('ui.nav', []) as $item)
                        @php
                            $link = ui_resolve_link($item);
                            $label = __($item['label'] ?? '');
                            $isActive = !empty($item['route']) && Route::currentRouteName() === $item['route'];
                        @endphp
                        <a href="{{ $link }}" 
                           class="px-3 py-2 text-sm font-semibold rounded transition-colors {{ $isActive ? 'text-white dark:text-gray-900' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                           @if($isActive) data-accent="bg" @endif
                           aria-current="{{ $isActive ? 'page' : 'false' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    
                    {{-- Switchers --}}
                    <div class="hidden md:flex items-center gap-1">
                        <x-lang-switcher />
                        <x-theme-switcher />
                    </div>

                    @auth
                        {{-- Notifications --}}
                        <div class="hidden sm:block">
                            @livewire('notifications')
                        </div>

                        {{-- Desktop User Menu --}}
                        <div class="hidden lg:block relative" x-data="{ open: false }">
                            @php
                                $profileUrl = Route::has('profile') ? route('profile') : '#profile';
                                $settingsUrl = Route::has('settings') ? route('settings') : '#settings';
                            @endphp

                            <button 
                                @click="open = !open; if (open) $nextTick(() => $refs.firstMenuItem?.focus())"
                                @keydown.enter.prevent="open = !open; if (open) $nextTick(() => $refs.firstMenuItem?.focus())"
                                @keydown.space.prevent="open = !open; if (open) $nextTick(() => $refs.firstMenuItem?.focus())"
                                type="button"
                                x-bind:aria-expanded="open ? 'true' : 'false'"
                                aria-haspopup="true"
                                aria-label="{{ __('ui.user_menu') }}"
                                class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors focus:outline-none focus-visible:border focus-visible:border-indigo-500">
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->display_name }}" class="w-8 h-8 rounded object-cover">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white hidden xl:block max-w-32 truncate">{{ Auth::user()->display_name }}</span>
                                <i class="fas fa-chevron-down text-xs text-gray-500 dark:text-gray-400"></i>
                            </button>

                            <div 
                                x-show="open"
                                x-cloak
                                @click.outside="open = false"
                                @keydown.escape.window="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1"
                                class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow overflow-hidden"
                                style="display: none;"
                                role="menu" aria-label="User menu">
                                
                                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->display_name }}" class="w-10 h-10 rounded object-cover">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->display_name }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ mask_email(Auth::user()->email) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="py-1">
                                    <a href="{{ $profileUrl }}" x-ref="firstMenuItem" tabindex="-1" role="menuitem" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                        <i class="fas fa-user w-4 text-gray-400"></i>
                                        {{ __('ui.profile') }}
                                    </a>
                                    <a href="{{ $settingsUrl }}" tabindex="-1" role="menuitem" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                        <i class="fas fa-cog w-4 text-gray-400"></i>
                                        {{ __('ui.settings') }}
                                    </a>
                                </div>

                                <div class="border-t border-gray-200 dark:border-gray-800 py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" role="menuitem" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950 transition-colors">
                                            <i class="fas fa-sign-out-alt w-4"></i>
                                            {{ __('ui.auth.sign_out') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <x-button href="{{ route('clips.submit') }}" variant="primary" size="sm" class="hidden lg:inline-flex ml-2" accent="bg">
                            <i class="fas fa-plus text-xs"></i>
                            <span>{{ __('ui.submit') }}</span>
                        </x-button>

                    @else
                        {{-- Login Button --}}
                        <x-button href="{{ route('login') }}" variant="primary" size="sm" class="hidden lg:inline-flex ml-2" accent="bg">
                            <i class="fas fa-sign-in-alt text-lg"></i>
                            {{ __('ui.auth.sign_in') }}
                        </x-button>
                    @endauth

                    {{-- Mobile Menu Button --}}
                    <x-button variant="icon" type="button" @click="mobileMenuOpen = !mobileMenuOpen" x-bind:aria-expanded="mobileMenuOpen" aria-controls="mobile-menu" x-bind:aria-label="mobileMenuOpen ? '{{ __('ui.close_menu') }}' : '{{ __('ui.open_menu') }}'" class="lg:hidden">
                        <i x-show="!mobileMenuOpen" class="fas fa-bars text-lg"></i>
                        <i x-show="mobileMenuOpen" class="fas fa-times text-lg" style="display: none;"></i>
                    </x-button>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div 
                x-show="mobileMenuOpen"
                x-cloak
                id="mobile-menu"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="lg:hidden pb-4 border-t border-gray-200 dark:border-gray-800"
                style="display: none;">
                
                {{-- Mobile Switchers --}}
                <div class="flex items-center justify-center gap-2 px-4 py-3 border-b border-gray-200 dark:border-gray-800 md:hidden">
                    <x-lang-switcher />
                    <x-theme-switcher />
                </div>

                {{-- Mobile Navigation --}}
                <nav class="px-4 pt-3 space-y-1" role="navigation" aria-label="Mobile navigation">
                    @foreach(config('ui.nav', []) as $item)
                        @php
                            $link = ui_resolve_link($item);
                            $label = __($item['label'] ?? '');
                            $isActive = !empty($item['route']) && Route::currentRouteName() === $item['route'];
                        @endphp
                        <a href="{{ $link }}" 
                           class="block px-4 py-2.5 text-sm font-semibold rounded transition-colors {{ $isActive ? 'text-white dark:text-gray-900' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900' }}"
                           @if($isActive) data-accent="bg" @endif
                           aria-current="{{ $isActive ? 'page' : 'false' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>

                @auth
                    {{-- Mobile User Section --}}
                    <div class="px-4 pt-3 mt-3 border-t border-gray-200 dark:border-gray-800 space-y-3">
                        @php
                            $profileUrl = Route::has('profile') ? route('profile') : '#profile';
                            $settingsUrl = Route::has('settings') ? route('settings') : '#settings';
                        @endphp

                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900 rounded">
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->display_name }}" class="w-10 h-10 rounded object-cover">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->display_name }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ mask_email(Auth::user()->email) }}</p>
                            </div>
                        </div>

                        <div class="space-y-1" role="menu" aria-label="{{ __('ui.user_menu') }}">
                            <a href="{{ route('clips.submit') }}" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-bold rounded text-white dark:text-gray-900 transition-colors" data-accent="bg" role="menuitem">
                                <i class="fas fa-plus text-xs"></i>
                                {{ __('ui.submit') }}
                            </a>
                            
                            <a href="{{ $profileUrl }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 rounded transition-colors" role="menuitem">
                                <i class="fas fa-user w-4 text-gray-400"></i>
                                {{ __('ui.profile') }}
                            </a>
                            
                            <a href="{{ $settingsUrl }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 rounded transition-colors" role="menuitem">
                                <i class="fas fa-cog w-4 text-gray-400"></i>
                                {{ __('ui.settings') }}
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" aria-label="{{ __('ui.auth.sign_out') }}" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950 rounded transition-colors" role="menuitem">
                                    <i class="fas fa-sign-out-alt w-4"></i>
                                    {{ __('ui.auth.sign_out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="px-4 pt-3 mt-3 border-t border-gray-200 dark:border-gray-800">
                        <x-button href="{{ route('login') }}" variant="primary" size="md" block accent="bg">
                            <i class="fas fa-sign-in-alt text-lg"></i>
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
        @if(isset($header) || isset($breadcrumbs))
            <div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 sm:gap-6">
                        <div class="min-w-0 flex-1">
                            @isset($breadcrumbs)
                                <nav class="text-sm text-gray-600 dark:text-gray-400 mb-2" aria-label="Breadcrumb">
                                    {{ $breadcrumbs }}
                                </nav>
                            @endisset

                            @isset($header)
                                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $header }}</h1>
                            @endisset

                            @isset($subheader)
                                <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-400">{{ $subheader }}</p>
                            @endisset
                        </div>

                        @isset($headerActions)
                            <div class="flex items-center gap-2 shrink-0">
                                {{ $headerActions }}
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        @endif

        {{-- Flash Messages --}}
        @if(session()->hasAny(['success', 'error', 'warning', 'info']))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6" role="region" aria-live="polite">
                <div class="space-y-3">
                    @if(session('success'))
                        <div class="flex items-start gap-3 p-4 bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800 rounded-lg" role="status">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-lg mt-0.5 shrink-0"></i>
                            <p class="text-sm font-semibold text-green-900 dark:text-green-100">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="flex items-start gap-3 p-4 bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 rounded-lg" role="alert">
                            <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 text-lg mt-0.5 shrink-0"></i>
                            <p class="text-sm font-semibold text-red-900 dark:text-red-100">{{ session('error') }}</p>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="flex items-start gap-3 p-4 bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 rounded-lg" role="status">
                            <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400 text-lg mt-0.5 shrink-0"></i>
                            <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">{{ session('warning') }}</p>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 rounded-lg" role="status">
                            <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-lg mt-0.5 shrink-0"></i>
                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">{{ session('info') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Page Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            {{ $slot }}
            </div>
    </main>

    {{-- Accent Bar --}}
    <div class="h-1" data-accent="bg"></div>

    {{-- Footer --}}
    <footer class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                
                <div class="flex flex-col items-center md:items-start gap-3">
                    <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('ui.footer.rights') }}
                    </p>
                    <x-color-picker />
                </div>

                <nav class="flex flex-wrap items-center justify-center gap-4 text-xs font-semibold" aria-label="Footer navigation">
                    <a href="https://github.com/ClipNook/ClipNook" target="_blank" rel="noopener noreferrer" 
                       class="inline-flex items-center gap-1.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <i class="fab fa-github text-base"></i>
                        <span>GitHub</span>
                    </a>
                    
                    <span class="text-gray-300 dark:text-gray-700">·</span>
                    
                    <a href="https://github.com/ClipNook/ClipNook/blob/main/LICENSE" target="_blank" rel="noopener noreferrer" 
                       class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        AGPL-3.0
                    </a>
                    
                    <span class="text-gray-300 dark:text-gray-700">·</span>
                    
                    <a href="#terms" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        {{ __('ui.footer.terms') }}
                    </a>
                    
                    <a href="#privacy" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        {{ __('ui.footer.privacy') }}
                    </a>
                    
                    <a href="#imprint" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        {{ __('ui.footer.imprint') }}
                    </a>
                </nav>
            </div>
        </div>
    </footer>

    @livewireScriptConfig
    @vite(['resources/js/livewire.js'])
    @stack('scripts_footer')
</body>
</html>