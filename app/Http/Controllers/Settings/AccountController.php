<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteAccountRequest;
use App\Http\Requests\ExportDataRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountController extends Controller
{
    /**
     * Export all user data as JSON.
     */
    public function export(ExportDataRequest $request): StreamedResponse
    {
        $user     = $request->user();
        $filename = "user-data-{$user->id}-".now()->format('Y-m-d-H-i-s').'.json';
        $data     = $this->compileUserData($user);

        return response()->streamDownload(
            fn () => print json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            $filename,
            ['Content-Type' => 'application/json']
        );
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

            return redirect('/')->with('success', __('ui.account_deleted'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Account deletion failed', ['error' => $e->getMessage()]);

            return redirect()
                ->route('settings.index', ['tab' => 'account'])
                ->with('error', __('ui.account_deletion_failed'));
        }
    }

    /**
     * Compile all user data for export.
     */
    private function compileUserData(\App\Models\User $user): array
    {
        return [
            'user' => [
                'id'           => $user->id,
                'twitch_id'    => $user->twitch_id,
                'display_name' => $user->display_name,
                'email'        => $user->twitch_email,
                'username'     => $user->twitch_login,
                'intro'        => $user->intro,
                'timezone'     => $user->timezone,
                'locale'       => $user->locale,
                'theme'        => $user->theme_preference,
                'accent_color' => $user->accent_color,
                'created_at'   => $user->created_at->toISOString(),
                'updated_at'   => $user->updated_at->toISOString(),
            ],
            'roles' => [
                'is_streamer'  => $user->is_streamer,
                'is_cutter'    => $user->is_cutter,
                'is_viewer'    => $user->is_viewer,
                'is_moderator' => $user->is_moderator,
                'is_admin'     => $user->is_admin,
            ],
            'preferences'       => $user->getCachedPreferences(),
            'twitch_connection' => [
                'twitch_id'    => $user->twitch_id,
                'twitch_login' => $user->twitch_login,
                'connected_at' => $user->twitch_connected_at?->toISOString(),
            ],
            'avatar' => [
                'source'      => $user->avatar_source,
                'url'         => $user->avatar_url,
                'disabled_at' => $user->avatar_disabled_at?->toISOString(),
            ],
            'statistics' => [
                'clips_count' => $user->clips()->count(),
            ],
            'streamer_profile' => $user->streamerProfile ? [
                'intro'           => $user->streamerProfile->intro,
                'stream_schedule' => $user->streamerProfile->stream_schedule,
                'preferred_games' => $user->streamerProfile->preferred_games,
                'stream_quality'  => $user->streamerProfile->stream_quality,
                'has_overlay'     => $user->streamerProfile->has_overlay,
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
