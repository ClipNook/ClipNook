<div>
    @if (session()->has('message'))
        <div class="bg-green-900/50 border border-green-800 rounded-lg p-4 mb-4">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-check-circle text-green-400 mt-0.5"></i>
                <span class="text-green-200">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @auth
        <div class="mb-6">
            @if ($replyToId)
                <div class="mb-2 p-2 bg-zinc-800 rounded-md text-sm text-zinc-300 flex items-center justify-between">
                    <span>
                        <i class="fa-solid fa-reply mr-2"></i>
                        {{ __('clips.reply') }}
                    </span>
                    <button wire:click="cancelReply" class="text-zinc-400 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
            <textarea
                wire:model="newComment"
                rows="3"
                placeholder="{{ __('clips.add_comment') }}"
                class="w-full px-4 py-2.5 bg-zinc-800 border border-zinc-700 rounded-md text-white placeholder-zinc-500 focus:border-(--color-accent-500) focus:outline-none transition-colors resize-none"
            ></textarea>
            @error('newComment') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
            <div class="mt-2 flex justify-end">
                <x-ui.button
                    wire:click="postComment"
                    variant="primary"
                    size="sm"
                >
                    {{ __('clips.post_comment') }}
                </x-ui.button>
            </div>
        </div>
    @else
        <p class="text-zinc-400 text-center py-4">
            <a href="{{ route('auth.login') }}" class="text-(--color-accent-400) hover:text-(--color-accent-300)">{{ __('auth.login') }}</a> {{ __('clips.login_to_comment') }}
        </p>
    @endauth

    <div class="space-y-4">
        @forelse ($comments as $comment)
            <div class="border-t border-zinc-800 pt-4">
                <div class="flex gap-3">
                    <img
                        src="{{ $comment->user->avatar_url }}"
                        alt="{{ $comment->user->twitch_display_name }}"
                        class="w-10 h-10 rounded-md"
                    >
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-medium text-zinc-100">{{ $comment->user->twitch_display_name }}</span>
                            <span class="text-sm text-zinc-500">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        @if ($comment->is_deleted)
                            <p class="text-zinc-500 italic">{{ __('clips.comment_deleted') }}</p>
                        @else
                            <p class="text-zinc-300">{{ $comment->content }}</p>
                            <div class="mt-2 flex gap-3 text-sm">
                                @auth
                                    <button wire:click="setReplyTo({{ $comment->id }})" class="text-zinc-400 hover:text-(--color-accent-400) transition-colors">
                                        <i class="fa-solid fa-reply mr-1"></i>
                                        {{ __('clips.reply') }}
                                    </button>
                                    @if ($comment->user_id === auth()->id())
                                        <button wire:click="deleteComment({{ $comment->id }})" class="text-zinc-400 hover:text-red-400 transition-colors">
                                            <i class="fa-solid fa-trash mr-1"></i>
                                            {{ __('clips.delete_comment') }}
                                        </button>
                                    @endif
                                @endauth
                            </div>
                        @endif

                        @if ($comment->replies->isNotEmpty())
                            <div class="mt-4 space-y-3 pl-4 border-l-2 border-zinc-800">
                                @foreach ($comment->replies as $reply)
                                    <div class="flex gap-3">
                                        <img
                                            src="{{ $reply->user->avatar_url }}"
                                            alt="{{ $reply->user->twitch_display_name }}"
                                            class="w-8 h-8 rounded-md"
                                        >
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="font-medium text-zinc-100 text-sm">{{ $reply->user->twitch_display_name }}</span>
                                                <span class="text-xs text-zinc-500">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if ($reply->is_deleted)
                                                <p class="text-zinc-500 italic text-sm">{{ __('clips.comment_deleted') }}</p>
                                            @else
                                                <p class="text-zinc-300 text-sm">{{ $reply->content }}</p>
                                                @auth
                                                    @if ($reply->user_id === auth()->id())
                                                        <button wire:click="deleteComment({{ $reply->id }})" class="mt-1 text-xs text-zinc-400 hover:text-red-400 transition-colors">
                                                            <i class="fa-solid fa-trash mr-1"></i>
                                                            {{ __('clips.delete_comment') }}
                                                        </button>
                                                    @endif
                                                @endauth
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-zinc-500 text-center py-8">{{ __('clips.no_comments') }}</p>
        @endforelse
    </div>
</div>
