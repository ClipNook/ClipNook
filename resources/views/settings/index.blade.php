{{-- User Settings Page with Tabs --}}
<x-layouts.app :title="__('ui.settings')" :header="__('ui.settings')" :subheader="__('ui.settings_description')">

    <div class="max-w-6xl mx-auto">
        {{-- Settings Tabs --}}
        <div class="mb-8">
            <nav class="flex space-x-8" aria-label="Tabs">
                <a href="{{ route('settings.index', ['tab' => 'profile']) }}"
                   class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'profile' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    <i class="fas fa-user mr-2"></i>
                    {{ __('ui.profile') }}
                </a>

                <a href="{{ route('settings.index', ['tab' => 'roles']) }}"
                   class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'roles' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    <i class="fas fa-user-tag mr-2"></i>
                    {{ __('ui.roles') }}
                </a>

                <a href="{{ route('settings.index', ['tab' => 'preferences']) }}"
                   class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'preferences' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    <i class="fas fa-palette mr-2"></i>
                    {{ __('ui.preferences') }}
                </a>

                <a href="{{ route('settings.index', ['tab' => 'avatar']) }}"
                   class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'avatar' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    <i class="fas fa-camera mr-2"></i>
                    {{ __('ui.avatar') }}
                </a>

                <a href="{{ route('settings.index', ['tab' => 'account']) }}"
                   class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'account' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    <i class="fas fa-shield-alt mr-2"></i>
                    {{ __('ui.account') }}
                </a>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="space-y-6">
            @if($activeTab === 'profile')
                @include('settings.tabs.profile')
            @elseif($activeTab === 'roles')
                @include('settings.tabs.roles')
            @elseif($activeTab === 'preferences')
                @include('settings.tabs.preferences')
            @elseif($activeTab === 'avatar')
                @include('settings.tabs.avatar')
            @elseif($activeTab === 'account')
                @include('settings.tabs.account')
            @endif
        </div>
    </div>

</x-layouts.app>