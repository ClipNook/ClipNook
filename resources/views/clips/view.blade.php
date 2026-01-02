<x-layouts.app title="{{ $clip->title }}">
    <div class="min-h-screen bg-zinc-950">
        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-(--color-accent-500)/5 via-transparent to-(--color-accent-600)/3">
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-zinc-900/20 via-transparent to-transparent"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
                <!-- Breadcrumb -->
                <x-breadcrumb :items="[
                    ['url' => route('home'), 'label' => __('common.home')],
                    ['url' => route('clips.list'), 'label' => __('clips.browse')],
                    ['label' => $clip->title, 'truncate' => 40, 'current' => true]
                ]" />


                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Title & Stats -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-8 bg-(--color-accent-500) rounded-full"></div>
                                <h1 class="text-zinc-100 text-3xl lg:text-4xl font-bold leading-tight">{{ $clip->title }}</h1>
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-sm text-zinc-400">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-eye text-(--color-accent-400)"></i>
                                    <span>{{ number_format($clip->view_count) }} {{ __('clips.views') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-calendar text-(--color-accent-400)"></i>
                                    <span>{{ $clip->created_at_twitch->format('M j, Y') }}</span>
                                </div>
                                @if ($clip->game)
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-gamepad text-(--color-accent-400)"></i>
                                        <span>{{ $clip->game->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Video Player -->
                        <div class="relative group">
                            <div class="absolute -inset-1 bg-gradient-to-r from-(--color-accent-500) to-(--color-accent-600) rounded-2xl blur-xl opacity-20 group-hover:opacity-30 transition-all duration-500"></div>
                            <div class="relative bg-zinc-900 border border-zinc-800/50 rounded-2xl overflow-hidden shadow-2xl">
                                <div class="aspect-video bg-zinc-900">
                                    <livewire:twitch-player-consent :clip-info="['twitchClipId' => $clip->twitch_clip_id, 'localThumbnailPath' => $clip->local_thumbnail_path]" />
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if ($clip->description)
                            <div class="bg-zinc-900/40 backdrop-blur-sm border border-zinc-800/30 rounded-2xl p-6 lg:p-8">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-align-left text-(--color-accent-400)"></i>
                                    </div>
                                    <h2 class="text-xl font-bold text-zinc-100">{{ __('clips.description') }}</h2>
                                </div>
                                <div class="text-zinc-300 leading-relaxed">{{ $clip->description }}</div>
                            </div>
                        @endif

                        <!-- Tags & Actions -->
                        <div class="bg-zinc-900/40 backdrop-blur-sm border border-zinc-800/30 rounded-2xl p-6 lg:p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-info-circle text-(--color-accent-400)"></i>
                                    </div>
                                    <h2 class="text-xl font-bold text-zinc-100">{{ __('clips.clip_info') }}</h2>
                                </div>
                                <x-ui.button x-data="{ copied: false }" x-on:click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)" variant="secondary" size="sm" icon="share-nodes">
                                    <span x-text="copied ? '{{ __('clips.copied') }}' : '{{ __('clips.share') }}'"></span>
                                </x-ui.button>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                @if ($clip->game)
                                    <x-ui.button :href="route('games.view', $clip->game->id)" variant="outline" size="md" icon="gamepad">{{ $clip->game->name }}</x-ui.button>
                                @endif
                                @if ($clip->submitter)
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-800/50 border border-zinc-700/50 text-zinc-400 rounded-xl text-sm hover:border-(--color-accent-500)/30 transition-colors">
                                        <i class="fa-solid fa-user-pen text-(--color-accent-400)"></i>
                                        <span class="font-medium">{{ __('clips.submitted_by_label') }}:</span>
                                        <span>{{ $clip->submitter->twitch_login }}</span>
                                    </div>
                                @endif
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-800/50 border border-zinc-700/50 text-zinc-400 rounded-xl text-sm hover:border-(--color-accent-500)/30 transition-colors">
                                    <i class="fa-solid fa-scissors text-(--color-accent-400)"></i>
                                    <span class="font-medium">{{ __('clips.created_by_label') }}:</span>
                                    <span>{{ $clip->clip_creator_name }}</span>
                                </div>
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-800/50 border border-zinc-700/50 text-zinc-400 rounded-xl text-sm hover:border-(--color-accent-500)/30 transition-colors">
                                    <i class="fa-solid fa-plus text-(--color-accent-400)"></i>
                                    <span class="font-medium">{{ __('clips.added_on_label', ['date' => $clip->created_at->format('M j, Y')]) }}</span>
                                </div>
                                @foreach ($clip->tags as $tag)
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-800/50 border border-zinc-700/50 text-zinc-400 rounded-xl text-sm hover:border-(--color-accent-500)/30 transition-colors">
                                        <i class="fa-solid fa-tag text-(--color-accent-400)"></i>
                                        <span>{{ $tag }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Comments -->
                        <div class="bg-zinc-900/40 backdrop-blur-sm border border-zinc-800/30 rounded-2xl p-6 lg:p-8">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-comments text-(--color-accent-400)"></i>
                                </div>
                                <h2 class="text-xl font-bold text-zinc-100">{{ __('clips.comments') }}</h2>
                            </div>
                            <livewire:clips.clip-comments :clip="$clip" :key="'comments-' . $clip->id" />
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-8">
                        <!-- Broadcaster Card -->
                        <div class="bg-zinc-900/40 backdrop-blur-sm border border-zinc-800/30 rounded-2xl p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-user text-(--color-accent-400)"></i>
                                </div>
                                <h3 class="text-lg font-bold text-zinc-100">{{ __('clips.streamer') }}</h3>
                            </div>
                            <a href="{{ url('https://twitch.tv/' . $clip->broadcaster->twitch_login) }}" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-4 p-4 bg-zinc-800/30 rounded-xl hover:bg-zinc-800/50 transition-all" aria-label="Visit {{ $clip->broadcaster->twitch_display_name }} on Twitch">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-lg font-bold text-zinc-100 group-hover:text-(--color-accent-400) transition-colors truncate">{{ $clip->broadcaster->twitch_display_name }}</h4>
                                    <p class="text-sm text-zinc-400 truncate">
                                        <i class="fa-solid fa-at text-(--color-accent-400) mr-1"></i>
                                        {{ $clip->broadcaster->twitch_login }}
                                    </p>
                                </div>
                                <i class="fa-solid fa-chevron-right text-zinc-500 group-hover:text-(--color-accent-400) transition-colors"></i>
                            </a>
                        </div>

                        <!-- Clip Details -->
                        <div class="bg-zinc-900/40 backdrop-blur-sm border border-zinc-800/30 rounded-2xl p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-circle-info text-(--color-accent-400)"></i>
                                </div>
                                <h3 class="text-lg font-bold text-zinc-100">{{ __('clips.details') }}</h3>
                            </div>
                            <div class="space-y-4">
                                @php
                                    $details = [
                                        ['icon' => 'fa-scissors', 'label' => __('clips.created_by_label'), 'value' => $clip->clip_creator_name],
                                        ['icon' => 'fa-user-plus', 'label' => __('clips.submitted_by_label'), 'value' => $clip->submitter?->twitch_login, 'condition' => $clip->submitter],
                                        ['icon' => 'fa-calendar-plus', 'label' => __('clips.added_on'), 'value' => $clip->created_at->format('M j, Y')],
                                    ];
                                @endphp
                                @foreach ($details as $detail)
                                    @if (!isset($detail['condition']) || $detail['condition'])
                                        <div class="flex items-center justify-between p-3 bg-zinc-800/20 rounded-lg">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-(--color-accent-500)/10 rounded-lg flex items-center justify-center">
                                                    <i class="fa-solid {{ $detail['icon'] }} text-(--color-accent-400) text-sm"></i>
                                                </div>
                                                <span class="text-sm text-zinc-400">{{ $detail['label'] }}</span>
                                            </div>
                                            <span class="text-zinc-300 font-medium">{{ $detail['value'] }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Community Actions -->
                        @auth
                            <div class="bg-zinc-900/40 backdrop-blur-sm border border-zinc-800/30 rounded-2xl p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-(--color-accent-500)/10 border border-(--color-accent-500)/20 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-thumbs-up text-(--color-accent-400)"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-zinc-100">{{ __('clips.rate_this_clip') }}</h3>
                                    </div>
                                </div>
                                <livewire:clips.clip-rating :clip="$clip" :key="'rating-' . $clip->id" />
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Clips -->
        @if ($relatedClips->isNotEmpty())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                <div class="flex items-center justify-between mb-8">
                    <div class="space-y-2">
                        <h2 class="text-2xl lg:text-3xl font-bold text-zinc-100">{{ __('clips.related_clips') }}</h2>
                        <p class="text-zinc-400">{{ __('clips.related_clips_subtitle') }}</p>
                    </div>
                    <a href="{{ route('clips.list', ['broadcaster' => $clip->broadcaster->twitch_login]) }}" class="text-(--color-accent-400) hover:text-(--color-accent-300) transition-colors">
                        {{ __('common.view_all') }} <i class="fa-solid fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($relatedClips as $relatedClip)
                        <x-ui.clip-card :clip="$relatedClip" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
