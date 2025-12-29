{{-- User Settings Page --}}
<x-layouts.app :title="__('ui.settings')" :header="__('ui.settings')" :subheader="__('ui.settings_description')">

    <div class="max-w-6xl mx-auto space-y-8">
        {{-- Settings Grid --}}
        <div class="grid lg:grid-cols-3 gap-8">

            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Profile Information --}}
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user text-gray-600 dark:text-gray-400"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('ui.profile_information') }}</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.profile_info_description') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <form method="POST" action="{{ route('settings.profile.update') }}" class="space-y-6">
                            @csrf

                            {{-- Twitch Information (Read-only) --}}
                            <div class="grid md:grid-cols-2 gap-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('ui.display_name') }}
                                    </label>
                                    <p class="text-gray-900 dark:text-white font-medium">{{ auth()->user()->display_name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('ui.username') }}
                                    </label>
                                    <p class="text-gray-900 dark:text-white">{{ auth()->user()->twitch_login }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('ui.email_address') }}
                                    </label>
                                    <p class="text-gray-900 dark:text-white">{{ auth()->user()->twitch_email }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('ui.twitch_id') }}
                                    </label>
                                    <p class="text-gray-900 dark:text-white font-mono text-sm">{{ auth()->user()->twitch_id }}</p>
                                </div>
                            </div>

                            {{-- Biography --}}
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    {{ __('ui.biography') }}
                                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ __('ui.optional') }})</span>
                                </label>
                                <textarea id="bio" name="bio" rows="4" maxlength="500"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                                    placeholder="{{ __('ui.bio_placeholder') }}">{{ old('bio', auth()->user()->bio) }}</textarea>
                                <div class="mt-2 flex justify-between items-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.bio_help') }}</p>
                                    <span class="text-xs text-gray-500 dark:text-gray-400" id="bio-counter">
                                        {{ strlen(old('bio', auth()->user()->bio ?? '')) }}/500
                                    </span>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-800 flex justify-end">
                                <x-button type="submit" variant="primary" accent="bg">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ __('ui.save_changes') }}
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Account Roles --}}
                <div x-data="roleSettings" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-tag text-gray-600 dark:text-gray-400"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('ui.account_roles') }}</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.roles_description') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <form method="POST" action="{{ route('settings.roles.update') }}"
                            @submit.prevent="submitForm">
                            @csrf
                            @method('POST')

                            {{-- Role Cards Grid --}}
                            <div class="grid md:grid-cols-3 gap-4 mb-8">
                                {{-- Viewer (always active) --}}
                                <div
                                    class="p-5 border rounded-xl transition-all duration-200 hover:shadow-md
                                          {{ auth()->user()->isViewer() ? 'border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-950/20' : 'border-gray-200 dark:border-gray-800' }}">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div
                                            class="w-10 h-10 rounded-full flex items-center justify-center
                                                  {{ auth()->user()->isViewer() ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' }}">
                                            <i class="fas fa-eye text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                                {{ __('ui.viewer') }}</h3>
                                            <div
                                                class="inline-flex items-center gap-1 px-2 py-0.5 mt-1 text-xs font-semibold rounded-full
                                                      {{ auth()->user()->isViewer() ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                                                <i class="fas fa-check text-xs"></i>
                                                {{ __('ui.active') }}
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('ui.viewer_description') }}
                                    </p>
                                    <ul class="mt-3 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                                        <li class="flex items-center gap-2">
                                            <i class="fas fa-check text-green-500"></i>
                                            {{ __('ui.viewer_permission_1') }}
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <i class="fas fa-check text-green-500"></i>
                                            {{ __('ui.viewer_permission_2') }}
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <i class="fas fa-check text-green-500"></i>
                                            {{ __('ui.viewer_permission_3') }}
                                        </li>
                                    </ul>
                                </div>

                                {{-- Streamer --}}
                                <div class="p-5 border rounded-xl transition-all duration-200 hover:shadow-md
                                          {{ $isStreamer ? 'border-purple-200 dark:border-purple-800 bg-purple-50/50 dark:bg-purple-950/20' : 'border-gray-200 dark:border-gray-800' }}"
                                    x-bind:class="{ 'border-purple-200 dark:border-purple-800 bg-purple-50/50 dark:bg-purple-950/20': isStreamer }">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                                      {{ $isStreamer ? 'bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' }}"
                                                x-bind:class="{ 'bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400': isStreamer }">
                                                <i class="fas fa-broadcast-tower text-sm"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                                    {{ __('ui.streamer') }}</h3>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <div class="relative inline-flex items-center">
                                                        <input type="checkbox" id="is_streamer" name="is_streamer"
                                                            value="1" x-model="isStreamer" class="sr-only peer">
                                                        <div
                                                            @click="isStreamer = !isStreamer"
                                                            @keydown.enter.prevent="isStreamer = !isStreamer"
                                                            @keydown.space.prevent="isStreamer = !isStreamer"
                                                            tabindex="0"
                                                            role="switch"
                                                            x-bind:aria-checked="isStreamer"
                                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white dark:after:bg-gray-800 after:border-gray-300 dark:after:border-gray-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-current">
                                                        </div>
                                                    </div>
                                                    <span class="text-xs font-medium"
                                                        x-text="isStreamer ? '{{ __('ui.active') }}' : '{{ __('ui.inactive') }}'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('ui.streamer_description') }}
                                    </p>
                                    <ul class="mt-3 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                                        <li class="flex items-center gap-2">
                                            <i class="fas"
                                                x-bind:class="isStreamer ? 'fa-check text-green-500' :
                                                    'fa-times text-gray-300 dark:text-gray-600'"></i>
                                            {{ __('ui.streamer_permission_1') }}
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <i class="fas"
                                                x-bind:class="isStreamer ? 'fa-check text-green-500' :
                                                    'fa-times text-gray-300 dark:text-gray-600'"></i>
                                            {{ __('ui.streamer_permission_2') }}
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <i class="fas"
                                                x-bind:class="isStreamer ? 'fa-check text-green-500' :
                                                    'fa-times text-gray-300 dark:text-gray-600'"></i>
                                            {{ __('ui.streamer_permission_3') }}
                                        </li>
                                    </ul>
                                </div>

                                {{-- Cutter --}}
                                <div class="p-5 border rounded-xl transition-all duration-200 hover:shadow-md
                                          {{ $isCutter ? 'border-teal-200 dark:border-teal-800 bg-teal-50/50 dark:bg-teal-950/20' : 'border-gray-200 dark:border-gray-800' }}"
                                    x-bind:class="{ 'border-teal-200 dark:border-teal-800 bg-teal-50/50 dark:bg-teal-950/20': isCutter }">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                                      {{ $isCutter ? 'bg-teal-100 dark:bg-teal-900 text-teal-600 dark:text-teal-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' }}"
                                                x-bind:class="{ 'bg-teal-100 dark:bg-teal-900 text-teal-600 dark:text-teal-400': isCutter }">
                                                <i class="fas fa-scissors text-sm"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                                    {{ __('ui.cutter') }}</h3>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <div class="relative inline-flex items-center">
                                                        <input type="checkbox" id="is_cutter" name="is_cutter"
                                                            value="1" x-model="isCutter" class="sr-only peer">
                                                        <div
                                                            @click="isCutter = !isCutter"
                                                            @keydown.enter.prevent="isCutter = !isCutter"
                                                            @keydown.space.prevent="isCutter = !isCutter"
                                                            tabindex="0"
                                                            role="switch"
                                                            x-bind:aria-checked="isCutter"
                                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white dark:after:bg-gray-800 after:border-gray-300 dark:after:border-gray-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-current">
                                                        </div>
                                                    </div>
                                                    <span class="text-xs font-medium"
                                                        x-text="isCutter ? '{{ __('ui.active') }}' : '{{ __('ui.inactive') }}'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('ui.cutter_description') }}
                                    </p>
                                    <ul class="mt-3 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                                        <li class="flex items-center gap-2">
                                            <i class="fas"
                                                x-bind:class="isCutter ? 'fa-check text-green-500' :
                                                    'fa-times text-gray-300 dark:text-gray-600'"></i>
                                            {{ __('ui.cutter_permission_1') }}
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <i class="fas"
                                                x-bind:class="isCutter ? 'fa-check text-green-500' :
                                                    'fa-times text-gray-300 dark:text-gray-600'"></i>
                                            {{ __('ui.cutter_permission_2') }}
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <i class="fas"
                                                x-bind:class="isCutter ? 'fa-check text-green-500' :
                                                    'fa-times text-gray-300 dark:text-gray-600'"></i>
                                            {{ __('ui.cutter_permission_3') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            {{-- Streamer Introduction --}}
                            <div x-show="isStreamer" x-cloak x-transition class="mb-6">
                                <div
                                    class="p-5 border border-purple-200 dark:border-purple-800 bg-purple-50/30 dark:bg-purple-950/10 rounded-xl">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div
                                            class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                            <i class="fas fa-comment text-purple-600 dark:text-purple-400 text-sm"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ __('ui.streamer_introduction') }}
                                            </h3>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ __('ui.intro_help') }}
                                            </p>
                                        </div>
                                    </div>
                                    <textarea id="intro" name="intro" rows="3" x-model="intro" maxlength="500"
                                        class="w-full px-4 py-3 border border-purple-200 dark:border-purple-800 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="{{ __('ui.intro_placeholder') }}"></textarea>
                                    <div class="mt-2 flex items-center justify-between text-xs">
                                        <span class="text-gray-500 dark:text-gray-400">
                                            {{ __('ui.intro_count') }}
                                        </span>
                                        <span class="font-mono" x-text="intro.length + '/500'"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Clip Sharing Permission --}}
                            <div x-show="isStreamer" x-cloak x-transition class="mb-6">
                                <div
                                    class="p-5 border border-purple-200 dark:border-purple-800 bg-purple-50/30 dark:bg-purple-950/10 rounded-xl">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                                <i class="fas fa-share text-purple-600 dark:text-purple-400 text-sm"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                                                    {{ __('ui.clip_sharing') }}
                                                </h3>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                                    {{ __('ui.clip_sharing_description') }}
                                                </p>
                                            </div>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" id="allow_clip_sharing" name="allow_clip_sharing"
                                                value="1" x-model="allowClipSharing" class="sr-only peer">
                                            <div
                                                @click="allowClipSharing = !allowClipSharing"
                                                @keydown.enter.prevent="allowClipSharing = !allowClipSharing"
                                                @keydown.space.prevent="allowClipSharing = !allowClipSharing"
                                                tabindex="0"
                                                role="switch"
                                                x-bind:aria-checked="allowClipSharing"
                                                class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white dark:after:bg-gray-800 after:border-gray-300 dark:after:border-gray-600 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-current">
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Cutter Availability --}}
                            <div x-show="isCutter" x-cloak x-transition class="mb-6">
                                <div
                                    class="p-5 border border-teal-200 dark:border-teal-800 bg-teal-50/30 dark:bg-teal-950/10 rounded-xl">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-teal-100 dark:bg-teal-900 flex items-center justify-center">
                                                <i
                                                    class="fas fa-calendar-check text-teal-600 dark:text-teal-400 text-sm"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                                                    {{ __('ui.availability') }}
                                                </h3>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                                    {{ __('ui.availability_description') }}
                                                </p>
                                            </div>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" id="available_for_jobs" name="available_for_jobs"
                                                value="1" x-model="availableForJobs" class="sr-only peer">
                                            <div
                                                @click="availableForJobs = !availableForJobs"
                                                @keydown.enter.prevent="availableForJobs = !availableForJobs"
                                                @keydown.space.prevent="availableForJobs = !availableForJobs"
                                                tabindex="0"
                                                role="switch"
                                                x-bind:aria-checked="availableForJobs"
                                                class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white dark:after:bg-gray-800 after:border-gray-300 dark:after:border-gray-600 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-current">
                                            </div>
                                        </label>
                                    </div>

                                    <template x-if="availableForJobs">
                                        <div class="mt-4 space-y-3">
                                            <div class="grid md:grid-cols-2 gap-4">
                                                <div>
                                                    <label
                                                        class="block text-xs font-medium text-gray-900 dark:text-white mb-2">
                                                        {{ __('ui.hourly_rate') }} ({{ __('ui.optional') }})
                                                    </label>
                                                    <div class="relative">
                                                        <div
                                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <span class="text-gray-500 dark:text-gray-400">€</span>
                                                        </div>
                                                        <input type="number" name="hourly_rate" x-model="hourlyRate"
                                                            min="0" step="0.01"
                                                            class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                                            placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-medium text-gray-900 dark:text-white mb-2">
                                                        {{ __('ui.response_time') }}
                                                    </label>
                                                    <select name="response_time" x-model="responseTime"
                                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                                        <option value="24">{{ __('ui.within_24h') }}</option>
                                                        <option value="48">{{ __('ui.within_48h') }}</option>
                                                        <option value="72">{{ __('ui.within_72h') }}</option>
                                                        <option value="168">{{ __('ui.within_week') }}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-gray-900 dark:text-white mb-2">
                                                    {{ __('ui.skills') }} ({{ __('ui.optional') }})
                                                </label>
                                                <div class="flex flex-wrap gap-2">
                                                    <template x-for="(skill, index) in skills" :key="index">
                                                        <div
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-teal-100 dark:bg-teal-900 text-teal-700 dark:text-teal-300 rounded-full text-xs font-medium">
                                                            <span x-text="skill"></span>
                                                            <button type="button" @click="removeSkill(index)"
                                                                class="w-4 h-4 rounded-full hover:bg-teal-200 dark:hover:bg-teal-800 flex items-center justify-center">
                                                                <i class="fas fa-times text-xs"></i>
                                                            </button>
                                                        </div>
                                                    </template>
                                                    <div class="relative">
                                                        <input type="text" x-model="newSkill"
                                                            @keydown.enter.prevent="addSkill"
                                                            placeholder="{{ __('ui.add_skill') }}"
                                                            class="pl-3 pr-8 py-1.5 border border-gray-300 dark:border-gray-700 rounded-full text-xs bg-white dark:bg-gray-800">
                                                        <button type="button" @click="addSkill"
                                                            class="absolute right-1 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full flex items-center justify-center text-xs"
                                                            data-accent="bg">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="skills"
                                                    x-bind:value="JSON.stringify(skills)">
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div
                                class="pt-6 border-t border-gray-200 dark:border-gray-800 flex items-center justify-between">
                                <span x-show="hasChanges" x-cloak
                                    class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                    <i class="fas fa-exclamation-circle text-yellow-500"></i>
                                    {{ __('ui.unsaved_changes') }}
                                </span>
                                <div class="flex items-center gap-3">
                                    <x-button type="button" variant="neutral" size="md" @click="resetForm">
                                        {{ __('ui.reset') }}
                                    </x-button>
                                    <x-button type="submit" variant="primary" size="md" accent="bg"
                                        x-bind:disabled="!hasChanges">
                                        <i class="fas fa-save text-sm mr-2"></i>
                                        {{ __('ui.save_changes') }}
                                    </x-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-8">

                {{-- Avatar Settings --}}
                <div x-data="avatarSettings" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-600 dark:text-gray-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('ui.avatar') }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.avatar_description') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        {{-- Current Avatar --}}
                        <div class="flex items-center gap-4 mb-6">
                            <img x-bind:src="currentAvatar" alt="Avatar" class="w-16 h-16 rounded-full border-2 border-gray-200 dark:border-gray-700">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="avatarStatus.text"></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400" x-text="avatarSource"></p>
                            </div>
                        </div>

                        {{-- Avatar Actions --}}
                        <div class="space-y-3">
                            {{-- Upload Custom Avatar --}}
                            <div>
                                <input type="file" id="avatar-upload" accept="image/*" @change="uploadAvatar($event)" class="hidden">
                                <label for="avatar-upload" class="block">
                                    <x-button variant="secondary" class="w-full">
                                        <i class="fas fa-upload mr-2"></i>
                                        {{ __('ui.upload_custom_avatar') }}
                                    </x-button>
                                </label>
                            </div>

                            {{-- Restore from Twitch --}}
                            <x-button x-show="hasTwitchConnection && !hasTwitchAvatar" variant="secondary" class="w-full" @click="openDialog('restore')">
                                <i class="fas fa-sync mr-2"></i>
                                {{ __('ui.restore_from_twitch') }}
                            </x-button>

                            {{-- Remove Avatar --}}
                            <x-button variant="danger" class="w-full" @click="openDialog('remove')">
                                <i class="fas fa-trash mr-2"></i>
                                {{ __('ui.remove_avatar') }}
                            </x-button>
                        </div>
                    </div>
                </div>

                {{-- Preferences --}}
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-palette text-gray-600 dark:text-gray-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('ui.preferences') }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.preferences_description') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Accent Color --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                                {{ __('ui.accent_color') }}
                            </label>
                            <x-color-picker />
                        </div>

                        {{-- Theme Preference --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                                {{ __('ui.theme') }}
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="setTheme('light')"
                                    class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-300 dark:hover:border-gray-600 transition-colors"
                                    x-bind:class="{ 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20': theme === 'light' }">
                                    <i class="fas fa-sun text-lg mb-1 block"></i>
                                    <span class="text-xs">{{ __('ui.light') }}</span>
                                </button>
                                <button type="button" @click="setTheme('dark')"
                                    class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-300 dark:hover:border-gray-600 transition-colors"
                                    x-bind:class="{ 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20': theme === 'dark' }">
                                    <i class="fas fa-moon text-lg mb-1 block"></i>
                                    <span class="text-xs">{{ __('ui.dark') }}</span>
                                </button>
                                <button type="button" @click="setTheme('system')"
                                    class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-300 dark:hover:border-gray-600 transition-colors"
                                    x-bind:class="{ 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20': theme === 'system' }">
                                    <i class="fas fa-desktop text-lg mb-1 block"></i>
                                    <span class="text-xs">{{ __('ui.system') }}</span>
                                </button>
                            </div>
                        </div>

                        {{-- Language --}}
                        <div>
                            <label for="locale" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                {{ __('ui.language') }}
                            </label>
                            <select id="locale" name="locale" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="en" {{ auth()->user()->locale === 'en' ? 'selected' : '' }}>English</option>
                                <option value="de" {{ auth()->user()->locale === 'de' ? 'selected' : '' }}>Deutsch</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Account Management --}}
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('ui.account_management') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.account_management_description') }}</p>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        {{-- Export Data --}}
                        <form method="POST" action="{{ route('settings.export') }}">
                            @csrf
                            <x-button type="submit" variant="secondary" class="w-full">
                                <i class="fas fa-download mr-2"></i>
                                {{ __('ui.export_data') }}
                            </x-button>
                        </form>

                        {{-- Delete Account --}}
                        <x-button variant="danger" class="w-full" @click="openDialog('delete')">
                            <i class="fas fa-trash mr-2"></i>
                            {{ __('ui.delete_account') }}
                        </x-button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Dialog System --}}
    <x-dialog />

    {{-- JavaScript --}}
    <script>
        document.addEventListener('alpine:init', () => {
            // Role Settings Component
            Alpine.data('roleSettings', () => ({
                isStreamer: @json((bool) auth()->user()->is_streamer),
                isCutter: @json((bool) auth()->user()->is_cutter),
                intro: @json(old('intro', auth()->user()->intro ?? '')),
                availableForJobs: @json((bool) auth()->user()->available_for_jobs),
                allowClipSharing: @json((bool) (auth()->user()->allow_clip_sharing ?? false)),
                hourlyRate: {{ old('hourly_rate', auth()->user()->hourly_rate) ?? 0 }},
                responseTime: '{{ old('response_time', auth()->user()->response_time) ?? '24' }}',
                skills: @json(old('skills', auth()->user()->skills ? json_decode(auth()->user()->skills, true) : [])),
                newSkill: '',

                originalValues: {},

                init() {
                    this.originalValues = {
                        isStreamer: this.isStreamer,
                        isCutter: this.isCutter,
                        intro: this.intro,
                        availableForJobs: this.availableForJobs,
                        allowClipSharing: this.allowClipSharing,
                        hourlyRate: this.hourlyRate,
                        responseTime: this.responseTime,
                        skills: [...this.skills]
                    };
                },

                get hasChanges() {
                    return this.isStreamer !== this.originalValues.isStreamer ||
                        this.isCutter !== this.originalValues.isCutter ||
                        this.intro !== this.originalValues.intro ||
                        this.availableForJobs !== this.originalValues.availableForJobs ||
                        this.allowClipSharing !== this.originalValues.allowClipSharing ||
                        this.hourlyRate !== this.originalValues.hourlyRate ||
                        this.responseTime !== this.originalValues.responseTime ||
                        JSON.stringify(this.skills) !== JSON.stringify(this.originalValues.skills);
                },

                addSkill() {
                    if (this.newSkill.trim() && !this.skills.includes(this.newSkill.trim())) {
                        this.skills.push(this.newSkill.trim());
                        this.newSkill = '';
                    }
                },

                removeSkill(index) {
                    this.skills.splice(index, 1);
                },

                resetForm() {
                    this.isStreamer = this.originalValues.isStreamer;
                    this.isCutter = this.originalValues.isCutter;
                    this.intro = this.originalValues.intro;
                    this.availableForJobs = this.originalValues.availableForJobs;
                    this.allowClipSharing = this.originalValues.allowClipSharing;
                    this.hourlyRate = this.originalValues.hourlyRate;
                    this.responseTime = this.originalValues.responseTime;
                    this.skills = [...this.originalValues.skills];
                },

                submitForm() {
                    if (this.hasChanges) {
                        this.$el.submit();
                    }
                }
            }));

            // Avatar Settings Component
            Alpine.data('avatarSettings', () => ({
                isAvatarDisabled: {{ auth()->user()->isAvatarDisabled() ? 'true' : 'false' }},
                hasTwitchAvatar: {{ !empty(auth()->user()->twitch_avatar) ? 'true' : 'false' }},
                hasTwitchConnection: {{ auth()->user()->isTwitchConnected() ? 'true' : 'false' }},
                currentAvatar: '{{ auth()->user()->avatar_url }}',

                get avatarStatus() {
                    if (this.isAvatarDisabled) {
                        return {
                            text: '{{ __('ui.avatar_disabled') }}',
                            icon: 'fas fa-ban text-red-500',
                            class: 'bg-red-100 dark:bg-red-900'
                        };
                    }

                    if (this.hasTwitchAvatar) {
                        return {
                            text: '{{ __('ui.avatar_twitch') }}',
                            icon: 'fab fa-twitch text-purple-500',
                            class: 'bg-purple-100 dark:bg-purple-900'
                        };
                    }

                    return {
                        text: '{{ __('ui.avatar_custom') }}',
                        icon: 'fas fa-user text-blue-500',
                        class: 'bg-blue-100 dark:bg-blue-900'
                    };
                },

                get avatarSource() {
                    if (this.isAvatarDisabled) return '{{ __('ui.avatar_source_disabled') }}';
                    if (this.hasTwitchAvatar) return '{{ __('ui.avatar_source_twitch') }}';
                    return '{{ __('ui.avatar_source_custom') }}';
                },

                openDialog(action) {
                    const dialogs = {
                        remove: {
                            title: '{{ __('ui.remove_avatar_title') }}',
                            message: '{{ __('ui.remove_avatar_confirm') }}',
                            action: '{{ route('settings.avatar.update') }}',
                            actionType: 'remove',
                            method: 'POST',
                            type: 'danger',
                            confirmButtonText: '{{ __('ui.remove') }}'
                        },
                        restore: {
                            title: '{{ __('ui.restore_avatar_title') }}',
                            message: '{{ __('ui.restore_avatar_confirm') }}',
                            action: '{{ route('settings.avatar.update') }}',
                            actionType: 'restore',
                            method: 'POST',
                            type: 'primary',
                            confirmButtonText: '{{ __('ui.restore') }}'
                        },
                        enable: {
                            title: '{{ __('ui.enable_avatar_title') }}',
                            message: '{{ __('ui.enable_avatar_confirm') }}',
                            action: '{{ route('settings.avatar.update') }}',
                            actionType: 'restore',
                            method: 'POST',
                            type: 'primary',
                            confirmButtonText: '{{ __('ui.enable') }}'
                        }
                    };

                    this.$dispatch('open-dialog', dialogs[action]);
                },

                uploadAvatar(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    // Validate file
                    if (!file.type.match('image.*')) {
                        this.$dispatch('show-toast', {
                            type: 'error',
                            message: '{{ __('ui.avatar_invalid_type') }}'
                        });
                        return;
                    }

                    if (file.size > 5 * 1024 * 1024) { // 5MB
                        this.$dispatch('show-toast', {
                            type: 'error',
                            message: '{{ __('ui.avatar_too_large') }}'
                        });
                        return;
                    }

                    // Preview image
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.currentAvatar = e.target.result;
                        this.uploadToServer(file);
                    };
                    reader.readAsDataURL(file);
                },

                async uploadToServer(file) {
                    const formData = new FormData();
                    formData.append('avatar', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    try {
                        const response = await fetch('{{ route('settings.avatar.upload') }}', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.$dispatch('show-toast', {
                                type: 'success',
                                message: data.message || '{{ __('ui.avatar_upload_success') }}'
                            });

                            this.hasTwitchAvatar = false;
                            this.isAvatarDisabled = false;
                            this.currentAvatar = data.avatar_url;
                        } else {
                            this.$dispatch('show-toast', {
                                type: 'error',
                                message: data.message || '{{ __('ui.avatar_upload_error') }}'
                            });
                        }
                    } catch (error) {
                        this.$dispatch('show-toast', {
                            type: 'error',
                            message: '{{ __('ui.avatar_upload_error') }}'
                        });
                    }
                }
            }));

            // Theme Manager Component
            Alpine.data('themeManager', () => ({
                theme: localStorage.getItem('theme') || 'system',

                init() {
                    this.applyTheme();
                },

                setTheme(theme) {
                    this.theme = theme;
                    localStorage.setItem('theme', theme);
                    this.applyTheme();
                },

                applyTheme() {
                    const root = document.documentElement;
                    const isDark = this.theme === 'dark' ||
                        (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

                    root.classList.toggle('dark', isDark);
                }
            }));
        });

        // Profile Form Character Counter
        document.addEventListener('DOMContentLoaded', function() {
            const bioTextarea = document.getElementById('bio');
            const bioCounter = document.getElementById('bio-counter');

            if (bioTextarea && bioCounter) {
                bioTextarea.addEventListener('input', function() {
                    const length = this.value.length;
                    bioCounter.textContent = length + '/500';

                    if (length > 500) {
                        bioCounter.classList.add('text-red-600', 'dark:text-red-400');
                    } else {
                        bioCounter.classList.remove('text-red-600', 'dark:text-red-400');
                    }
                });
            }
        });
    </script>
</x-layouts.app>
