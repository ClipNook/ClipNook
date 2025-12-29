<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PreferencesController extends Controller
{
    /**
     * Update user preferences via AJAX or form submission.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $user->update([
                'theme_preference' => $validated['theme_preference'] ?? $user->theme_preference,
                'locale'           => $validated['locale'] ?? $user->locale,
                'timezone'         => $validated['timezone'] ?? $user->timezone,
                'accent_color'     => $validated['accent_color'] ?? $user->accent_color,
            ]);

            // Clear preferences cache
            $user->clearPreferencesCache();

            DB::commit();

            $message = __('ui.preferences_updated');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return redirect()
                ->route('settings.index', ['tab' => 'preferences'])
                ->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Preferences update failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            $message = __('ui.preferences_update_failed');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 500);
            }

            return redirect()
                ->route('settings.index', ['tab' => 'preferences'])
                ->with('error', $message);
        }
    }

    /**
     * Update theme preference via AJAX.
     */
    public function updateTheme(Request $request): JsonResponse
    {
        $request->validate([
            'theme' => ['required', 'string', 'in:system,light,dark'],
        ]);

        $user                   = $request->user();
        $user->theme_preference = $request->theme;
        $user->clearPreferencesCache();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => __('ui.theme_updated'),
        ]);
    }
}
