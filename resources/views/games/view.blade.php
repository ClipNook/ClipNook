<x-layouts.app title="{{ __('games.view_page_title') }}">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-gray-950">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Game Header -->
            <div class="bg-gray-900 rounded-md border border-gray-800 overflow-hidden">
                <div class="relative h-48 bg-gradient-to-b from-gray-800 to-gray-900">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-32 h-44 bg-gray-800 rounded-md border border-gray-700">
                            <img src="" alt="Game Box Art" class="w-full h-full object-cover rounded-md">
                        </div>
                    </div>
                </div>
                <div class="p-6 pt-20">
                    <h1 class="text-3xl font-bold text-white mb-2">Game Name</h1>
                    <p class="text-gray-400 mb-4">Game description goes here...</p>
                    <div class="flex flex-wrap gap-3 text-sm">
                        <span class="px-3 py-1 bg-gray-800 text-gray-300 rounded-md">
                            <i class="fas fa-video mr-1"></i>
                            456 clips
                        </span>
                        <span class="px-3 py-1 bg-gray-800 text-gray-300 rounded-md">
                            <i class="fas fa-users mr-1"></i>
                            123 streamers
                        </span>
                    </div>
                </div>
            </div>

            <!-- Clips Section -->
            <div class="bg-gray-900 rounded-md border border-gray-800 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">
                        <i class="fas fa-film mr-2"></i>
                        Clips
                    </h2>
                    <select class="px-3 py-1.5 bg-gray-800 border border-gray-700 rounded-md text-white text-sm focus:border-purple-500 focus:outline-none transition-colors">
                        <option>Most Recent</option>
                        <option>Most Viewed</option>
                        <option>Top Rated</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <!-- Clip cards will go here -->
                    <p class="text-gray-500 text-center py-8 col-span-full">No clips found for this game</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
