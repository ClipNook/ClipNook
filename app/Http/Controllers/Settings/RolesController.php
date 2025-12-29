<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateRolesRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolesController extends Controller
{
    /**
     * Update user roles and associated profiles.
     */
    public function update(UpdateRolesRequest $request): RedirectResponse
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

            // Update or create profiles based on roles
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

            return redirect()
                ->route('settings.index', ['tab' => 'roles'])
                ->with('success', __('ui.roles_updated'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Roles update failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return redirect()
                ->route('settings.index', ['tab' => 'roles'])
                ->with('error', __('ui.roles_update_failed'));
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
}
