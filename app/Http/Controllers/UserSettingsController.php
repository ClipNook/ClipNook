<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteAccountRequest;
use App\Http\Requests\ExportDataRequest;
use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateRolesRequest;
use App\Http\Requests\UploadAvatarRequest;
use App\Services\Twitch\AvatarService;
use App\Services\Twitch\Contracts\OAuthInterface;
use App\Services\Twitch\TokenRefreshService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserSettingsController extends Controller
{
    public function __construct(
        private readonly OAuthInterface $oauth,
        private readonly TokenRefreshService $tokenRefresh,
        private readonly AvatarService $avatarService
    ) {}

    /**
     * Display the settings page.
     */
    public function edit(): View
    {
        $user = Auth::user();
        return view('settings.edit', [
            'user'       => $user->load(['streamerProfile', 'cutterProfile']),
            'isStreamer' => $user->is_streamer,
            'isCutter'   => $user->is_cutter,
        ]);
    }

    /**
     * Update user profile (bio only).
     */
    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        return $this->updateUserFields($request, ['bio'], __('ui.profile_updated'), __('ui.profile_update_failed'));
    }

    /**
     * Update user preferences (e.g. accent color).
     */
    public function updatePreferences(\App\Http\Requests\UpdateSettingsRequest $request): RedirectResponse
    {
        return $this->updateUserFields($request, ['accent_color'], __('ui.accent_updated'), __('ui.preferences_update_failed'));
    }

    /**
     * Update user roles and professional settings.
     */
    public function updateRoles(UpdateRolesRequest $request): RedirectResponse
    {
        $user = $request->user();
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $user->update([
                'is_streamer'        => $validated['is_streamer'] ?? false,
                'is_cutter'          => $validated['is_cutter'] ?? false,
                'intro'              => $validated['intro'] ?? null,
                'available_for_jobs' => $validated['available_for_jobs'] ?? false,
                'allow_clip_sharing' => $validated['allow_clip_sharing'] ?? false,
            ]);
            if ($user->is_cutter) {
                $this->updateCutterProfile($user, $validated);
            } else {
                $user->cutterProfile()->delete();
            }
            if ($user->is_streamer) {
                $this->updateStreamerProfile($user, $validated);
            } else {
                $user->streamerProfile()->delete();
            }
            DB::commit();
            return redirect()->route('settings.edit')->with('success', __('ui.roles_updated'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Roles update failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->route('settings.edit')->with('error', __('ui.roles_update_failed'));
        }
    }

    /**
     * Helper for simple user field updates.
     */
    private function updateUserFields($request, array $fields, string $successMsg, string $errorMsg): RedirectResponse
    {
        $user = $request->user();
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $update = [];
            foreach ($fields as $field) {
                $update[$field] = $validated[$field] ?? null;
            }
            $user->update($update);
            DB::commit();
            return redirect()->route('settings.edit')->with('success', $successMsg);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('User update failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->route('settings.edit')->with('error', $errorMsg);
        }
    }

    /**
     * Change avatar status (remove/restore).
     */
    public function updateAvatar(UpdateAvatarRequest $request): RedirectResponse
    {
        $user = $request->user();
        return match ($request->validated('action')) {
            'remove'  => $this->removeAvatar($user),
            'restore' => $this->restoreAvatar($user),
            default   => redirect()->route('settings.edit')->with('error', __('ui.invalid_action')),
        };
    }

    /**
     * Upload a new custom avatar.
     */
    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        $user = $request->user();
        $file = $request->file('avatar');
        DB::beginTransaction();
        try {
            if ($user->custom_avatar_path) {
                Storage::disk('public')->delete($user->custom_avatar_path);
            }
            $path = $file->store('avatars/custom', 'public');
            $thumbnailPath = $this->createAvatarThumbnail($path);
            $user->update([
                'custom_avatar_path'           => $path,
                'custom_avatar_thumbnail_path' => $thumbnailPath,
                'avatar_source'                => 'custom',
                'avatar_disabled_at'           => null,
            ]);
            $user->twitch_avatar = null;
            $user->save();
            DB::commit();
            return response()->json([
                'success'       => true,
                'message'       => __('ui.avatar_upload_success'),
                'avatar_url'    => $user->avatar_url,
                'thumbnail_url' => $user->avatar_thumbnail_url,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Avatar upload failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => __('ui.avatar_upload_failed'),
            ], 500);
        }
    }

    /**
     * Export all user data as JSON.
     */
    public function exportData(ExportDataRequest $request): StreamedResponse
    {
        $user = $request->user();
        $filename = "user-data-{$user->id}-".now()->format('Y-m-d-H-i-s').'.json';
        $data = $this->compileUserData($user);
        return response()->streamDownload(fn () => print json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Delete the user account completely.
     */
    public function destroy(DeleteAccountRequest $request): RedirectResponse
    {
        $user = $request->user();
        DB::beginTransaction();
        try {
            $this->deleteUserCompletely($user);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            DB::commit();
            return redirect('/')->with('success', __('ui.delete_success'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Account deletion failed', ['error' => $e->getMessage()]);
            return redirect()->route('settings.edit')->with('error', __('ui.delete_failed'));
        }
    }

    /**
     * Set avatar as removed and delete custom files.
     */
    private function removeAvatar(\App\Models\User $user): RedirectResponse
    {
        try {
            $user->update([
                'avatar_disabled_at' => now(),
                'avatar_source'      => 'disabled',
            ]);
            if ($user->custom_avatar_path) {
                Storage::disk('public')->delete([
                    $user->custom_avatar_path,
                    $user->custom_avatar_thumbnail_path,
                ]);
                $user->update([
                    'custom_avatar_path'           => null,
                    'custom_avatar_thumbnail_path' => null,
                ]);
            }
            return redirect()->route('settings.edit')->with('success', __('ui.avatar_removed'));
        } catch (\Throwable $e) {
            Log::error('Avatar removal failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->route('settings.edit')->with('error', __('ui.avatar_remove_failed'));
        }
    }

    /**
     * Restore avatar from Twitch.
     */
    private function restoreAvatar(\App\Models\User $user): RedirectResponse
    {
        if (empty($user->twitch_id)) {
            return redirect()->route('settings.edit')->with('error', __('ui.not_connected_to_twitch'));
        }
        $accessToken = $this->tokenRefresh->getValidToken($user);
        if (! $accessToken) {
            return redirect()->route('settings.edit')->with('error', __('ui.twitch_connection_failed'));
        }
        try {
            $user->update(['avatar_disabled_at' => null]);
            $this->avatarService->restoreFromTwitch($user, $accessToken);
            if ($user->custom_avatar_path) {
                Storage::disk('public')->delete([
                    $user->custom_avatar_path,
                    $user->custom_avatar_thumbnail_path,
                ]);
                $user->update([
                    'custom_avatar_path'           => null,
                    'custom_avatar_thumbnail_path' => null,
                    'avatar_source'                => 'twitch',
                ]);
            }
            $user->refresh();
            if (empty($user->twitch_avatar)) {
                Log::debug('Avatar restore completed but no avatar was stored', ['user_id' => $user->id]);
                return redirect()->route('settings.edit')->with('info', __('ui.no_twitch_avatar'));
            }
            return redirect()->route('settings.edit')->with('success', __('ui.avatar_restored'));
        } catch (\App\Services\Twitch\Exceptions\ValidationException $e) {
            Log::debug('Avatar restore validation failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->route('settings.edit')->with('info', __('ui.no_twitch_avatar'));
        } catch (\Throwable $e) {
            Log::error('Avatar restoration failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->route('settings.edit')->with('error', __('ui.avatar_restore_failed'));
        }
    }

    /**
     * Update cutter profile with professional settings.
     */
    private function updateCutterProfile(\App\Models\User $user, array $data): void
    {
        $cutterProfile = $user->cutterProfile()->firstOrNew();
        $cutterProfile->fill([
            'hourly_rate'      => $data['hourly_rate'] ?? null,
            'response_time'    => $data['response_time'] ?? '24',
            'skills'           => isset($data['skills']) ? json_decode($data['skills'], true) : [],
            'is_available'     => $data['available_for_jobs'] ?? false,
            'portfolio_url'    => $data['portfolio_url'] ?? null,
            'experience_years' => $data['experience_years'] ?? null,
        ]);
        $cutterProfile->save();
    }

    /**
     * Update streamer profile.
     */
    private function updateStreamerProfile(\App\Models\User $user, array $data): void
    {
        $streamerProfile = $user->streamerProfile()->firstOrNew();
        $streamerProfile->fill([
            'intro'           => $data['intro'] ?? null,
            'stream_schedule' => $data['stream_schedule'] ?? null,
            'preferred_games' => $data['preferred_games'] ?? null,
            'stream_quality'  => $data['stream_quality'] ?? '720p',
            'has_overlay'     => $data['has_overlay'] ?? false,
        ]);
        $streamerProfile->save();
    }

    /**
     * Create a thumbnail for the avatar (placeholder).
     */
    private function createAvatarThumbnail(string $originalPath): string
    {
        // TODO: Implement image processing (e.g. Intervention Image)
        return $originalPath;
    }

    /**
     * Compile all user data for export.
     */
    private function compileUserData(\App\Models\User $user): array
    {
        return [
            'user' => [
                'id'           => $user->id,
                'display_name' => $user->display_name,
                'email'        => $user->email,
                'username'     => $user->username,
                'bio'          => $user->bio,
                'timezone'     => $user->timezone,
                'created_at'   => $user->created_at->toISOString(),
                'updated_at'   => $user->updated_at->toISOString(),
            ],
            'roles' => [
                'is_streamer' => $user->is_streamer,
                'is_cutter'   => $user->is_cutter,
                'is_viewer'   => $user->is_viewer,
            ],
            'twitch_connection' => [
                'twitch_id'    => $user->twitch_id,
                'twitch_login' => $user->twitch_login,
                'connected_at' => $user->twitch_connected_at?->toISOString(),
            ],
            'statistics' => [
                'clips_count' => $user->clips()->count(),
            ],
            'streamer_profile' => $user->streamerProfile ? [
                'intro'           => $user->streamerProfile->intro,
                'stream_schedule' => $user->streamerProfile->stream_schedule,
                'preferred_games' => $user->streamerProfile->preferred_games,
                'created_at'      => $user->streamerProfile->created_at->toISOString(),
            ] : null,
            'cutter_profile' => $user->cutterProfile ? [
                'hourly_rate'      => $user->cutterProfile->hourly_rate,
                'response_time'    => $user->cutterProfile->response_time,
                'skills'           => $user->cutterProfile->skills,
                'is_available'     => $user->cutterProfile->is_available,
                'portfolio_url'    => $user->cutterProfile->portfolio_url,
                'experience_years' => $user->cutterProfile->experience_years,
                'created_at'       => $user->cutterProfile->created_at->toISOString(),
            ] : null,
            'exported_at' => now()->toISOString(),
        ];
    }

    /**
     * Delete the user (soft delete, optionally force delete via job).
     */
    protected function deleteUserCompletely(\App\Models\User $user): void
    {
        $user->delete();
        // Optionally: ForceDeleteUser::dispatch($user)->delay(now()->addDays(30));
        // Immediate hard delete (use with caution!):
        // $user->forceDelete();
    }
}
