<x-layouts.app title="{{ __('games.list_page_title') }}">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-gray-950">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-white mb-2">
                    <i class="fas fa-gamepad mr-2"></i>
                    {{ __('games.list_page_title') }}
                </h1>
                <p class="text-gray-400">{{ __('games.list_page_subtitle') }}</p>
            </div>

            <!-- Search & Filter -->
            <div class="bg-gray-900 rounded-md border border-gray-800 p-4 mb-6">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input
                            type="text"
                            placeholder="Search games..."
                            class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-md text-white placeholder-gray-500 focus:border-purple-500 focus:outline-none transition-colors"
                        >
                    </div>
                    <select class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-md text-white focus:border-purple-500 focus:outline-none transition-colors">
                        <option>Most Clips</option>
                        <option>Alphabetical</option>
                        <option>Recent</option>
                    </select>
                </div>
            </div>

            <!-- Games Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                <!-- Game Card -->
                <a href="#" class="group bg-gray-900 rounded-md border border-gray-800 hover:border-gray-600 transition-colors overflow-hidden">
                    <div class="aspect-[3/4] bg-gray-800 relative">
                        <img src="" alt="Game" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
                    </div>
                    <div class="p-3">
                        <h3 class="font-medium text-white text-sm mb-1 line-clamp-2">Game Name</h3>
                        <p class="text-xs text-gray-500">123 clips</p>
                    </div>
                </a>

                <a href="#" class="group bg-gray-900 rounded-md border border-gray-800 hover:border-gray-600 transition-colors overflow-hidden">
                    <div class="aspect-[3/4] bg-gray-800 relative">
                        <img src="" alt="Game" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
                    </div>
                    <div class="p-3">
                        <h3 class="font-medium text-white text-sm mb-1 line-clamp-2">Another Game</h3>
                        <p class="text-xs text-gray-500">456 clips</p>
                    </div>
                </a>

                <a href="#" class="group bg-gray-900 rounded-md border border-gray-800 hover:border-gray-600 transition-colors overflow-hidden">
                    <div class="aspect-[3/4] bg-gray-800 relative">
                        <img src="" alt="Game" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
                    </div>
                    <div class="p-3">
                        <h3 class="font-medium text-white text-sm mb-1 line-clamp-2">Cool Game Title</h3>
                        <p class="text-xs text-gray-500">789 clips</p>
                    </div>
                </a>
            </div>

            <!-- Empty State -->
            <div class="hidden text-center py-12">
                <i class="fas fa-gamepad text-gray-600 text-6xl mb-4"></i>
                <h3 class="text-xl font-medium text-gray-400 mb-2">No games found</h3>
                <p class="text-gray-500">Try adjusting your search</p>
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md transition-colors disabled:opacity-50" disabled>
                        Previous
                    </button>
                    <button class="px-4 py-2 bg-purple-600 text-white rounded-md">1</button>
                    <button class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md transition-colors">2</button>
                    <button class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md transition-colors">3</button>
                    <button class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md transition-colors">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
