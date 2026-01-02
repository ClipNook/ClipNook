<?php

declare(strict_types=1);

namespace App\Livewire\Settings\Tabs;

use App\Models\BroadcasterSettings;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

use function __;
use function view;

final class StreamerTab extends Component
{
    public bool $isStreamer = false;

    public string $clipSubmissionPermission = 'everyone';

    public function mount(): void
    {
        $user             = Auth::user();
        $this->isStreamer = $user->is_streamer;

        if ($user->is_streamer && $user->broadcasterSettings) {
            $this->clipSubmissionPermission = $user->broadcasterSettings->getClipSubmissionPermission();
        }
    }

    public function toggleStreamerStatus(): void
    {
        try {
            $user      = Auth::user();
            $newStatus = ! $this->isStreamer;

            $user->update([
                'is_streamer' => $newStatus,
            ]);

            // BroadcasterSettings erstellen, falls aktiviert
            if ($newStatus && ! $user->broadcasterSettings) {
                BroadcasterSettings::create([
                    'broadcaster_id'             => $user->id,
                    'clip_submission_permission' => 'everyone',
                ]);
            }

            $this->isStreamer = $newStatus;
            $this->dispatch('notify', type: 'success', message: __('Streamer status updated.'));
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error updating streamer status: :error', ['error' => $e->getMessage()]));
        }
    }

    public function updateClipPermission(): void
    {
        $this->validate([
            'clipSubmissionPermission' => 'required|in:everyone,none',
        ]);

        try {
            $user = Auth::user();

            if (! $user->is_streamer) {
                $this->dispatch('notify', type: 'error', message: __('You must be a streamer to update clip permissions.'));

                return;
            }

            if (! $user->broadcasterSettings) {
                BroadcasterSettings::create([
                    'broadcaster_id'             => $user->id,
                    'clip_submission_permission' => $this->clipSubmissionPermission,
                ]);
            } else {
                $user->broadcasterSettings->setClipSubmissionPermission($this->clipSubmissionPermission);
            }

            $this->dispatch('notify', type: 'success', message: __('Clip submission permission updated.'));
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error updating permission: :error', ['error' => $e->getMessage()]));
        }
    }

    public function render()
    {
        return view('livewire.settings.tabs.streamer-tab', [
            'user' => Auth::user(),
        ]);
    }
}
