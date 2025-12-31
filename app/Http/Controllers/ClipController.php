<?php

namespace App\Http\Controllers;

use App\Actions\Clip\SubmitClipAction;
use App\Http\Requests\Clip\SubmitClipRequest;
use App\Http\Requests\Clip\UpdateClipRequest;
use App\Models\Clip;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Controller for managing clip operations.
 *
 * This controller handles all clip-related API endpoints including:
 * - Listing and viewing clips
 * - Submitting new clips
 * - Moderating clips (approve/reject/flag)
 * - Managing featured clips
 * - User-specific clip listings
 */
class ClipController extends Controller
{
    public function __construct(
        private SubmitClipAction $submitClipAction
    ) {}

    /**
     * Get a paginated list of approved clips.
     *
     * Supports filtering by featured status, user, and search terms.
     *
     * @param  Request  $request  The HTTP request
     * @return JsonResponse Paginated clips data
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Clip::with(['submitter:id,twitch_display_name,twitch_login'])
                ->approved()
                ->orderBy('created_at', 'desc');

            // Filter by featured status
            if ($request->boolean('featured')) {
                $query->featured();
            }

            // Filter by specific user
            if ($request->has('user_id')) {
                $query->where('submitter_id', $request->integer('user_id'));
            }

            // Search by title or description
            if ($request->has('search')) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $clips = $query->paginate(20);

            return response()->json($clips);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to retrieve clips.',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error.',
            ], 500);
        }
    }

    /**
     * Submit a new clip from Twitch.
     *
     * @param  SubmitClipRequest  $request  The validated request containing twitch_clip_id
     * @return JsonResponse The created clip data
     */
    public function store(SubmitClipRequest $request): JsonResponse
    {
        try {
            $clip = $this->submitClipAction->execute(
                Auth::user(),
                $request->string('twitch_clip_id')
            );

            return response()->json([
                'message' => 'Clip submitted successfully and is pending moderation.',
                'clip'    => $clip->load('submitter:id,twitch_display_name,twitch_login'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to submit clip.',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error.',
            ], 500);
        }
    }

    /**
     * Get a specific clip by ID.
     *
     * Regular users can only see approved clips or their own submissions.
     * Moderators can see all clips.
     *
     * @param  Clip  $clip  The clip to display
     * @return JsonResponse The clip data
     */
    public function show(Clip $clip): JsonResponse
    {
        // Only show approved clips to regular users
        if (! Auth::check() || ! Auth::user()->can('view', $clip)) {
            Gate::authorize('view', $clip);
        }

        return response()->json(
            $clip->load([
                'submitter:id,twitch_display_name,twitch_login',
                'moderator:id,twitch_display_name',
            ])
        );
    }

    /**
     * Moderate a clip (approve, reject, flag, or toggle featured status).
     *
     * @param  UpdateClipRequest  $request  The validated request
     * @param  Clip  $clip  The clip to moderate
     * @return JsonResponse Updated clip data
     */
    public function update(UpdateClipRequest $request, Clip $clip): JsonResponse
    {
        try {
            $moderator = Auth::user();
            $message   = '';

            switch ($request->string('action')) {
                case 'approve':
                    $clip->approve($moderator);
                    $message = 'Clip approved successfully.';
                    break;

                case 'reject':
                    $clip->reject($request->string('reason'), $moderator);
                    $message = 'Clip rejected successfully.';
                    break;

                case 'flag':
                    $clip->flag($request->string('reason'), $moderator);
                    $message = 'Clip flagged successfully.';
                    break;

                case 'toggle_featured':
                    $clip->toggleFeatured();
                    $message = $clip->is_featured ? 'Clip marked as featured.' : 'Clip removed from featured.';
                    break;
            }

            return response()->json([
                'message' => $message,
                'clip'    => $clip->load([
                    'submitter:id,twitch_display_name,twitch_login',
                    'moderator:id,twitch_display_name',
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to update clip.',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error.',
            ], 500);
        }
    }

    /**
     * Delete a clip.
     *
     * @param  Clip  $clip  The clip to delete
     * @return JsonResponse Success message
     */
    public function destroy(Clip $clip): JsonResponse
    {
        Gate::authorize('delete', $clip);

        try {
            $clip->delete();

            return response()->json([
                'message' => 'Clip deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to delete clip.',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error.',
            ], 500);
        }
    }

    /**
     * Get pending clips for moderation.
     *
     * Only accessible to users with moderation permissions.
     *
     * @param  Request  $request  The HTTP request
     * @return JsonResponse Paginated pending clips
     */
    public function pending(Request $request): JsonResponse
    {
        // Check if user has any moderation permissions
        // In a more complex system, you might filter clips by broadcasters they can moderate
        $hasModerationPermission = Auth::user()->broadcasterSettings ||
            Auth::user()->clipPermissionsReceived()->where('can_moderate_clips', true)->exists();

        if (! $hasModerationPermission) {
            abort(403, 'Unauthorized');
        }

        try {
            $clips = Clip::with(['submitter:id,twitch_display_name,twitch_login'])
                ->pending()
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json($clips);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to retrieve pending clips.',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error.',
            ], 500);
        }
    }

    /**
     * Get clips submitted by a specific user.
     *
     * Users can see their own clips, moderators can see all clips.
     *
     * @param  Request  $request  The HTTP request
     * @param  User  $user  The user whose clips to retrieve
     * @return JsonResponse Paginated user clips
     */
    public function userClips(Request $request, User $user): JsonResponse
    {
        try {
            // Users can only see their own clips, or approved clips of others
            if (Auth::id() !== $user->id) {
                // Check if current user can view this user's clips
                $query = $user->clips()->approved();
            } else {
                $query = $user->clips();
            }

            $clips = $query->with('submitter:id,twitch_display_name,twitch_login')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json($clips);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to retrieve user clips.',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error.',
            ], 500);
        }
    }
}
