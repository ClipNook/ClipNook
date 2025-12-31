<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-900">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#1f2937" media="(prefers-color-scheme: dark)">
        <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="{{ $noIndex ?? false ? 'noindex, nofollow' : 'index, follow' }}">
        <meta name="description" content="Submit and manage your favorite Twitch clips with ease.">

        <title>{{ isset($title) ? $title . ' · ' . config('app.name') : config('app.name') }}</title>

        @livewireStyles
        @stack('scripts_header')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans text-white min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-gray-800 border-b border-gray-700 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-blue-400">
                            <i class="fas fa-video mr-2"></i>
                            {{ config('app.name') }}
                        </h1>
                    </div>
                    <nav class="hidden md:flex space-x-8">
                        <a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition-colors">Home</a>
                        @auth
                            <a href="{{ route('clips.submit') }}" class="text-gray-300 hover:text-white transition-colors">Submit Clip</a>
                            <form method="POST" action="{{ route('auth.twitch.logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-300 hover:text-white transition-colors">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('auth.login') }}" class="text-gray-300 hover:text-white transition-colors">Login</a>
                        @endauth
                    </nav>
                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button class="text-gray-300 hover:text-white">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 border-t border-gray-700 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Brand Section -->
                    <div class="text-center md:text-left">
                        <h3 class="text-lg font-semibold text-blue-400 mb-2">
                            <i class="fas fa-video mr-2"></i>
                            {{ config('app.name') }}
                        </h3>
                        <p class="text-sm text-gray-400">
                            {{ __('footer.open_source') }}
                        </p>
                    </div>

                    <!-- Links Section -->
                    <div class="text-center">
                        <h4 class="text-sm font-medium text-gray-300 mb-3">Links</h4>
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors text-sm">Home</a>
                            @auth
                                <a href="{{ route('clips.submit') }}" class="text-gray-400 hover:text-white transition-colors text-sm">Submit Clip</a>
                            @else
                                <a href="{{ route('auth.login') }}" class="text-gray-400 hover:text-white transition-colors text-sm">Login</a>
                            @endauth
                            <a href="https://github.com/ClipNook/ClipNook" target="_blank" class="text-gray-400 hover:text-white transition-colors text-sm flex items-center justify-center">
                                <i class="fab fa-github mr-1"></i>
                                {{ __('footer.github') }}
                            </a>
                        </div>
                    </div>

                    <!-- Legal Section -->
                    <div class="text-center md:text-right">
                        <h4 class="text-sm font-medium text-gray-300 mb-3">Legal</h4>
                        <div class="flex flex-col space-y-2">
                            <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Privacy Policy</a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Terms of Service</a>
                        </div>
                    </div>
                </div>

                <!-- Bottom Section -->
                <div class="border-t border-gray-700 mt-6 pt-4">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-sm text-gray-400">
                            {{ __('footer.copyright', ['year' => date('Y'), 'app_name' => config('app.name')]) }}
                        </p>
                        <p class="text-sm text-gray-400 mt-2 md:mt-0">
                            {{ __('footer.powered_by', ['software' => 'ClipNook']) }}
                        </p>
                    </div>
                </div>
            </div>
        </footer>

        @livewireScriptConfig
        @vite(['resources/js/livewire.js'])
        @stack('scripts_footer')
    </body>
</html>
