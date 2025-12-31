<x-layouts.app title="Submit a Clip">
    <div class="min-h-screen bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">
                    <i class="fas fa-video text-blue-400 mr-3"></i>
                    Submit a Clip
                </h1>
                <p class="text-lg text-gray-300">
                    Share your favorite Twitch clips with the community
                </p>
            </div>

            <!-- Main Content -->
            <div class="bg-gray-800 rounded-lg shadow-xl border border-gray-700 overflow-hidden">
                <div class="p-6 lg:p-8">
                    @livewire('clips.submit-clip')
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 text-center">
                    <i class="fas fa-shield-alt text-green-400 text-2xl mb-3"></i>
                    <h3 class="text-lg font-medium text-white mb-2">Secure</h3>
                    <p class="text-sm text-gray-400">All clips are processed securely with privacy in mind.</p>
                </div>
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 text-center">
                    <i class="fas fa-rocket text-blue-400 text-2xl mb-3"></i>
                    <h3 class="text-lg font-medium text-white mb-2">Fast</h3>
                    <p class="text-sm text-gray-400">Quick submission process with background processing.</p>
                </div>
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 text-center">
                    <i class="fas fa-users text-purple-400 text-2xl mb-3"></i>
                    <h3 class="text-lg font-medium text-white mb-2">Community</h3>
                    <p class="text-sm text-gray-400">Share clips with fellow Twitch enthusiasts.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>