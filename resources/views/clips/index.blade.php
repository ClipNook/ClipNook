<x-layouts.app title="Clip Library">
    <div class="min-h-screen bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-video text-blue-400 mr-3"></i>
                    Clip Library
                </h1>
                <p class="text-xl text-gray-300">
                    Discover and explore submitted clips
                </p>
            </div>

            <!-- Main Content -->
            <div class="bg-gray-800 rounded-lg shadow-xl border border-gray-700 overflow-hidden">
                <div class="p-6 lg:p-8">
                    @livewire('clips.clip-list')
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>