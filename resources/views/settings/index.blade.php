{{-- User Settings Page with Tabs --}}
<x-layouts.app :title="__('ui.settings')" :header="__('ui.settings')" :subheader="__('ui.settings_description')">

    <div class="max-w-7xl mx-auto">
        <div class="lg:grid lg:grid-cols-12 lg:gap-x-8">
            {{-- Sidebar Navigation --}}
            <aside class="lg:col-span-3">
                <nav class="space-y-1" aria-label="Sidebar">
                    <a href="{{ route('settings.index', ['tab' => 'profile']) }}"
                       class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ $activeTab === 'profile' ? 'bg-indigo-50 border-r-2 border-indigo-500 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200' }}">
                        <i class="fas fa-user mr-3 text-lg {{ $activeTab === 'profile' ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        <span class="truncate">{{ __('ui.profile') }}</span>
                    </a>

                    <a href="{{ route('settings.index', ['tab' => 'roles']) }}"
                       class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ $activeTab === 'roles' ? 'bg-indigo-50 border-r-2 border-indigo-500 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200' }}">
                        <i class="fas fa-user-tag mr-3 text-lg {{ $activeTab === 'roles' ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        <span class="truncate">{{ __('ui.roles') }}</span>
                    </a>

                    <a href="{{ route('settings.index', ['tab' => 'preferences']) }}"
                       class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ $activeTab === 'preferences' ? 'bg-indigo-50 border-r-2 border-indigo-500 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200' }}">
                        <i class="fas fa-palette mr-3 text-lg {{ $activeTab === 'preferences' ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        <span class="truncate">{{ __('ui.preferences') }}</span>
                    </a>

                    <a href="{{ route('settings.index', ['tab' => 'avatar']) }}"
                       class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ $activeTab === 'avatar' ? 'bg-indigo-50 border-r-2 border-indigo-500 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200' }}">
                        <i class="fas fa-camera mr-3 text-lg {{ $activeTab === 'avatar' ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        <span class="truncate">{{ __('ui.avatar') }}</span>
                    </a>

                    <a href="{{ route('settings.index', ['tab' => 'account']) }}"
                       class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ $activeTab === 'account' ? 'bg-red-50 border-r-2 border-red-500 text-red-700 dark:bg-red-900/50 dark:text-red-300' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200' }}">
                        <i class="fas fa-shield-alt mr-3 text-lg {{ $activeTab === 'account' ? 'text-red-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        <span class="truncate">{{ __('ui.account') }}</span>
                    </a>
                </nav>
            </aside>

            {{-- Main Content --}}
            <main class="lg:col-span-9">
                {{-- Mobile Tabs --}}
                <div class="lg:hidden mb-8">
                    <nav class="flex space-x-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-lg" aria-label="Tabs">
                        @foreach(['profile', 'roles', 'preferences', 'avatar', 'account'] as $tab)
                            <a href="{{ route('settings.index', ['tab' => $tab]) }}"
                               class="flex-1 py-2 px-3 text-sm font-medium text-center rounded-md transition-all duration-200 {{ $activeTab === $tab ? 'bg-white shadow-sm text-indigo-700 dark:bg-gray-700 dark:text-indigo-300' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                                <i class="fas fa-{{ $tab === 'profile' ? 'user' : ($tab === 'roles' ? 'user-tag' : ($tab === 'preferences' ? 'palette' : ($tab === 'avatar' ? 'camera' : 'shield-alt'))) }} mr-1"></i>
                                {{ __('ui.' . $tab) }}
                            </a>
                        @endforeach
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
            </main>
        </div>
    </div>

</x-layouts.app>