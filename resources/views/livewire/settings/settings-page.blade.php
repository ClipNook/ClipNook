<div class="max-w-7xl mx-auto space-y-8">
    <!-- Tab Navigation -->
    <div class="bg-zinc-900 my-6 border border-zinc-800 rounded-xl overflow-hidden shadow-2xl">
        <div class="border-b border-zinc-800">
            <nav class="flex flex-wrap gap-3 p-2" aria-label="Tabs">
                <button
                    wire:click="setActiveTab('account')"
                    @class([
                        'flex items-center gap-3 px-5 py-3 rounded-lg text-sm font-medium transition-all duration-200',
                        'bg-(--color-accent-500) text-white shadow-lg transform scale-105' => $activeTab === 'account',
                        'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-200 hover:scale-105' => $activeTab !== 'account',
                    ])
                >
                    <i class="fa-solid fa-user text-lg"></i>
                    <span class="hidden sm:inline">{{ __('Account') }}</span>
                </button>

                <button
                    wire:click="setActiveTab('profile')"
                    @class([
                        'flex items-center gap-3 px-5 py-3 rounded-lg text-sm font-medium transition-all duration-200',
                        'bg-(--color-accent-500) text-white shadow-lg transform scale-105' => $activeTab === 'profile',
                        'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-200 hover:scale-105' => $activeTab !== 'profile',
                    ])
                >
                    <i class="fa-solid fa-id-card text-lg"></i>
                    <span class="hidden sm:inline">{{ __('Profile') }}</span>
                </button>

                <button
                    wire:click="setActiveTab('streamer')"
                    @class([
                        'flex items-center gap-3 px-5 py-3 rounded-lg text-sm font-medium transition-all duration-200',
                        'bg-(--color-accent-500) text-white shadow-lg transform scale-105' => $activeTab === 'streamer',
                        'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-200 hover:scale-105' => $activeTab !== 'streamer',
                    ])
                >
                    <i class="fa-solid fa-video text-lg"></i>
                    <span class="hidden sm:inline">{{ __('Streamer') }}</span>
                </button>

                <button
                    wire:click="setActiveTab('privacy')"
                    @class([
                        'flex items-center gap-3 px-5 py-3 rounded-lg text-sm font-medium transition-all duration-200',
                        'bg-(--color-accent-500) text-white shadow-lg transform scale-105' => $activeTab === 'privacy',
                        'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-200 hover:scale-105' => $activeTab !== 'privacy',
                    ])
                >
                    <i class="fa-solid fa-shield text-lg"></i>
                    <span class="hidden sm:inline">{{ __('Privacy') }}</span>
                </button>

                <button
                    wire:click="setActiveTab('sessions')"
                    @class([
                        'flex items-center gap-3 px-5 py-3 rounded-lg text-sm font-medium transition-all duration-200',
                        'bg-(--color-accent-500) text-white shadow-lg transform scale-105' => $activeTab === 'sessions',
                        'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-200 hover:scale-105' => $activeTab !== 'sessions',
                    ])
                >
                    <i class="fa-solid fa-desktop text-lg"></i>
                    <span class="hidden sm:inline">{{ __('Sessions') }}</span>
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="bg-gradient-to-br from-zinc-800/50 to-zinc-900/50 rounded-lg p-8 backdrop-blur-sm">
            @if($activeTab === 'account')
                <livewire:settings.tabs.account-tab />
            @elseif($activeTab === 'profile')
                <livewire:settings.tabs.profile-tab />
            @elseif($activeTab === 'streamer')
                <livewire:settings.tabs.streamer-tab />
            @elseif($activeTab === 'privacy')
                <livewire:settings.tabs.privacy-tab />
            @elseif($activeTab === 'sessions')
                <livewire:settings.tabs.sessions-tab />
            @endif
        </div>
    </div>
</div>
