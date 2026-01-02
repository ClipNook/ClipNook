<?php

declare(strict_types=1);

namespace App\Livewire\Settings\Tabs;

use App\Services\Twitch\Api\StreamerApiService;
use App\Services\Twitch\Auth\TwitchTokenManager;
use Exception;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Livewire\Component;
use Livewire\WithFileUploads;

use function __;
use function app;
use function logger;
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
        try {
            $user = Auth::user();

            $user->uploadCustomAvatar($this->avatar);
            $this->avatar = null;
            $this->dispatch('notify', type: 'success', message: __('Avatar successfully uploaded.'));
        } catch (InvalidArgumentException $e) {
            $this->dispatch('notify', type: 'error', message: __('Avatar upload failed: :error', ['error' => $e->getMessage()]));
        } catch (Exception $e) {
            logger()->error('Avatar upload error', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            $this->dispatch('notify', type: 'error', message: __('An unexpected error occurred while uploading the avatar.'));
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
                $user->syncTwitchAvatar($twitchUser->profileImageUrl);
                $this->dispatch('notify', type: 'success', message: __('Twitch avatar synchronized.'));
            } else {
                $this->dispatch('notify', type: 'error', message: __('Could not retrieve Twitch user data.'));
            }
        } catch (InvalidArgumentException $e) {
            $this->dispatch('notify', type: 'error', message: __('Avatar sync failed: :error', ['error' => $e->getMessage()]));
        } catch (Exception $e) {
            logger()->error('Twitch avatar sync error', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);
            $this->dispatch('notify', type: 'error', message: __('An unexpected error occurred while syncing the avatar.'));
        }
    }

    public function deleteCustomAvatar(): void
    {
        try {
            $user = Auth::user();

            if ($user->deleteAvatar()) {
                $this->dispatch('notify', type: 'success', message: __('Avatar deleted.'));
            } else {
                $this->dispatch('notify', type: 'info', message: __('No avatar to delete.'));
            }
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error deleting avatar: :error', ['error' => $e->getMessage()]));
        }
    }

    public function resetAvatar(): void
    {
        try {
            $user = Auth::user();

            if ($user->resetAvatar()) {
                $this->dispatch('notify', type: 'success', message: __('Avatar reset to default.'));
            } else {
                $this->dispatch('notify', type: 'info', message: __('No avatar to reset.'));
            }
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error resetting avatar: :error', ['error' => $e->getMessage()]));
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
