<?php

declare(strict_types=1);

namespace App\Livewire\Settings\Tabs;

use App\Services\Twitch\Api\StreamerApiService;
use App\Services\Twitch\Auth\TwitchTokenManager;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

use function __;
use function app;
use function ceil;
use function implode;
use function now;
use function view;

final class AccountTab extends Component
{
    public bool $canSync = true;

    public ?string $nextSyncTime = null;

    public ?string $lastSyncTime = null;

    public function mount(): void
    {
        $this->updateSyncStatus();
    }

    public function syncTwitchData(): void
    {
        try {
            $user = Auth::user();

            // Check if Twitch token is still valid
            if ($user->twitch_token_expires_at && $user->twitch_token_expires_at->isPast()) {
                $this->dispatch('notify', type: 'error', message: __('Your Twitch token has expired. Please reconnect your Twitch account.'));

                return;
            }

            // Rate limiting: Check if last sync was within 12 hours
            if ($user->last_twitch_sync_at && $user->last_twitch_sync_at->addHours(12)->isFuture()) {
                $remainingTime = now()->diffInHours($user->last_twitch_sync_at->addHours(12), false);
                $this->dispatch('notify', type: 'error', message: __('Twitch sync is rate limited. You can sync again in :hours hours.', ['hours' => $remainingTime]));
                $this->updateSyncStatus();

                return;
            }

            $streamerApi  = app(StreamerApiService::class);
            $tokenManager = app(TwitchTokenManager::class);

            // Twitch-Daten synchronisieren - verwende User Access Token für E-Mail-Adresse
            // Synchronize Twitch data - use User Access Token for email address
            $userAccessToken = $tokenManager->getValidAccessToken($user);
            $twitchUser = $streamerApi->getStreamer($user->twitch_id, $userAccessToken);

            if ($twitchUser) {
                $updatedFields = [];

                // Check what fields changed
                if ($twitchUser->login !== $user->twitch_login) {
                    $updatedFields[] = 'username';
                }
                if ($twitchUser->displayName !== $user->twitch_display_name) {
                    $updatedFields[] = 'display name';
                }
                if ($twitchUser->email && $twitchUser->email !== $user->twitch_email) {
                    $updatedFields[] = 'email';
                }

                $user->update([
                    'twitch_login'        => $twitchUser->login,
                    'twitch_display_name' => $twitchUser->displayName,
                    'twitch_email'        => $twitchUser->email ?: $user->twitch_email, // Keep existing email if not provided
                    'last_twitch_sync_at' => now(),
                ]);

                $this->updateSyncStatus();

                if (empty($updatedFields)) {
                    $message = __('Twitch data is already up to date.');
                    if (!$twitchUser->email) {
                        $message .= ' ' . __('Note: Email address could not be retrieved from Twitch.');
                    }
                    $this->dispatch('notify', type: 'info', message: $message);
                } else {
                    $this->dispatch('notify', type: 'success', message: __('Twitch data synchronized successfully. Updated: :fields', ['fields' => implode(', ', $updatedFields)]));
                }
            } else {
                $this->dispatch('notify', type: 'error', message: __('Could not retrieve data from Twitch. Please try again later.'));
            }
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error synchronizing Twitch data: :error', ['error' => $e->getMessage()]));
        }
    }

    public function render()
    {
        return view('livewire.settings.tabs.account-tab', [
            'user' => Auth::user(),
        ]);
    }

    private function updateSyncStatus(): void
    {
        $user = Auth::user();

        if ($user->last_twitch_sync_at) {
            $this->lastSyncTime = $user->last_twitch_sync_at->diffForHumans();

            $nextSyncAt    = $user->last_twitch_sync_at->addHours(12);
            $this->canSync = $nextSyncAt->isPast();

            if (! $this->canSync) {
                $remaining = now()->diffInMinutes($nextSyncAt, false);
                if ($remaining > 60) {
                    $hours              = ceil($remaining / 60);
                    $this->nextSyncTime = __('in :hours hours', ['hours' => $hours]);
                } else {
                    $this->nextSyncTime = __('in :minutes minutes', ['minutes' => $remaining]);
                }
            }
        } else {
            $this->canSync      = true;
            $this->lastSyncTime = null;
            $this->nextSyncTime = null;
        }
    }
}
