<?php

namespace App\Http\Controllers;

use App\Services\Twitch\AvatarService;
use App\Services\Twitch\Contracts\OAuthInterface;
use App\Services\Twitch\TokenRefreshService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class UserSettingsController extends Controller
{
    public function __construct(
        private readonly OAuthInterface $oauth,
        private readonly TokenRefreshService $tokenRefresh,
        private readonly AvatarService $avatarService
    ) {}

    /**
     * Display the user settings page.
     */
    public function edit(): View
    {
        return view('settings.edit');
    }

    /**
     * Update user settings.
     */
    public function update(\App\Http\Requests\UpdateSettingsRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Prefer explicit actionType when provided
        $action = $request->input('actionType');

        if ($action === 'remove_avatar' || $request->boolean('remove_avatar')) {
            return $this->removeAvatar($user);
        }

        if ($action === 'restore_avatar' || $request->boolean('restore_avatar')) {
            return $this->restoreAvatar($user);
        }

        // Role/profile updates
        if ($request->hasAny(['is_streamer', 'is_cutter', 'intro', 'available_for_jobs'])) {
            DB::beginTransaction();
            try {
                $user->is_viewer = true;

                $user->is_streamer        = $request->boolean('is_streamer');
                $user->is_cutter          = $request->boolean('is_cutter');
                $user->intro              = $request->input('intro') ?: null;
                $user->available_for_jobs = $request->boolean('available_for_jobs');

                $user->save();

                DB::commit();

                return redirect()
                    ->route('settings')
                    ->with('success', __('ui.settings_saved'));
            } catch (\Throwable $e) {
                DB::rollBack();
                // Log error without user-identifying details to avoid storing PII
                Log::error('Saving user roles failed', ['error' => $e->getMessage()]);

                return redirect()
                    ->route('settings')
                    ->with('error', __('ui.settings_save_failed'));
            }
        }

        return redirect()
            ->route('settings')
            ->with('info', __('ui.no_changes'));
    }

    /**
     * Delete the user account.
     */
    public function destroy(\App\Http\Requests\DeleteAccountRequest $request): RedirectResponse
    {
        $user = $request->user();

        try {
            $this->deleteUserCompletely($user);

            // Logout and session cleanup
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')
                ->with('success', __('ui.delete_success'));
        } catch (\Throwable $e) {
            DB::rollBack();
            // Avoid logging user-identifying information here
            Log::error('Account deletion failed', ['error' => $e->getMessage()]);

            return redirect()
                ->route('settings')
                ->with('error', __('ui.delete_failed'));
        }
    }

    /**
     * Remove user's avatar.
     */
    private function removeAvatar(\App\Models\User $user): RedirectResponse
    {
        try {
            // Mark avatar as disabled (user opted out) and delete any stored avatar
            $user->avatar_disabled = true;
            $user->save();

            $user->deleteAvatar();

            return redirect()
                ->route('settings')
                ->with('success', __('ui.settings_saved'));
        } catch (\Throwable $e) {
            // Keep logs minimal and avoid recording PII
            Log::error('Avatar removal failed', ['error' => $e->getMessage()]);

            return redirect()
                ->route('settings')
                ->with('error', __('ui.avatar_remove_failed'));
        }
    }

    /**
     * Restore avatar from Twitch.
     */
    private function restoreAvatar(\App\Models\User $user): RedirectResponse
    {
        if (empty($user->twitch_id)) {
            return redirect()
                ->route('settings')
                ->with('error', __('ui.settings_not_connected'));
        }

        $accessToken = $this->tokenRefresh->getValidToken($user);
        if (! $accessToken) {
            return redirect()
                ->route('settings')
                ->with('error', __('ui.settings_restore_failed'));
        }

        try {
            // Re-enable avatar saving
            $user->avatar_disabled = false;
            $user->save();

            // Restore avatar from Twitch and store it according to AvatarService logic
            $this->avatarService->restoreFromTwitch($user, $accessToken);

            // Reload from database to verify result
            $user->refresh();

            if (empty($user->twitch_avatar)) {
                // Not an error condition; record as debug to avoid noise and PII
                Log::debug('Avatar restore completed but no avatar was stored');

                return redirect()
                    ->route('settings')
                    ->with('info', __('ui.settings_restore_no_avatar'));
            }

            return redirect()
                ->route('settings')
                ->with('success', __('ui.settings_restore_success'));
        } catch (\App\Services\Twitch\Exceptions\ValidationException $e) {
            // Expected validation-like case (e.g. no avatar available on Twitch)
            Log::debug('Avatar restore validation: '.$e->getMessage());

            return redirect()
                ->route('settings')
                ->with('info', __('ui.settings_restore_no_avatar'));
        } catch (\Throwable $e) {
            // Keep error details but avoid PII in log context
            Log::error('Avatar restoration failed', ['error' => $e->getMessage()]);

            return redirect()
                ->route('settings')
                ->with('error', __('ui.settings_restore_failed'));
        }
    }

    /**
     * Attempt a complete deletion of the user record, with verification and fallback.
     * Throws on failure so calling transaction can roll back.
     */
    protected function deleteUserCompletely(\App\Models\User $user): void
    {
        try {
            $user->delete();
        } catch (\Throwable $e) {
            // Avoid storing user-identifying details in log context
            Log::error('Eloquent delete threw exception', ['error' => $e->getMessage()]);
            throw $e;
        }

        $after = DB::table('users')->where('id', $user->id)->count();
        if ($after > 0) {
            $afterDirect = DB::table('users')->where('id', $user->id)->count();
            if ($afterDirect > 0) {
                Log::error('Failed to delete user record via both Eloquent and direct DB delete');
                throw new \RuntimeException('Failed to delete user record via both Eloquent and direct DB delete');
            }
        }
    }
}
