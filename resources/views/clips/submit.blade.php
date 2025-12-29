<x-layouts.app :title="__('ui.submit_clip')" :header="__('ui.submit_clip')" :subheader="__('ui.submit_clip_description')">
        @auth
            <livewire:clips.submit-clip />
        @else
            <p>Please <a href="{{ route('login') }}" class="text-indigo-600">log in</a> to submit clips.</p>
        @endauth
</x-layouts.app>