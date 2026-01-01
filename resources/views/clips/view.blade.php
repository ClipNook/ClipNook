<x-layouts.app title="{{ $clip->title }}">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-gray-950">
        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Clip Player -->
            <div class="bg-gray-900 rounded-md border border-gray-800 overflow-hidden">
                <div class="aspect-video bg-gray-800">
                    <livewire:twitch-player-consent :clip-info="['twitchClipId' => $clip->twitch_clip_id]" />
                </div>
            </div>

            <!-- Clip Info -->
            <div class="bg-gray-900 rounded-md border border-gray-800 p-6">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-4">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-white mb-2">{{ $clip->title }}</h1>
                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-400">
                            <a href="#" class="hover:text-purple-400 transition-colors">
                                <i class="fas fa-user mr-1"></i>
                                {{ $clip->broadcaster->twitch_display_name }}
                            </a>
                            <span>•</span>
                            <span>
                                <i class="fas fa-eye mr-1"></i>
                                {{ __('clips.views_count', ['count' => number_format($clip->view_count)]) }}
                            </span>
                            <span>•</span>
                            <span>
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $clip->created_at_twitch->format('M j, Y') }}
                            </span>
                            <span>•</span>
                            <div class="flex items-center gap-1">
                                <i class="fas fa-clock mr-1"></i>
                                <span>{{ __('clips.duration_seconds', ['seconds' => number_format($clip->duration, 2)]) }}</span>
                            </div>
                            <span>•</span>
                            <span>
                                <i class="fas fa-plus mr-1"></i>
                                <span>{{ __('clips.added_on_label', ['date' => $clip->created_at->format('M j, Y')]) }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <button class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md transition-colors">
                            <i class="fas fa-share-alt mr-2"></i>
                            {{ __('clips.share') }}
                        </button>
                    </div>
                </div>

                <!-- Description -->
                @if ($clip->description)
                    <div class="border-t border-gray-800 pt-4">
                        <p class="text-gray-300 leading-relaxed">
                            {{ $clip->description }}
                        </p>
                    </div>
                @endif

                <!-- Tags/Meta -->
                <div class="border-t border-gray-800 mt-4 pt-4 flex flex-wrap gap-2">
                    @if ($clip->game)
                        <a href="{{ route('games.view', $clip->game->id) }}" class="px-3 py-1 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-md text-sm transition-colors">
                            <i class="fas fa-gamepad mr-1"></i>
                            {{ $clip->game->name }}
                        </a>
                    @endif
                    @if ($clip->submitter)
                        <span class="px-3 py-1 bg-gray-800 text-gray-400 rounded-md text-sm">
                            <i class="fas fa-user-edit mr-1"></i>
                            {{ __('clips.submitted_by_label') }}: {{ $clip->submitter->twitch_login }}
                        </span>
                    @endif
                    <span class="px-3 py-1 bg-gray-800 text-gray-400 rounded-md text-sm">
                        <i class="fas fa-scissors mr-1"></i>
                        {{ __('clips.created_by_label') }}: {{ $clip->clip_creator_name }}
                    </span>
                </div>
            </div>

            <!-- Rating & Actions -->
            @auth
                <livewire:clips.clip-rating :clip="$clip" :key="'rating-'.$clip->id" />
            @endauth

            <!-- Comments Section -->
            <livewire:clips.clip-comments :clip="$clip" :key="'comments-'.$clip->id" />

            <!-- Related Clips -->
            @if ($relatedClips->isNotEmpty())
                <div class="bg-gray-900 rounded-md border border-gray-800 p-6">
                    <h2 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-film mr-2"></i>
                        {{ __('clips.related_clips') }}
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($relatedClips as $relatedClip)
                            <a href="{{ route('clips.view', $relatedClip) }}" class="group block bg-gray-800 rounded-md overflow-hidden hover:bg-gray-750 transition-colors">
                                <div class="aspect-video relative overflow-hidden bg-gray-900">
                                    <img src="{{ $relatedClip->thumbnail_url }}" alt="{{ $relatedClip->title }}" class="w-full h-full object-cover">
                                </div>
                                <div class="p-3">
                                    <h3 class="font-medium text-white text-sm line-clamp-2 group-hover:text-purple-400 transition-colors">
                                        {{ $relatedClip->title }}
                                    </h3>
                                    <p class="text-xs text-gray-400 mt-1">{{ $relatedClip->broadcaster->twitch_display_name }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
