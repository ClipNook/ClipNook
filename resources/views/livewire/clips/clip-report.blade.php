<div>
    @if (session()->has('error'))
        <x-ui.alert type="error" class="mb-4">
            {{ session('error') }}
        </x-ui.alert>
    @endif

    @if (session()->has('message'))
        <x-ui.alert type="success" class="mb-4">
            {{ session('message') }}
        </x-ui.alert>
    @endif

    <x-ui.button
        wire:click="openModal"
        variant="danger"
        size="sm"
        class="mb-4"
    >
        <i class="fa-solid fa-flag mr-2"></i>
        {{ __('clips.report_clip') }}
    </x-ui.button>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75" wire:click="closeModal">
            <div class="bg-zinc-900 rounded-md border border-zinc-800 p-6 max-w-md w-full" wire:click.stop>
                <h3 class="text-xl font-semibold text-zinc-100 mb-4">{{ __('clips.report_title') }}</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">{{ __('clips.report_reason') }}</label>
                        <select
                            wire:model="reason"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-md text-white focus:border-violet-500 focus:outline-none"
                        >
                            @foreach (__('clips.report_reasons') as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('reason') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">{{ __('clips.report_description') }}</label>
                        <textarea
                            wire:model="description"
                            rows="4"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-md text-white placeholder-zinc-500 focus:border-violet-500 focus:outline-none resize-none"
                        ></textarea>
                        @error('description') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-3">
                        <x-ui.button
                            wire:click="submitReport"
                            variant="danger"
                            class="flex-1"
                        >
                            {{ __('clips.submit_report') }}
                        </x-ui.button>
                        <x-ui.button
                            wire:click="closeModal"
                            variant="secondary"
                            class="flex-1"
                        >
                            {{ __('clips.cancel') }}
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
