{{-- Roles Settings Tab --}}
<div class="space-y-6">
    {{-- Account Roles --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-tag text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('ui.account_roles') }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.roles_description') }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('settings.roles.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Role Cards Grid --}}
                <div class="grid md:grid-cols-3 gap-4 mb-8">
                    {{-- Viewer (always active) --}}
                    <div class="p-5 border rounded-xl transition-all duration-200 hover:shadow-md border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-950/20">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400">
                                <i class="fas fa-eye text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('ui.viewer') }}</h3>
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 mt-1 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300">
                                    <i class="fas fa-check text-xs"></i>
                                    {{ __('ui.active') }}
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.viewer_description') }}</p>
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
                    <div class="p-5 border rounded-xl transition-all duration-200 hover:shadow-md {{ $user->is_streamer ? 'border-purple-200 dark:border-purple-800 bg-purple-50/50 dark:bg-purple-950/20' : 'border-gray-200 dark:border-gray-800' }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $user->is_streamer ? 'bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' }}">
                                    <i class="fas fa-broadcast-tower text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('ui.streamer') }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <input type="hidden" name="is_streamer" value="0">
                                        <input type="checkbox" id="is_streamer" name="is_streamer" value="1"
                                            {{ old('is_streamer', $user->is_streamer) ? 'checked' : '' }}
                                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                        <span class="text-xs font-medium {{ $user->is_streamer ? 'text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $user->is_streamer ? __('ui.active') : __('ui.inactive') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.streamer_description') }}</p>
                        <ul class="mt-3 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                            <li class="flex items-center gap-2">
                                <i class="fas {{ $user->is_streamer ? 'fa-check text-green-500' : 'fa-times text-gray-300 dark:text-gray-600' }}"></i>
                                {{ __('ui.streamer_permission_1') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas {{ $user->is_streamer ? 'fa-check text-green-500' : 'fa-times text-gray-300 dark:text-gray-600' }}"></i>
                                {{ __('ui.streamer_permission_2') }}
                            </li>
                        </ul>
                    </div>

                    {{-- Cutter --}}
                    <div class="p-5 border rounded-xl transition-all duration-200 hover:shadow-md {{ $user->is_cutter ? 'border-green-200 dark:border-green-800 bg-green-50/50 dark:bg-green-950/20' : 'border-gray-200 dark:border-gray-800' }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $user->is_cutter ? 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' }}">
                                    <i class="fas fa-cut text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('ui.cutter') }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <input type="hidden" name="is_cutter" value="0">
                                        <input type="checkbox" id="is_cutter" name="is_cutter" value="1"
                                            {{ old('is_cutter', $user->is_cutter) ? 'checked' : '' }}
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        <span class="text-xs font-medium {{ $user->is_cutter ? 'text-green-700 dark:text-green-300' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $user->is_cutter ? __('ui.active') : __('ui.inactive') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.cutter_description') }}</p>
                        <ul class="mt-3 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                            <li class="flex items-center gap-2">
                                <i class="fas {{ $user->is_cutter ? 'fa-check text-green-500' : 'fa-times text-gray-300 dark:text-gray-600' }}"></i>
                                {{ __('ui.cutter_permission_1') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas {{ $user->is_cutter ? 'fa-check text-green-500' : 'fa-times text-gray-300 dark:text-gray-600' }}"></i>
                                {{ __('ui.cutter_permission_2') }}
                            </li>
                        </ul>
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

    {{-- Streamer Profile (conditional) --}}
    @if($user->is_streamer)
        @include('settings.tabs.streamer-profile')
    @endif

    {{-- Cutter Profile (conditional) --}}
    @if($user->is_cutter)
        @include('settings.tabs.cutter-profile')
    @endif
</div>