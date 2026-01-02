<?php

declare(strict_types=1);

namespace App\Livewire\Settings\Tabs;

use App\Services\Twitch\Api\StreamerApiService;
use App\Services\Twitch\Auth\TwitchTokenManager;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithFileUploads;

use function __;
use function app;
use function view;

final class ProfileTab extends Component
{
    use WithFileUploads;

    public $avatar;

    public string $description = '';

    public function mount(): void
    {
        $user              = Auth::user();
        $this->description = $user->description ?? '';
    }

    public function uploadAvatar(): void
    {
        $this->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        try {
            $user = Auth::user();

            // Alten Custom-Avatar löschen
            $user->deleteCustomAvatar();

            // Neuen Avatar speichern
            $path = $this->avatar->store('avatars', 'public');

            $user->update([
                'avatar_source' => $path,
            ]);

            $user->setAvatarSource('custom');

            $this->avatar = null;
            $this->dispatch('notify', type: 'success', message: __('Avatar successfully uploaded.'));
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error uploading avatar: :error', ['error' => $e->getMessage()]));
        }
    }

    public function syncTwitchAvatar(): void
    {
        try {
            $user         = Auth::user();
            $streamerApi  = app(StreamerApiService::class);
            $tokenManager = app(TwitchTokenManager::class);

            $twitchUser = $streamerApi->getStreamer($user->twitch_id, $tokenManager->getAppAccessToken());

            if ($twitchUser && $twitchUser->profileImageUrl) {
                // Alten Twitch-Avatar löschen
                $user->deleteTwitchAvatar();

                // Custom-Avatar löschen
                $user->deleteCustomAvatar();

                // Twitch-Avatar herunterladen und lokal speichern
                $response = Http::get($twitchUser->profileImageUrl);
                if ($response->successful()) {
                    $path = $user->getTwitchAvatarStoragePath();

                    Storage::disk('public')->put($path, $response->body());

                    $this->dispatch('notify', type: 'success', message: __('Twitch avatar synchronized.'));
                } else {
                    $this->dispatch('notify', type: 'error', message: __('Could not download Twitch avatar.'));
                }
            } else {
                $this->dispatch('notify', type: 'error', message: __('Could not retrieve Twitch user data.'));
            }
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error syncing Twitch avatar: :error', ['error' => $e->getMessage()]));
        }
    }

    public function deleteCustomAvatar(): void
    {
        try {
            $user = Auth::user();

            if ($user->deleteCustomAvatar()) {
                $this->dispatch('notify', type: 'success', message: __('Custom avatar deleted.'));
            }
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error deleting avatar: :error', ['error' => $e->getMessage()]));
        }
    }

    public function updateDescription(): void
    {
        $this->validate([
            'description' => 'nullable|string|max:500',
        ]);

        try {
            Auth::user()->update([
                'description' => $this->description,
            ]);

            $this->dispatch('notify', type: 'success', message: __('Description updated.'));
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error updating description: :error', ['error' => $e->getMessage()]));
        }
    }

    public function render()
    {
        return view('livewire.settings.tabs.profile-tab', [
            'user' => Auth::user(),
        ]);
    }
}
