<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Models\BroadcasterClipPermission;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

use function __;
use function collect;
use function strlen;
use function view;

/**
 * User streamer permissions settings component.
 */
final class StreamerSettings extends Component
{
    public User $user;

    public Collection $permissions;

    public string $newUserSearch = '';

    public SupportCollection $userSuggestions;

    public array $selectedPermissions = [
        'can_submit_clips'   => false,
        'can_edit_clips'     => false,
        'can_delete_clips'   => false,
        'can_moderate_clips' => false,
    ];

    public ?int $editingPermissionId = null;

    public array $editingPermissions = [
        'can_submit_clips'   => false,
        'can_edit_clips'     => false,
        'can_delete_clips'   => false,
        'can_moderate_clips' => false,
    ];

    public function mount(): void
    {
        $this->user            = Auth::user();
        $this->userSuggestions = collect();
        $this->loadPermissions();
    }

    public function loadPermissions(): void
    {
        $this->permissions = BroadcasterClipPermission::where('broadcaster_id', $this->user->id)
            ->with('user')
            ->get();
    }

    public function getAllUsersSubmissionEnabled(): bool
    {
        if (! $this->user->is_streamer || ! $this->user->broadcasterSettings) {
            return false;
        }

        return $this->user->broadcasterSettings->clip_submission_permission === 'everyone';
    }

    public function toggleAllUsersSubmission(): void
    {
        if (! $this->user->is_streamer) {
            $this->dispatch('notify', type: 'error', message: __('settings.must_be_streamer'));

            return;
        }

        $newValue = $this->getAllUsersSubmissionEnabled() ? 'none' : 'everyone';

        if (! $this->user->broadcasterSettings) {
            \App\Models\BroadcasterSettings::create([
                'broadcaster_id'             => $this->user->id,
                'clip_submission_permission' => $newValue,
            ]);
        } else {
            $this->user->broadcasterSettings->update([
                'clip_submission_permission' => $newValue,
            ]);
        }

        $this->user->refresh();
        $message = $newValue === 'everyone'
            ? __('settings.all_users_submission_enabled')
            : __('settings.all_users_submission_disabled');
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function updatedNewUserSearch(): void
    {
        if (strlen($this->newUserSearch) >= 2) {
            $this->userSuggestions = User::where(function ($query): void {
                $query->where('twitch_login', 'like', '%'.$this->newUserSearch.'%')
                    ->orWhere('twitch_display_name', 'like', '%'.$this->newUserSearch.'%');
            })
                ->where('id', '!=', $this->user->id) // Exclude current user
                ->limit(10)
                ->get();
        } else {
            $this->userSuggestions = collect();
        }
    }

    public function selectUser(int $userId): void
    {
        $selectedUser = $this->userSuggestions->find($userId);
        if ($selectedUser) {
            $this->newUserSearch   = $selectedUser->twitch_display_name ?? $selectedUser->twitch_login;
            $this->userSuggestions = collect();
        }
    }

    public function addPermission(): void
    {
        $this->validate([
            'newUserSearch' => 'required|string|min:2',
        ]);

        $targetUser = User::where(function ($query): void {
            $query->where('twitch_login', $this->newUserSearch)
                ->orWhere('twitch_display_name', $this->newUserSearch);
        })->first();

        if (! $targetUser) {
            $this->dispatch('notify', type: 'error', message: __('settings.user_not_found'));

            return;
        }

        // Check if permission already exists
        if (BroadcasterClipPermission::where('broadcaster_id', $this->user->id)
            ->where('user_id', $targetUser->id)
            ->exists()) {
            $this->dispatch('notify', type: 'error', message: __('settings.user_already_has_permissions'));

            return;
        }

        BroadcasterClipPermission::create([
            'broadcaster_id'      => $this->user->id,
            'user_id'             => $targetUser->id,
            'can_submit_clips'    => $this->selectedPermissions['can_submit_clips'],
            'can_edit_clips'      => $this->selectedPermissions['can_edit_clips'],
            'can_delete_clips'    => $this->selectedPermissions['can_delete_clips'],
            'can_moderate_clips'  => $this->selectedPermissions['can_moderate_clips'],
        ]);

        $this->newUserSearch       = '';
        $this->userSuggestions     = collect();
        $this->resetSelectedPermissions();

        $this->loadPermissions();
        $this->dispatch('notify', type: 'success', message: __('settings.permission_granted_successfully'));
    }

    public function startEditing(int $permissionId): void
    {
        $permission = $this->permissions->find($permissionId);
        if ($permission) {
            $this->editingPermissionId = $permissionId;
            $this->editingPermissions  = [
                'can_submit_clips'   => $permission->can_submit_clips,
                'can_edit_clips'     => $permission->can_edit_clips,
                'can_delete_clips'   => $permission->can_delete_clips,
                'can_moderate_clips' => $permission->can_moderate_clips,
            ];
        }
    }

    public function updatePermission(): void
    {
        $permission = BroadcasterClipPermission::find($this->editingPermissionId);
        if ($permission && $permission->broadcaster_id === $this->user->id) {
            $permission->update($this->editingPermissions);
            $this->loadPermissions();
            $this->cancelEditing();
            $this->dispatch('notify', type: 'success', message: __('settings.permission_updated_successfully'));
        }
    }

    public function cancelEditing(): void
    {
        $this->editingPermissionId = null;
        $this->resetEditingPermissions();
    }

    public function removePermission(int $permissionId): void
    {
        $permission = BroadcasterClipPermission::find($permissionId);
        if ($permission && $permission->broadcaster_id === $this->user->id) {
            $permission->delete();
            $this->loadPermissions();
            $this->dispatch('notify', type: 'success', message: __('settings.permission_removed_successfully'));
        }
    }

    public function grantAllPermissions(int $permissionId): void
    {
        $permission = BroadcasterClipPermission::find($permissionId);
        if ($permission && $permission->broadcaster_id === $this->user->id) {
            $permission->grantAllPermissions();
            $this->loadPermissions();
            $this->dispatch('notify', type: 'success', message: __('settings.all_permissions_granted'));
        }
    }

    public function revokeAllPermissions(int $permissionId): void
    {
        $permission = BroadcasterClipPermission::find($permissionId);
        if ($permission && $permission->broadcaster_id === $this->user->id) {
            $permission->revokeAllPermissions();
            $this->loadPermissions();
            $this->dispatch('notify', type: 'success', message: __('settings.all_permissions_revoked'));
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.settings.streamer-settings');
    }

    private function resetSelectedPermissions(): void
    {
        $this->selectedPermissions = [
            'can_submit_clips'   => false,
            'can_edit_clips'     => false,
            'can_delete_clips'   => false,
            'can_moderate_clips' => false,
        ];
    }

    private function resetEditingPermissions(): void
    {
        $this->editingPermissions = [
            'can_submit_clips'   => false,
            'can_edit_clips'     => false,
            'can_delete_clips'   => false,
            'can_moderate_clips' => false,
        ];
    }
}
