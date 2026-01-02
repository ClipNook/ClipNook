<x-layouts.app title="{{ $game->name }} - {{ __('games.view_page_title') }}">
    <div class="min-h-screen bg-zinc-950">
        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <!-- Background with subtle gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-(--color-accent-500)/10 via-transparent to-(--color-accent-600)/5"></div>

            <!-- Hero Content -->
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
                <div class="flex flex-col lg:flex-row items-center gap-12">
                    <!-- Game Cover -->
                    <div class="flex-shrink-0">
                        @if($game->local_box_art_path)
                            <div class="relative group">
                                <div class="absolute -inset-2 bg-gradient-to-r from-(--color-accent-500) to-(--color-accent-600) rounded-xl blur-lg opacity-20 group-hover:opacity-30 transition-opacity"></div>
                                <div class="relative w-64 h-80 bg-zinc-800 border-2 border-(--color-accent-500)/30 rounded-xl overflow-hidden shadow-2xl">
                                    <img
                                        src="{{ Storage::url($game->local_box_art_path) }}"
                                        alt="{{ $game->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                </div>
                            </div>
                        @else
                            <div class="relative group">
                                <div class="absolute -inset-2 bg-gradient-to-r from-(--color-accent-500) to-(--color-accent-600) rounded-xl blur-lg opacity-20"></div>
                                <div class="relative w-64 h-80 bg-zinc-800 border-2 border-(--color-accent-500)/30 rounded-xl flex items-center justify-center shadow-2xl">
                                    <i class="fa-solid fa-gamepad text-(--color-accent-400) text-6xl"></i>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Game Info -->
                    <div class="flex-1 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-full text-sm font-medium text-(--color-accent-300) mb-6">
                            <i class="fa-solid fa-gamepad text-xs"></i>
                            {{ __('games.game') }}
                        </div>

                        <h1 class="text-4xl lg:text-6xl font-bold text-zinc-100 mb-4 leading-tight">
                            {{ $game->name }}
                        </h1>

                        <p class="text-xl text-zinc-400 mb-8 max-w-2xl">
                            {{ __('games.view_page_subtitle') }}
                        </p>

                        <!-- Stats Cards -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <div class="bg-zinc-900/80 backdrop-blur-sm border border-zinc-800/50 rounded-xl p-6 text-center min-w-[160px] group hover:border-(--color-accent-500)/30 transition-colors">
                                <div class="text-3xl font-bold text-(--color-accent-400) mb-2 group-hover:text-(--color-accent-300) transition-colors">
                                    {{ number_format($clipsCount) }}
                                </div>
                                <div class="text-sm text-zinc-500 uppercase tracking-wide font-medium">
                                    {{ Str::plural(__('games.clip'), $clipsCount) }}
                                </div>
                            </div>

                            <div class="bg-zinc-900/80 backdrop-blur-sm border border-zinc-800/50 rounded-xl p-6 text-center min-w-[160px] group hover:border-(--color-accent-500)/30 transition-colors">
                                <div class="text-3xl font-bold text-(--color-accent-400) mb-2 group-hover:text-(--color-accent-300) transition-colors">
                                    {{ number_format($streamersCount) }}
                                </div>
                                <div class="text-sm text-zinc-500 uppercase tracking-wide font-medium">
                                    {{ Str::plural('streamer', $streamersCount) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Decorative Elements -->
            <div class="absolute top-1/4 left-10 w-32 h-32 bg-(--color-accent-500)/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/4 right-10 w-40 h-40 bg-(--color-accent-600)/5 rounded-full blur-3xl"></div>
        </div>

        <!-- Content Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <!-- Clips Section -->
            <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800/50 rounded-2xl p-8 lg:p-12">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-video text-(--color-accent-400) text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-zinc-100">{{ __('games.clips_section') }}</h2>
                            <p class="text-zinc-400 text-sm">{{ __('games.clips_section_subtitle', ['count' => $clipsCount]) }}</p>
                        </div>
                    </div>

                    @if($clipsCount > 12)
                        <a href="{{ route('clips.list', ['game' => $game->id]) }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-(--color-accent-500) hover:bg-(--color-accent-600) text-zinc-100 font-medium rounded-xl transition-all duration-200 shadow-lg shadow-(--color-accent-500)/20 hover:shadow-xl hover:shadow-(--color-accent-500)/30">
                            <span>{{ __('games.view_all_clips') }}</span>
                            <i class="fa-solid fa-arrow-right text-sm"></i>
                        </a>
                    @endif
                </div>

                <!-- Subtle accent border -->
                <div class="accent-border-divider-medium"></div>

                @if($game->clips->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($game->clips as $clip)
                            <x-ui.clip-card :clip="$clip" />
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <div class="w-20 h-20 bg-zinc-800/50 border border-zinc-700/50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fa-solid fa-video text-zinc-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-zinc-300 mb-2">{{ __('games.no_clips_yet') }}</h3>
                        <p class="text-zinc-500 max-w-md mx-auto">{{ __('games.no_clips_found') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
