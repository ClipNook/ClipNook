<div>
    <button 
        wire:click="openModal"
        class="w-full text-sm text-zinc-400 hover:text-red-400 transition-colors flex items-center justify-center gap-2 py-2"
    >
        <i class="fa-solid fa-flag text-xs"></i>
        <span>{{ $this->reportText() }}</span>
    </button>

    @if ($showModal)
        <!-- Mobile Bottom Sheet -->
        <div class="fixed inset-0 z-50 md:hidden">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/75 backdrop-blur-sm" wire:click="closeModal"></div>

            <!-- Bottom Sheet -->
            <div class="fixed bottom-0 left-0 right-0 bg-zinc-900 border-t border-zinc-800 rounded-t-2xl max-h-[90vh] overflow-hidden animate-in slide-in-from-bottom duration-300">
                <div class="p-6 pb-safe">
                    <!-- Handle -->
                    <div class="flex justify-center mb-4">
                        <div class="w-12 h-1.5 bg-zinc-600 rounded-full"></div>
                    </div>

                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-zinc-100">{{ $this->reportTitle() }}</h3>
                        <button wire:click="closeModal" class="p-2 text-zinc-400 hover:text-zinc-200 transition-colors">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Form -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">{{ __('clips.report_reason') }}</label>
                            <select
                                wire:model="reason"
                                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:border-(--color-accent-500) focus:outline-none text-base"
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
                                placeholder="{{ __('clips.report_description_placeholder') }}"
                                rows="4"
                                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:border-(--color-accent-500) focus:outline-none resize-none text-base"
                            ></textarea>
                            @error('description') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-3 pt-2">
                            <x-ui.button
                                wire:click="submitReport"
                                variant="danger"
                                class="flex-1 py-3 text-base font-medium"
                            >
                                {{ __('clips.submit_report') }}
                            </x-ui.button>
                            <x-ui.button
                                wire:click="closeModal"
                                variant="secondary"
                                class="flex-1 py-3 text-base font-medium"
                            >
                                {{ __('clips.cancel') }}
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Modal -->
        <div class="hidden md:fixed md:inset-0 md:z-50 md:flex md:items-center md:justify-center md:p-4" wire:click="closeModal">
            <div class="bg-zinc-900 rounded-md border border-zinc-800 p-6 max-w-md w-full" wire:click.stop>
                <h3 class="text-xl font-semibold text-zinc-100 mb-4">{{ $this->reportTitle() }}</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">{{ __('clips.report_reason') }}</label>
                        <select
                            wire:model="reason"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-md text-white focus:border-(--color-accent-500) focus:outline-none"
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
                            placeholder="{{ __('clips.report_description_placeholder') }}"
                            rows="4"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-md text-white placeholder-zinc-500 focus:border-(--color-accent-500) focus:outline-none resize-none"
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
