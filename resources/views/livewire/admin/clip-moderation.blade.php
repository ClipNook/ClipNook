<div class="min-h-screen bg-zinc-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-zinc-100 mb-2">{{ __('admin.clip_moderation') }}</h1>
            <p class="text-zinc-400">{{ __('admin.clip_moderation_description') }}</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-zinc-800 rounded-md p-6 border border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-zinc-400 text-sm">{{ __('admin.pending_clips') }}</p>
                        <p class="text-3xl font-bold text-yellow-400">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="bg-yellow-500/10 rounded-full p-3">
                        <i class="fa-solid fa-clock text-yellow-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-800 rounded-md p-6 border border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-zinc-400 text-sm">{{ __('admin.approved_clips') }}</p>
                        <p class="text-3xl font-bold text-green-400">{{ $stats['approved'] }}</p>
                    </div>
                    <div class="bg-green-500/10 rounded-full p-3">
                        <i class="fa-solid fa-check text-green-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-800 rounded-md p-6 border border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-zinc-400 text-sm">{{ __('admin.rejected_clips') }}</p>
                        <p class="text-3xl font-bold text-red-400">{{ $stats['rejected'] }}</p>
                    </div>
                    <div class="bg-red-500/10 rounded-full p-3">
                        <i class="fa-solid fa-xmark text-red-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-800 rounded-md p-6 border border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-zinc-400 text-sm">{{ __('admin.flagged_clips') }}</p>
                        <p class="text-3xl font-bold text-orange-400">{{ $stats['flagged'] }}</p>
                    </div>
                    <div class="bg-orange-500/10 rounded-full p-3">
                        <i class="fa-solid fa-flag text-orange-400 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-zinc-800 rounded-md p-6 border border-zinc-700 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Status Filter -->
                <div class="flex-1">
                    <label for="statusFilter" class="block text-sm font-medium text-zinc-300 mb-2">
                        {{ __('admin.filter_by_status') }}
                    </label>
                    <select wire:model.live="statusFilter" id="statusFilter"
                            class="w-full bg-zinc-900 border border-zinc-700 rounded-md px-4 py-2.5 text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                        <option value="all">{{ __('admin.all_clips') }}</option>
                        <option value="pending">{{ __('admin.pending') }}</option>
                        <option value="approved">{{ __('admin.approved') }}</option>
                        <option value="rejected">{{ __('admin.rejected') }}</option>
                        <option value="flagged">{{ __('admin.flagged') }}</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="flex-1">
                    <label for="searchQuery" class="block text-sm font-medium text-zinc-300 mb-2">
                        {{ __('admin.search') }}
                    </label>
                    <input wire:model.live.debounce.300ms="searchQuery" type="text" id="searchQuery"
                           placeholder="{{ __('admin.search_placeholder') }}"
                           class="w-full bg-zinc-900 border border-zinc-700 rounded-md px-4 py-2.5 text-white placeholder-zinc-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Clips Table -->
        <div class="bg-zinc-800 rounded-md border border-zinc-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-900 border-b border-zinc-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                {{ __('admin.clip') }}
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                {{ __('admin.broadcaster') }}
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                {{ __('admin.submitter') }}
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                {{ __('admin.status') }}
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                {{ __('admin.submitted') }}
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                {{ __('admin.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-700">
                        @forelse($clips as $clip)
                            <tr class="hover:bg-zinc-700/50 transition-colors" wire:key="clip-{{ $clip->id }}">
                                <!-- Clip Info -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-24 h-14 bg-zinc-900 rounded overflow-hidden flex-shrink-0">
                                            @if($clip->local_thumbnail_path)
                                                <img src="{{ Storage::url($clip->local_thumbnail_path) }}"
                                                     alt="{{ $clip->title }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fa-solid fa-video text-zinc-600 text-lg"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('clips.view', $clip->uuid) }}"
                                               class="text-white font-medium hover:text-violet-400 transition-colors line-clamp-1">
                                                {{ $clip->title }}
                                            </a>
                                            <p class="text-xs text-zinc-400 mt-1">
                                                ID: {{ $clip->twitch_clip_id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Broadcaster -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-white">
                                        {{ $clip->broadcaster->twitch_display_name }}
                                    </div>
                                </td>

                                <!-- Submitter -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-white">
                                        {{ $clip->submitter->twitch_display_name }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                                            'approved' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                            'rejected' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                            'flagged' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                                        ];
                                        $color = $statusColors[$clip->status->value] ?? 'bg-neutral-500/10 text-neutral-400 border-neutral-500/20';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $color }}">
                                        {{ $clip->status->label() }}
                                    </span>
                                    @if($clip->is_featured)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border bg-purple-500/10 text-purple-400 border-purple-500/20 ml-2">
                                            {{ __('admin.featured') }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Submitted -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-400">
                                        {{ $clip->submitted_at->diffForHumans() }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($clip->status->value === 'pending')
                                            <button wire:click="approveClip({{ $clip->id }})"
                                                    wire:loading.attr="disabled"
                                                    class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded-md transition-colors disabled:opacity-50">
                                                {{ __('admin.approve') }}
                                            </button>
                                            <button wire:click="openRejectModal({{ $clip->id }})"
                                                    class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md transition-colors">
                                                {{ __('admin.reject') }}
                                            </button>
                                        @endif

                                        <button wire:click="toggleFeatured({{ $clip->id }})"
                                                wire:loading.attr="disabled"
                                                class="px-3 py-1.5 bg-violet-600 hover:bg-violet-700 text-white text-sm rounded-md transition-colors disabled:opacity-50">
                                            {{ $clip->is_featured ? __('admin.unfeature') : __('admin.feature') }}
                                        </button>

                                        <button wire:click="deleteClip({{ $clip->id }})"
                                                wire:confirm="{{ __('admin.confirm_delete') }}"
                                                wire:loading.attr="disabled"
                                                class="px-3 py-1.5 bg-zinc-700 hover:bg-zinc-600 text-white text-sm rounded-md transition-colors disabled:opacity-50">
                                            {{ __('admin.delete') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-zinc-400">
                                        <i class="fa-solid fa-inbox text-zinc-600 text-4xl mb-4"></i>
                                        <p class="text-lg font-medium">{{ __('admin.no_clips_found') }}</p>
                                        <p class="text-sm mt-1">{{ __('admin.try_different_filter') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-zinc-700">
                {{ $clips->links() }}
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    @if($showRejectModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-black/75 transition-opacity" wire:click="closeRejectModal"></div>

                <!-- Modal panel -->
                <div class="relative inline-block bg-zinc-800 rounded-md text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-zinc-700">
                    <div class="px-6 py-5">
                        <h3 class="text-lg font-semibold text-zinc-100 mb-4">
                            {{ __('admin.reject_clip') }}
                        </h3>

                        <div class="mb-4">
                            <label for="rejectReason" class="block text-sm font-medium text-zinc-300 mb-2">
                                {{ __('admin.rejection_reason') }}
                            </label>
                            <textarea wire:model="rejectReason" id="rejectReason" rows="4"
                                      class="w-full bg-zinc-900 border border-zinc-700 rounded-md px-4 py-2.5 text-white placeholder-zinc-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                                      placeholder="{{ __('admin.rejection_reason_placeholder') }}"></textarea>
                            @error('rejectReason')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button wire:click="closeRejectModal" type="button"
                                    class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-white rounded-md transition-colors">
                                {{ __('admin.cancel') }}
                            </button>
                            <button wire:click="rejectClip" type="button"
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors disabled:opacity-50">
                                {{ __('admin.reject') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if(session()->has('success'))
        <div class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-md shadow-lg flex items-center gap-3">
            <i class="fa-solid fa-check text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
</div>
