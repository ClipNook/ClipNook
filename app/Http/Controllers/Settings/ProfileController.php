<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user      = $request->user();
        $validated = $request->validated();

        DB::transaction(function () use ($user, $validated) {
            $user->update([
                'intro'               => $validated['intro'] ?? null,
                'available_for_jobs'  => $validated['available_for_jobs'] ?? false,
                'allow_clip_sharing'  => $validated['allow_clip_sharing'] ?? false,
            ]);
        });

        return redirect()
            ->route('settings.index', ['tab' => 'profile'])
            ->with('success', __('ui.profile_updated'));
    }
}
