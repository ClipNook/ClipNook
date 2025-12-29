<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\UploadAvatarRequest;
use App\Services\Twitch\AvatarService;
use App\Services\Twitch\Contracts\OAuthInterface;
use App\Services\Twitch\TokenRefreshService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    public function __construct(
        private readonly OAuthInterface $oauth,
        private readonly TokenRefreshService $tokenRefresh,
        private readonly AvatarService $avatarService
    ) {}

    /**
     * Update avatar status (remove/restore).
     */
    public function update(UpdateAvatarRequest $request): RedirectResponse
    {
        $user = $request->user();

        return match ($request->validated('action')) {
            'remove'  => $this->removeAvatar($user),
            'restore' => $this->restoreAvatar($user),
            default   => redirect()
                ->route('settings.index', ['tab' => 'avatar'])
                ->with('error', __('ui.invalid_action')),
        };
    }

    /**
     * Upload a new custom avatar.
     */
    public function upload(UploadAvatarRequest $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $file = $request->file('avatar');

        DB::beginTransaction();
        try {
            // Delete existing custom avatar
            if ($user->custom_avatar_path) {
                Storage::disk('public')->delete($user->custom_avatar_path);
            }

            $path          = $file->store('avatars/custom', 'public');
            $thumbnailPath = $this->createAvatarThumbnail($path);

            $user->update([
                'custom_avatar_path'           => $path,
                'custom_avatar_thumbnail_path' => $thumbnailPath,
                'avatar_source'                => 'custom',
                'avatar_disabled_at'           => null,
            ]);

            DB::commit();

            $message = __('ui.avatar_upload_success');

            if ($request->expectsJson()) {
                return response()->json([
                    'success'       => true,
                    'message'       => $message,
                    'avatar_url'    => $user->avatar_url,
                    'thumbnail_url' => $user->avatar_thumbnail_url,
                ]);
            }

            return redirect()
                ->route('settings.index', ['tab' => 'avatar'])
                ->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Avatar upload failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            $message = __('ui.avatar_upload_failed');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 500);
            }

            return redirect()
                ->route('settings.index', ['tab' => 'avatar'])
                ->with('error', $message);
        }
    }

    /**
     * Remove avatar and mark as disabled.
     */
    private function removeAvatar(\App\Models\User $user): RedirectResponse
    {
        try {
            $user->update([
                'avatar_disabled_at' => now(),
                'avatar_source'      => 'disabled',
            ]);

            // Delete custom avatar files
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

            return redirect()
                ->route('settings.index', ['tab' => 'avatar'])
                ->with('success', __('ui.avatar_removed'));
        } catch (\Throwable $e) {
            Log::error('Avatar removal failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return redirect()
                ->route('settings.index', ['tab' => 'avatar'])
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
                ->route('settings.index', ['tab' => 'avatar'])
                ->with('error', __('ui.not_connected_to_twitch'));
        }

        $accessToken = $this->tokenRefresh->getValidToken($user);
        if (! $accessToken) {
            return redirect()
                ->route('settings.index', ['tab' => 'avatar'])
                ->with('error', __('ui.twitch_connection_failed'));
        }

        try {
            $user->update(['avatar_disabled_at' => null]);
            $this->avatarService->restoreFromTwitch($user, $accessToken);

            // Delete custom avatar if restoring from Twitch
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

                return redirect()
                    ->route('settings.index', ['tab' => 'avatar'])
                    ->with('info', __('ui.no_twitch_avatar'));
            }

            return redirect()
                ->route('settings.index', ['tab' => 'avatar'])
                ->with('success', __('ui.avatar_restored'));
        } catch (\App\Services\Twitch\Exceptions\ValidationException $e) {
            Log::debug('Avatar restore validation failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return redirect()
                ->route('settings.index', ['tab' => 'avatar'])
                ->with('info', __('ui.no_twitch_avatar'));
        } catch (\Throwable $e) {
            Log::error('Avatar restoration failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return redirect()
                ->route('settings.index', ['tab' => 'avatar'])
                ->with('error', __('ui.avatar_restore_failed'));
        }
    }

    /**
     * Create a thumbnail for the avatar (placeholder).
     */
    private function createAvatarThumbnail(string $originalPath): string
    {
        // TODO: Implement image processing (e.g. Intervention Image)
        return $originalPath;
    }
}
