<div class="space-y-6">
    <h2 class="text-2xl font-bold mb-4">{{ __('clip.overview.title') }}</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse ($clips as $clip)
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="aspect-video w-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                    @if ($clip->thumbnail_path)
                        <img src="{{ asset($clip->thumbnail_path) }}" alt="{{ $clip->title }}" class="object-cover w-full h-full">
                    @else
                        <i class="fas fa-image text-3xl text-gray-400"></i>
                    @endif
                </div>
                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-1 truncate">{{ $clip->title }}</h3>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2 truncate">
                        @if ($clip->category)
                            <span class="inline-flex items-center gap-1"><i class="fas fa-gamepad"></i> {{ $clip->category->name }}</span>
                        @endif
                        @if ($clip->duration)
                            <span class="ml-2 inline-flex items-center gap-1"><i class="fas fa-clock"></i> {{ $clip->duration }}s</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-2 truncate">
                        <span>{{ __('clip.overview.broadcaster') }}: {{ $clip->broadcaster->display_name ?? '-' }}</span>
                    </div>
                    <div class="mt-auto flex items-center gap-2">
                        <a href="#{{ $clip->twitch_clip_id }}" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">{{ __('clip.overview.details') }}</a>
                        <span class="ml-auto text-xs text-gray-400">{{ $clip->clip_created_at ? $clip->clip_created_at->isoFormat('LL') : '' }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500 dark:text-gray-400 py-8">
                {{ __('clip.overview.empty') }}
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $clips->links() }}
    </div>
</div>
