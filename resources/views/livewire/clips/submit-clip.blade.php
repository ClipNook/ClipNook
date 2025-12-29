<div class="space-y-6">
    <form wire:submit.prevent="check" novalidate class="space-y-5">
        {{-- Eingabefeld mit direktem Livewire-Model-Binding --}}
        <div>
            <label for="clip-input" class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                {{ __('clip.submit.labels.input') }}
            </label>

            <input id="clip-input" wire:model.live="input" type="text" wire:keydown.enter.prevent="$refresh"
                aria-describedby="clip-help clip-error" placeholder="{{ __('clip.submit.labels.placeholder') }}"
                class="mt-1 block w-full px-4 py-3 border rounded-lg transition-colors duration-200
                       {{ $errors->has('input') ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-700' }}
                       bg-white dark:bg-gray-900 
                       text-gray-900 dark:text-gray-100
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                @keydown.escape="$wire.set('input', '')">
        </div>

        {{-- Hilfetexte mit Beispiel-Buttons --}}
        <div id="clip-help" class="space-y-2">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('clip.submit.examples.intro') }}
            </p>

            <div class="flex flex-wrap gap-2">
                @foreach ([
        'https://www.twitch.tv/zurret/clip/DreamyComfortableKimchiBCWarrior-KHiH2nw1Hgh2vAGN' => __('clip.submit.examples.full'),
        'https://clips.twitch.tv/DreamyComfortableKimchiBCWarrior-KHiH2nw1Hgh2vAGN' => __('clip.submit.examples.clips'),
        'DreamyComfortableKimchiBCWarrior-KHiH2nw1Hgh2vAGN' => __('clip.submit.examples.id'),
    ] as $example => $label)
                    <x-button size="sm" variant="neutral" type="button" wire:click="setInput('{{ $example }}')">
                        {{ $label }}
                    </x-button>
                @endforeach
            </div>
        </div>

        {{-- Validierungsfehler --}}
        @error('input')
            <div id="clip-error" class="p-3 bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex items-center gap-2 text-red-800 dark:text-red-200 text-sm">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $message }}</span>
                </div>
            </div>
        @enderror

        {{-- Submit-Button mit Ladezustand --}}
        <div class="flex items-center gap-3">
            <x-button type="submit" variant="{{ $accepted ? 'success' : 'primary' }}" wire:loading.attr="disabled" wire:target="check" class="inline-flex items-center justify-center gap-2 font-semibold">
                <i wire:loading wire:target="check" class="fas fa-spinner animate-spin" aria-hidden="true"></i>

                <span wire:loading.remove wire:target="check">
                    {{ $accepted ? __('clip.submit.actions.recheck') : __('clip.submit.actions.check') }}
                </span>

                <span wire:loading wire:target="check">
                    {{ __('clip.submit.actions.checking') }}
                </span>
            </x-button>

            @if ($input)
                <x-button size="sm" variant="neutral" type="button" wire:click="$set('input', '')">
                    {{ __('clip.submit.actions.reset') }}
                </x-button>
            @endif
        </div>
    </form>

    {{-- Livewire messages (validation / flow) --}}
    @if ($message)
        <div role="status" aria-live="polite"
            class="mt-3 p-3 rounded border text-sm font-medium {{ $accepted ? 'bg-green-50 border-green-200 text-green-800 dark:bg-green-950 dark:border-green-800 dark:text-green-200' : 'bg-red-50 border-red-200 text-red-800 dark:bg-red-950 dark:border-red-800 dark:text-red-200' }}">
            <div class="flex items-center gap-2">
                <span>{{ $message }}</span>
            </div>
        </div>
    @endif

    {{-- Statusmeldungen --}}
    @if (session()->has('message'))
        <div role="status" aria-live="polite"
            class="p-4 rounded-lg border text-sm font-medium transition-all duration-300
                    {{ session('status') === 'success'
                        ? 'bg-green-50 border-green-200 text-green-800 dark:bg-green-950 dark:border-green-800 dark:text-green-200'
                        : 'bg-red-50 border-red-200 text-red-800 dark:bg-red-950 dark:border-red-800 dark:text-red-200' }}">
            <div class="flex items-center gap-2">
                <i
                    class="fas {{ session('status') === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    {{-- Clip-Vorschau --}}
    @if ($accepted && $clip)
        <div x-data="{ copied: false }" class="space-y-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                {{ __('clip.submit.labels.header') }}
            </h3>

            <div
                class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="p-5">
                    <div class="flex flex-col md:flex-row gap-5">
                        {{-- Thumbnail --}}
                        <div class="shrink-0">
                            <div class="w-full md:w-48 h-32 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800">
                                @if (!empty($clip['thumbnail_url']))
                                    <img src="{{ $clip['thumbnail_url'] }}"
                                        alt="{{ __('clip.submit.alt.thumbnail', ['title' => $clip['title'] ?? '']) }}"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <i class="fas fa-image text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Clip-Details --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ $clip['title'] ?? __('clip.submit.labels.untitled') }}
                            </h4>

                            <div class="space-y-2 mb-4">
                                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                    @if (!empty($clip['broadcaster_name']))
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fas fa-user"></i>
                                            {{ $clip['broadcaster_name'] }}
                                        </span>
                                    @endif

                                    @if (!empty($clip['duration']))
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fas fa-clock"></i>
                                            {{ $clip['duration'] }}s
                                        </span>
                                    @endif

                                    @if (!empty($clip['view_count']))
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fas fa-eye"></i>
                                            {{ number_format($clip['view_count']) }}
                                            {{ __('clip.submit.labels.views') }}
                                        </span>
                                    @endif

                                    @if (!empty($clip['creator_name']))
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fas fa-user-circle"></i>
                                            {{ __('clip.submit.labels.creator') }}: {{ $clip['creator_name'] }}
                                        </span>
                                    @endif
                                </div>

                                @if (!empty($clip['game_name']))
                                    <div
                                        class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 dark:bg-gray-800 rounded-md">
                                        <i class="fas fa-gamepad text-gray-500"></i>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $clip['game_name'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Aktionen --}}
                            <div
                                class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                                <x-button variant="neutral" size="sm" href="{{ $clip['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-twitch"></i>
                                    {{ __('clip.submit.actions.open_on_twitch') }}
                                </x-button>

                                <x-button size="sm" variant="neutral" type="button" @click="navigator.clipboard.writeText('{{ $clip['id'] }}'); copied = true; setTimeout(() => copied = false, 2000)">
                                    <i class="fas" x-bind:class="copied ? 'fa-check' : 'fa-copy'"></i>
                                    <span x-text="copied ? '{{ __('clip.submit.actions.copied') }}' : '{{ __('clip.submit.actions.copy_id') }}'"></span>
                                </x-button>

                                @if (!empty($clip['video_available']) && !empty($clip['video_url']))
                                    <x-button size="sm" variant="neutral" href="{{ $clip['video_url'] }}" target="_blank" rel="noopener noreferrer">
                                        <i class="fas fa-play-circle"></i>
                                        {{ __('clip.submit.actions.watch_vod') }}
                                    </x-button>
                                @endif

                                <x-button variant="dark" class="ml-auto" wire:click="saveClip" wire:loading.attr="disabled" wire:target="saveClip">
                                    <i wire:loading wire:target="saveClip" class="fas fa-spinner animate-spin" aria-hidden="true"></i>

                                    <span wire:loading.remove wire:target="saveClip">
                                        <i class="fas fa-plus"></i>
                                        {{ __('clip.submit.actions.save') }}
                                    </span>

                                    <span wire:loading wire:target="saveClip">
                                        {{ __('clip.submit.actions.saving') }}
                                    </span>
                                </x-button>
                            </div>

                            {{-- Metadaten --}}
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                                <div
                                    class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-500">
                                    <span>
                                        <strong>{{ __('clip.submit.labels.id') }}</strong>
                                        <code class="ml-1 font-mono bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
                                            {{ $clip['id'] }}
                                        </code>
                                    </span>

                                    @if (!empty($clip['created_at']))
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fas fa-calendar"></i>
                                            {{ \Carbon\Carbon::parse($clip['created_at'])->locale(app()->getLocale())->isoFormat('LL') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
