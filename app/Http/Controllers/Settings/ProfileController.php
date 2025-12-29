<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $user->update([
                'twitch_display_name' => $validated['twitch_display_name'] ?? $user->twitch_display_name,
                'twitch_email'        => $validated['twitch_email'],
                'intro'               => $validated['intro'] ?? null,
                'available_for_jobs'  => $validated['available_for_jobs'] ?? false,
                'allow_clip_sharing'  => $validated['allow_clip_sharing'] ?? false,
            ]);

            DB::commit();

            return redirect()
                ->route('settings.index', ['tab' => 'profile'])
                ->with('success', __('ui.profile_updated'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Profile update failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return redirect()
                ->route('settings.index', ['tab' => 'profile'])
                ->with('error', __('ui.profile_update_failed'));
        }
    }
}
