<x-layouts.app :title="__('ui.settings')" :header="__('ui.settings')" :subheader="__('ui.settings_description')">
    <div class="max-w-4xl mx-auto space-y-6">
        
        {{-- Profile Information --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" data-accent="bgLight">
                        <i class="fas fa-user text-sm" data-accent="text" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ __('ui.your_profile') }}
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                            {{ __('ui.profile_info_description') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="flex flex-col sm:flex-row items-start gap-6">
                    <div class="relative shrink-0">
                        <img src="{{ auth()->user()->avatar_url }}" 
                             alt="{{ auth()->user()->display_name }}" 
                             class="w-20 h-20 rounded-lg object-cover border-2 border-gray-200 dark:border-gray-700">
                        
                        @if(auth()->user()->isTwitchConnected())
                            <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-purple-600 rounded-full flex items-center justify-center border-2 border-white dark:border-gray-900">
                                <i class="fab fa-twitch text-xs text-white" aria-hidden="true"></i>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0 space-y-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                {{ auth()->user()->display_name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ auth()->user()->email }}
                            </p>
                        </div>

                        @if(auth()->user()->isTwitchConnected())
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 rounded-md text-sm font-semibold">
                                <i class="fab fa-twitch" aria-hidden="true"></i>
                                {{ __('ui.connected_to_twitch') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Account Roles & Settings --}}
        <div x-data="roleSettings()" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" data-accent="bgLight">
                        <i class="fas fa-user-tag text-sm" data-accent="text" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ __('ui.account_roles') }}
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                            {{ __('ui.roles_description') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('settings.update') }}" @submit.prevent="submitForm">
                    @csrf
                    @method('PATCH')

                    {{-- Role Selection --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        {{-- Viewer (always active) --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <i class="fas fa-eye text-gray-600 dark:text-gray-400 text-sm"></i>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('ui.viewer') }}</h3>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                {{ __('ui.viewer_description') }}
                            </p>
                        </div>

                        {{-- Streamer --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                        <i class="fas fa-broadcast-tower text-indigo-600 dark:text-indigo-400 text-sm"></i>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('ui.streamer') }}</h3>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_streamer" value="1" 
                                           x-model="isStreamer"
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                {{ __('ui.streamer_description') }}
                            </p>
                        </div>

                        {{-- Cutter --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                        <i class="fas fa-scissors text-green-600 dark:text-green-400 text-sm"></i>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('ui.cutter') }}</h3>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_cutter" value="1" 
                                           x-model="isCutter"
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                                </label>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                {{ __('ui.cutter_description') }}
                            </p>
                        </div>
                    </div>

                    {{-- Streamer Introduction --}}
                    <div x-show="isStreamer" x-cloak class="mb-6">
                        <label for="intro" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            {{ __('ui.introduce_yourself') }}
                        </label>
                        <textarea id="intro" name="intro" rows="3" 
                                  x-model="intro"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="{{ __('ui.intro_placeholder') }}"></textarea>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('ui.intro_help') }}
                        </p>
                    </div>

                    {{-- Cutter Availability --}}
                    <div x-show="isCutter" x-cloak class="mb-6">
                        <div class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <input type="checkbox" id="available_for_jobs" name="available_for_jobs" value="1"
                                   x-model="availableForJobs"
                                   class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <div>
                                <label for="available_for_jobs" class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('ui.available_for_jobs') }}
                                </label>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    {{ __('ui.available_for_jobs_description') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-800">
                        <span x-show="hasChanges" x-cloak class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('ui.unsaved_changes') }}
                        </span>
                        <x-button type="submit" variant="primary" size="md" accent="bg" x-bind:disabled="!hasChanges">
                            <i class="fas fa-save text-xs" aria-hidden="true"></i>
                            {{ __('ui.save_changes') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Avatar Settings --}}
        <div x-data="avatarSettings()" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" data-accent="bgLight">
                        <i class="fas fa-image text-sm" data-accent="text" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ __('ui.avatar_settings') }}
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                            {{ __('ui.avatar_settings_description') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Current Avatar --}}
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                            {{ __('ui.current_avatar') }}
                        </h3>
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <img src="{{ auth()->user()->avatar_url }}" 
                                     alt="{{ __('ui.your_avatar') }}"
                                     class="w-16 h-16 rounded-lg object-cover border-2 border-gray-200 dark:border-gray-700">
                                <div x-show="isAvatarDisabled" class="absolute inset-0 bg-black/60 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-ban text-white" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-900 dark:text-white font-medium" x-text="avatarStatus"></p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    <span x-show="!isAvatarDisabled">{{ __('ui.avatar_source') }}:</span>
                                    <span x-text="avatarSource"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Avatar Actions --}}
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                            {{ __('ui.avatar_actions') }}
                        </h3>
                        <div class="space-y-3">
                            <template x-if="!isAvatarDisabled && hasTwitchAvatar">
                                <x-button type="button" size="md" class="w-full text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950 hover:bg-red-100 dark:hover:bg-red-900" @click="openDialog('remove')">
                                    <i class="fas fa-trash-alt text-xs" aria-hidden="true"></i>
                                    {{ __('ui.remove_avatar') }}
                                </x-button>
                            </template>

                            <template x-if="hasTwitchConnection">
                                <x-button type="button" size="md" variant="primary" class="w-full" accent="bg" @click="openDialog(isAvatarDisabled ? 'enable' : 'restore')">
                                    <i class="fas fa-sync-alt text-xs" aria-hidden="true"></i>
                                    <span x-text="isAvatarDisabled ? '{{ __('ui.enable_avatar') }}' : '{{ __('ui.restore_avatar') }}'"></span>
                                </x-button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Account Management --}}
        <div x-data="accountManagement()" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-red-100 dark:bg-red-900">
                        <i class="fas fa-exclamation-triangle text-sm text-red-600 dark:text-red-400" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-red-600 dark:text-red-400">
                            {{ __('ui.account_management') }}
                        </h2>
                        <p class="text-sm text-red-700 dark:text-red-300 mt-0.5">
                            {{ __('ui.account_management_description') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">
                            {{ __('ui.delete_account') }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('ui.delete_account_warning') }}
                        </p>
                        <ul class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start gap-2">
                                <i class="fas fa-times text-red-500 text-xs mt-0.5"></i>
                                <span>{{ __('ui.delete_account_consequence_1') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-times text-red-500 text-xs mt-0.5"></i>
                                <span>{{ __('ui.delete_account_consequence_2') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-times text-red-500 text-xs mt-0.5"></i>
                                <span>{{ __('ui.delete_account_consequence_3') }}</span>
                            </li>
                        </ul>
                    </div>

                    <x-button type="button" size="md" class="inline-flex items-center gap-2 text-white bg-red-600 hover:bg-red-700" @click="openDialog()">
                        <i class="fas fa-user-times text-xs" aria-hidden="true"></i>
                        {{ __('ui.delete_my_account') }}
                    </x-button>
                </div>
            </div>
        </div>

    </div>

    {{-- Dialog System --}}
    <div x-data="dialogSystem()" 
         x-show="isOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        
        <div class="fixed inset-0 bg-black/50" @click="close" @keydown.escape.window="close"></div>

        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white dark:bg-gray-900 rounded-lg shadow-xl max-w-md w-full overflow-hidden">
            
            {{-- Dialog Header --}}
            <div class="px-6 py-4 border-b" x-bind:class="type === 'danger' ? 'bg-red-50 dark:bg-red-950 border-red-200 dark:border-red-800' : 'border-gray-200 dark:border-gray-800'">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold" x-bind:class="type === 'danger' ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white'">
                        <span x-text="title"></span>
                    </h3>
                    <x-button variant="icon" type="button" @click="close" x-bind:aria-label="'{{ __('ui.close') }}'" class="w-8 h-8">
                        <i class="fas fa-times text-sm" aria-hidden="true"></i>
                    </x-button>
                </div>
            </div>

            {{-- Dialog Content --}}
            <div class="px-6 py-4 space-y-4">
                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="message"></p>



                {{-- Action Form --}}
                <form x-ref="dialogForm" method="POST" x-bind:action="action" @submit.prevent="submitForm" class="contents">
                    <template x-if="type === 'danger' && confirmText">
                        <div class="space-y-3">
                            <div class="p-3 bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 rounded-lg">
                                <p class="text-sm font-medium text-red-900 dark:text-red-100">
                                    {{ __('ui.delete_confirmation_warning') }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    {{ __('ui.type_to_confirm') }}
                                    <span class="font-mono text-red-600 dark:text-red-400" x-text="confirmText"></span>
                                </label>
                                <input name="confirm_name" type="text" 
                                       x-model="userInput"
                                       @input="checkConfirmation"
                                       class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500"
                                       x-bind:class="userInput === confirmText ? 'border-red-500' : 'border-gray-300 dark:border-gray-700'"
                                       x-bind:placeholder="confirmText">
                            </div>
                        </div>
                    </template>
                    @csrf
                    <input type="hidden" name="_method" x-bind:value="method">

                    <input type="hidden" x-bind:name="actionType" value="1">

                    <div class="pt-2 flex items-center justify-end gap-3">
                        <x-button variant="neutral" size="sm" type="button" @click="close">
                            {{ __('ui.cancel') }}
                        </x-button>

                        <x-button type="submit" size="md" x-bind:disabled="type === 'danger' && confirmText && userInput !== confirmText" x-bind:class="type === 'danger' ? 'bg-red-600 hover:bg-red-700 text-white' : (type === 'primary' ? 'bg-indigo-600 hover:bg-indigo-700 text-white dark:text-gray-900' : '')" x-bind:data-accent="type === 'primary' ? 'bg' : ''">
                            <span x-text="confirmButtonText"></span>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Role Settings Component
        function roleSettings() {
            return {
                isStreamer: {{ auth()->user()->isStreamer() ? 'true' : 'false' }},
                isCutter: {{ auth()->user()->isCutter() ? 'true' : 'false' }},
                intro: @json(old('intro', auth()->user()->intro)),
                availableForJobs: {{ auth()->user()->available_for_jobs ? 'true' : 'false' }},
                originalValues: {},

                init() {
                    // Store original values for comparison
                    this.originalValues = {
                        isStreamer: this.isStreamer,
                        isCutter: this.isCutter,
                        intro: this.intro,
                        availableForJobs: this.availableForJobs
                    };
                },

                get hasChanges() {
                    return this.isStreamer !== this.originalValues.isStreamer ||
                           this.isCutter !== this.originalValues.isCutter ||
                           this.intro !== this.originalValues.intro ||
                           this.availableForJobs !== this.originalValues.availableForJobs;
                },

                submitForm() {
                    if (this.hasChanges) {
                        this.$el.submit();
                    }
                }
            };
        }

        // Avatar Settings Component
        function avatarSettings() {
            return {
                isAvatarDisabled: {{ auth()->user()->isAvatarDisabled() ? 'true' : 'false' }},
                hasTwitchAvatar: {{ !empty(auth()->user()->twitch_avatar) ? 'true' : 'false' }},
                hasTwitchConnection: {{ auth()->user()->isTwitchConnected() ? 'true' : 'false' }},

                get avatarStatus() {
                    return this.isAvatarDisabled 
                        ? '{{ __('ui.avatar_disabled') }}' 
                        : '{{ __('ui.avatar_active') }}';
                },

                get avatarSource() {
                    if (this.isAvatarDisabled) return '{{ __('ui.avatar_source_disabled') }}';
                    return this.hasTwitchAvatar 
                        ? '{{ __('ui.avatar_source_twitch') }}' 
                        : '{{ __('ui.avatar_source_custom') }}';
                },

                openDialog(action) {
                    const dialogs = {
                        remove: {
                            title: '{{ __('ui.remove_avatar_title') }}',
                            message: '{{ __('ui.remove_avatar_confirm') }}',
                            action: '{{ route('settings.update') }}',
                            actionType: 'remove_avatar',
                            method: 'PATCH',
                            type: 'danger',
                            confirmButtonText: '{{ __('ui.remove') }}'
                        },
                        restore: {
                            title: '{{ __('ui.restore_avatar_title') }}',
                            message: '{{ __('ui.restore_avatar_confirm') }}',
                            action: '{{ route('settings.update') }}',
                            actionType: 'restore_avatar',
                            method: 'PATCH',
                            type: 'primary',
                            confirmButtonText: '{{ __('ui.restore') }}'
                        },
                        enable: {
                            title: '{{ __('ui.enable_avatar_title') }}',
                            message: '{{ __('ui.enable_avatar_confirm') }}',
                            action: '{{ route('settings.update') }}',
                            actionType: 'restore_avatar',
                            method: 'PATCH',
                            type: 'primary',
                            confirmButtonText: '{{ __('ui.enable') }}'
                        }
                    };

                    this.$dispatch('open-dialog', dialogs[action]);
                }
            };
        }

        // Account Management Component
        function accountManagement() {
            return {
                openDialog() {
                    this.$dispatch('open-dialog', {
                        title: '{{ __('ui.delete_account_title') }}',
                        message: '{{ __('ui.delete_account_confirm') }}',
                        action: '{{ route('settings.destroy') }}',
                        actionType: 'delete_account',
                        method: 'DELETE',
                        type: 'danger',
                        confirmText: '{{ auth()->user()->display_name }}',
                        confirmButtonText: '{{ __('ui.delete_permanently') }}'
                    });
                }
            };
        }

        // Dialog System Component
        function dialogSystem() {
            return {
                isOpen: false,
                title: '',
                message: '',
                action: '',
                actionType: '',
                method: 'PATCH',
                type: 'primary',
                confirmText: null,
                confirmButtonText: '{{ __('ui.confirm') }}',
                userInput: '',

                init() {
                    // Listen for dialog open events
                    this.$watch('isOpen', (value) => {
                        if (value) {
                            document.body.style.overflow = 'hidden';
                        } else {
                            document.body.style.overflow = '';
                            this.reset();
                        }
                    });

                    // Global event listener for opening dialogs (listen on window so events from other components are caught)
                    window.addEventListener('open-dialog', (event) => {
                        this.open(event.detail);
                    });
                },

                open(config) {
                    this.title = config.title || '';
                    this.message = config.message || '';
                    this.action = config.action || '';
                    this.actionType = config.actionType || '';
                    this.method = config.method || 'PATCH';
                    this.type = config.type || 'primary';
                    this.confirmText = config.confirmText || null;
                    this.confirmButtonText = config.confirmButtonText || '{{ __('ui.confirm') }}';
                    this.userInput = '';
                    this.isOpen = true;

                    // Focus first input when dialog opens
                    this.$nextTick(() => {
                        const input = this.$el.querySelector('input');
                        if (input) input.focus();
                    });
                },

                close() {
                    this.isOpen = false;
                },

                reset() {
                    this.title = '';
                    this.message = '';
                    this.action = '';
                    this.actionType = '';
                    this.method = 'PATCH';
                    this.type = 'primary';
                    this.confirmText = null;
                    this.confirmButtonText = '{{ __('ui.confirm') }}';
                    this.userInput = '';
                },

                checkConfirmation() {
                    // Real-time validation feedback could be added here
                },

                submitForm() {
                    if (this.type === 'danger' && this.confirmText && this.userInput !== this.confirmText) {
                        return;
                    }

                    // Prefer the ref if present (more robust), fall back to querying the DOM
                    const form = (this.$refs && this.$refs.dialogForm) ? this.$refs.dialogForm : this.$el.querySelector('form');

                    if (form && typeof form.submit === 'function') {
                        form.submit();
                    }
                }
            };
        }
    </script>
</x-layouts.app>