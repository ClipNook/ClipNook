<x-layouts.app :title="__('ui.submit_clip')" :header="__('ui.submit_clip')" :subheader="__('ui.submit_clip_description')">
        @auth
            <livewire:clips.submit-clip />
        @else
            <p>Please <x-button variant="primary" size="sm" href="{{ route('login') }}" accent="bg">{{ __('ui.auth.sign_in_with_twitch') }}</x-button> to submit clips.</p>
        @endauth
</x-layouts.app>