{{-- Account Settings Tab --}}
<div class="space-y-6">
    {{-- Data Export --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-download text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('ui.data_export') }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.data_export_description') }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.data_export_info') }}
                </p>

                <form method="POST" action="{{ route('settings.export') }}" class="inline">
                    @csrf
                    <x-button type="submit" variant="primary" accent="bg">
                        <i class="fas fa-download mr-2"></i>
                        {{ __('ui.export_data') }}
                    </x-button>
                </form>
            </div>
        </div>
    </div>

    {{-- Danger Zone --}}
    <div class="bg-white dark:bg-gray-900 border border-red-200 dark:border-red-800 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-red-200 dark:border-red-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-red-900 dark:text-red-100">{{ __('ui.danger_zone') }}</h2>
                    <p class="text-sm text-red-600 dark:text-red-400">{{ __('ui.danger_zone_description') }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            {{-- Delete Account --}}
            <div class="space-y-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('ui.delete_account') }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('ui.delete_account_description') }}
                    </p>
                </div>

                {{-- Delete Form --}}
                <form method="POST" action="{{ route('settings.account.destroy') }}"
                    onsubmit="return confirm('{{ __('ui.delete_account_confirm') }}')"
                    class="space-y-4">
                    @csrf
                    @method('DELETE')

                    {{-- Confirm Name --}}
                    <div>
                        <label for="confirm_name" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            {{ __('ui.confirm_name') }}
                        </label>
                        <input type="text" id="confirm_name" name="confirm_name" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors"
                            placeholder="{{ __('ui.confirm_name_placeholder') }}">
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('ui.confirm_name_help', ['name' => $user->display_name]) }}
                        </p>
                    </div>

                    {{-- Password Confirmation --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            {{ __('ui.password') }}
                        </label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors"
                            placeholder="{{ __('ui.password_placeholder') }}">
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.password_confirm_help') }}</p>
                    </div>

                    {{-- Delete Button --}}
                    <div class="pt-4 border-t border-red-200 dark:border-red-800">
                        <x-button type="submit" variant="danger" class="w-full sm:w-auto">
                            <i class="fas fa-trash mr-2"></i>
                            {{ __('ui.delete_account_button') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>