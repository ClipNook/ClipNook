<div>
    @if (session()->has('message'))
        <div class="bg-green-900/50 border border-green-800 rounded-lg p-4 mb-4 animate-fade-in">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-check-circle text-green-400 mt-0.5 flex-shrink-0"></i>
                <span class="text-green-200 text-sm sm:text-base">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @auth
        <div class="mb-6">
            @if ($replyToId)
                <div class="mb-2 p-3 bg-zinc-800 rounded-md text-sm text-zinc-300 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-reply text-(--color-accent-400)"></i>
                        {{ __('clips.reply') }}
                    </span>
                    <button
                        wire:click="cancelReply"
                        class="text-zinc-400 hover:text-white p-1 rounded-md hover:bg-zinc-700 transition-colors"
                        aria-label="{{ __('clips.cancel_reply') }}"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
            <form wire:submit="postComment" class="space-y-3">
                <textarea
                    wire:model="newComment"
                    rows="3"
                    placeholder="{{ __('clips.add_comment') }}"
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:border-(--color-accent-500) focus:outline-none focus:ring-2 focus:ring-(--color-accent-500)/20 transition-all resize-none text-base"
                    wire:loading.attr="disabled"
                ></textarea>
                @error('newComment')
                    <span class="text-red-400 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-exclamation-triangle"></i>
                        {{ $message }}
                    </span>
                @enderror
                <div class="flex justify-end">
                    <x-ui.button
                        type="submit"
                        variant="primary"
                        size="sm"
                        wire:loading.attr="disabled"
                        class="min-w-[100px]"
                    >
                        <span wire:loading.remove wire:target="postComment">
                            {{ __('clips.post_comment') }}
                        </span>
                        <span wire:loading wire:target="postComment" class="flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                            {{ __('common.loading') }}
                        </span>
                    </x-ui.button>
                </div>
            </form>
        </div>
    @else
        <div class="text-center py-6">
            <div class="flex flex-col items-center gap-3">
                <i class="fa-solid fa-lock text-zinc-600 text-2xl"></i>
                <p class="text-zinc-400 text-sm sm:text-base">
                    <a href="{{ route('auth.login') }}" class="text-(--color-accent-400) hover:text-(--color-accent-300) font-medium transition-colors">
                        {{ __('auth.login') }}
                    </a>
                    {{ __('clips.login_to_comment') }}
                </p>
            </div>
        </div>
    @endauth

    <div class="space-y-4">
        @forelse ($comments as $comment)
            <div class="border-t border-zinc-800 pt-4 first:border-t-0 first:pt-0">
                <div class="flex gap-3">
                    <img
                        src="{{ $comment->user->avatar_url }}"
                        alt="{{ $comment->user->twitch_display_name }}"
                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex-shrink-0"
                    >
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <span class="font-medium text-zinc-100 text-sm sm:text-base">{{ $comment->user->twitch_display_name }}</span>
                            <span class="text-xs text-zinc-500">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        @if ($comment->is_deleted)
                            <p class="text-zinc-500 italic text-sm">{{ __('clips.comment_deleted') }}</p>
                        @else
                            <p class="text-zinc-300 text-sm sm:text-base leading-relaxed">{{ $comment->content }}</p>
                            <div class="mt-3 flex gap-2 sm:gap-3 text-sm">
                                @auth
                                    <button
                                        wire:click="setReplyTo({{ $comment->id }})"
                                        class="flex items-center gap-1.5 px-3 py-1.5 text-zinc-400 hover:text-(--color-accent-400) hover:bg-(--color-accent-400)/10 rounded-md transition-all text-xs sm:text-sm"
                                        aria-label="{{ __('clips.reply_to_comment') }}"
                                    >
                                        <i class="fa-solid fa-reply"></i>
                                        <span class="hidden sm:inline">{{ __('clips.reply') }}</span>
                                    </button>
                                    @if ($comment->user_id === auth()->id())
                                        <button
                                            wire:click="deleteComment({{ $comment->id }})"
                                            class="flex items-center gap-1.5 px-3 py-1.5 text-zinc-400 hover:text-red-400 hover:bg-red-400/10 rounded-md transition-all text-xs sm:text-sm"
                                            wire:confirm="{{ __('clips.confirm_delete_comment') }}"
                                            aria-label="{{ __('clips.delete_comment') }}"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                            <span class="hidden sm:inline">{{ __('clips.delete_comment') }}</span>
                                        </button>
                                    @endif
                                @endauth
                            </div>
                        @endif

                        @if ($comment->replies->isNotEmpty())
                            <div class="mt-4 space-y-3 pl-4 border-l-2 border-zinc-800/50">
                                @foreach ($comment->replies as $reply)
                                    <div class="flex gap-3">
                                        <img
                                            src="{{ $reply->user->avatar_url }}"
                                            alt="{{ $reply->user->twitch_display_name }}"
                                            class="w-8 h-8 rounded-lg flex-shrink-0"
                                        >
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                <span class="font-medium text-zinc-100 text-xs sm:text-sm">{{ $reply->user->twitch_display_name }}</span>
                                                <span class="text-xs text-zinc-500">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if ($reply->is_deleted)
                                                <p class="text-zinc-500 italic text-xs sm:text-sm">{{ __('clips.comment_deleted') }}</p>
                                            @else
                                                <p class="text-zinc-300 text-xs sm:text-sm leading-relaxed">{{ $reply->content }}</p>
                                                @auth
                                                    @if ($reply->user_id === auth()->id())
                                                        <button
                                                            wire:click="deleteComment({{ $reply->id }})"
                                                            class="mt-2 flex items-center gap-1.5 px-2 py-1 text-zinc-400 hover:text-red-400 hover:bg-red-400/10 rounded-md transition-all text-xs"
                                                            wire:confirm="{{ __('clips.confirm_delete_comment') }}"
                                                            aria-label="{{ __('clips.delete_comment') }}"
                                                        >
                                                            <i class="fa-solid fa-trash"></i>
                                                            <span class="hidden sm:inline">{{ __('clips.delete_comment') }}</span>
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
            <div class="text-center py-8">
                <div class="flex flex-col items-center gap-3">
                    <i class="fa-solid fa-comments text-zinc-600 text-3xl"></i>
                    <p class="text-zinc-500 text-sm sm:text-base">{{ __('clips.no_comments') }}</p>
                    <p class="text-zinc-600 text-xs">{{ __('clips.be_first_to_comment') }}</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
