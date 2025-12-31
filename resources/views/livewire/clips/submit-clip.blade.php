<div class="max-w-2xl mx-auto bg-gray-900 text-white rounded-lg shadow-md p-6 border border-gray-700">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white mb-2 flex items-center">
            <i class="fas fa-video mr-3 text-blue-400"></i>
            {{ __('clips.ui_title') }}
        </h2>
        <p class="text-gray-300">{{ __('clips.ui_description') }}</p>
    </div>

    <form wire:submit="submit" class="space-y-4">
        <!-- Clip ID Input -->
        <div>
            <label for="twitchClipId" class="block text-sm font-medium text-gray-200 mb-1 flex items-center">
                <i class="fas fa-link mr-2 text-gray-400"></i>
                {{ __('clips.clip_id_label') }}
            </label>
            <div class="relative">
                <input
                    type="text"
                    id="twitchClipId"
                    wire:model="twitchClipId"
                    placeholder="{{ __('clips.clip_id_placeholder') }}"
                    class="w-full px-4 py-3 pl-10 border border-gray-600 rounded-lg bg-gray-800 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('twitchClipId') border-red-500 @enderror"
                    autocomplete="off"
                >
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            @error('twitchClipId')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-400">
                {{ __('clips.clip_id_help', ['example' => 'PluckyInventiveCarrotPastaThat']) }}
            </p>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between pt-4">
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-not-allowed"
                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <i wire:loading class="fas fa-spinner fa-spin mr-2"></i>
                <i wire:loading.remove class="fas fa-paper-plane mr-2"></i>
                <span wire:loading.remove>{{ __('clips.submit_button') }}</span>
                <span wire:loading>{{ __('clips.submitting_button') }}</span>
            </button>

            <div class="text-sm text-gray-400">
                <span class="inline-flex items-center">
                    <i class="fas fa-shield-alt mr-1 text-green-400"></i>
                    {{ __('clips.secure_private') }}
                </span>
            </div>
        </div>
    </form>

    <!-- Success Message -->
    @if($successMessage)
        <div class="mt-6 p-4 bg-green-900 border border-green-700 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-200">{{ $successMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Message -->
    @if($errorMessage)
        <div class="mt-6 p-4 bg-red-900 border border-red-700 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-200">{{ $errorMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Help Section -->
    <div class="mt-8 border-t border-gray-600 pt-6">
        <h3 class="text-lg font-medium text-gray-200 mb-3 flex items-center">
            <i class="fas fa-question-circle mr-2 text-blue-400"></i>
            {{ __('clips.help_title') }}
        </h3>
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-600">
            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-300">
                <li>{{ __('clips.help_step_1', ['example_url' => 'https://clips.twitch.tv/PluckyInventiveCarrotPastaThat']) }}</li>
                <li>{{ __('clips.help_step_2', ['example_id' => 'PluckyInventiveCarrotPastaThat']) }}</li>
                <li>{{ __('clips.help_step_3') }}</li>
            </ol>
        </div>
    </div>
</div>