<?php

namespace App\Http\Controllers;

use App\Actions\Clip\SubmitClipAction;
use App\Exceptions\BroadcasterNotRegisteredException;
use App\Exceptions\ClipNotFoundException;
use App\Exceptions\ClipPermissionException;
use App\Http\Requests\Clip\SubmitClipRequest;
use App\Http\Requests\Clip\UpdateClipRequest;
use App\Models\Clip;
use App\Models\User;
use App\Services\Cache\QueryCacheService;
use App\Services\ClipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

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
        private SubmitClipAction $submitClipAction,
        private QueryCacheService $queryCache,
        private ClipService $clipService
    ) {}

    /**
     * Get a paginated list of approved clips.
     *
     * Supports filtering by featured status, user, and search terms.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Clip::with([
                'user:id,twitch_display_name,twitch_login',
                'broadcaster:id,name,login',
                'game:id,name',
            ])
                ->approved()
                ->orderBy('created_at', 'desc');

            // Filter by featured status
            if ($request->boolean('featured')) {
                $query->where('is_featured', true);
            }

            // Filter by specific user
            if ($request->has('user_id')) {
                $query->where('user_id', $request->integer('user_id'));
            }

            // Search by title or description
            if ($request->has('search')) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $clips = $this->queryCache->remember(
                prefix: 'clips:index',
                query: $query,
                ttl: 300, // 5 minutes
                tags: ['clips', 'public']
            );

            return response()->json([
                'data' => $clips,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to retrieve clips.',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error.',
            ], 500);
        }
    }

    /**
     * Submit a new clip from Twitch.
     */
    public function store(SubmitClipRequest $request): JsonResponse
    {
        try {
            // Use synchronous execution in testing environment for immediate feedback
            if (config('app.use_sync_clip_submission', false)) {
                $clip = $this->clipService->submitClip(
                    Auth::user(),
                    $request->string('twitch_clip_id')
                );

                return response()->json([
                    'message' => 'Clip submitted successfully and is pending moderation.',
                    'clip'    => $clip->load([
                        'user:id,twitch_display_name,twitch_login',
                        'broadcaster:id,name,login',
                        'game:id,name',
                    ]),
                ], 201);
            }

            // Use asynchronous job dispatching in production
            $this->clipService->submitClip(
                Auth::user(),
                $request->string('twitch_clip_id')
            );

            return response()->json([
                'message' => 'Clip submitted successfully and is being processed in the background.',
                'status'  => 'processing',
            ], 202);
        } catch (BroadcasterNotRegisteredException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error'   => 'broadcaster_not_registered',
            ], 400);
        } catch (ClipNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error'   => 'clip_not_found',
            ], 404);
        } catch (ClipPermissionException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error'   => 'permission_denied',
            ], 403);
        } catch (\Exception $e) {
            Log::error('Clip submission failed', [
                'error'        => $e->getMessage(),
                'user_id'      => Auth::id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred while submitting the clip.',
                'error'   => 'internal_error',
            ], 500);
        }
    }

    /**
     * Get a specific clip by ID.
     */
    public function show(Clip $clip): JsonResponse
    {
        // Only show approved clips to regular users
        if (! Auth::check() || ! Auth::user()->can('view', $clip)) {
            Gate::authorize('view', $clip);
        }

        return response()->json(
            $clip->load([
                'user:id,twitch_display_name,twitch_login',
                'broadcaster:id,name,login',
                'game:id,name',
                'moderator:id,twitch_display_name',
            ])
        );
    }

    /**
     * Moderate a clip (approve, reject, flag, or toggle featured status).
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
                    $this->clipService->toggleFeatured($clip);
                    $message = $clip->is_featured ? 'Clip marked as featured.' : 'Clip removed from featured.';
                    break;
            }

            return response()->json([
                'message' => $message,
                'clip'    => $clip->load([
                    'user:id,twitch_display_name,twitch_login',
                    'broadcaster:id,name,login',
                    'game:id,name',
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
     */
    public function destroy(Clip $clip): JsonResponse
    {
        Gate::authorize('delete', $clip);

        try {
            $this->clipService->deleteClip($clip);

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
     */
    public function pending(Request $request): JsonResponse
    {
        // Check if user has any moderation permissions
        $hasModerationPermission = Auth::user()->broadcasterSettings ||
            Auth::user()->clipPermissionsReceived()->where('can_moderate_clips', true)->exists();

        if (! $hasModerationPermission) {
            abort(403, 'Unauthorized');
        }

        try {
            $clips = Clip::with([
                'user:id,twitch_display_name,twitch_login',
                'broadcaster:id,name,login',
                'game:id,name',
            ])
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
     */
    public function userClips(Request $request, User $user): JsonResponse
    {
        try {
            // Users can only see their own clips, or approved clips of others
            if (Auth::id() !== $user->id) {
                $query = $user->clips()->approved();
            } else {
                $query = $user->clips();
            }

            $clips = $query->with([
                'user:id,twitch_display_name,twitch_login',
                'broadcaster:id,name,login',
                'game:id,name',
            ])
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

    /**
     * Get featured clips
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $clips = $this->clipService->getFeaturedClips(
                $request->integer('limit', 10)
            );

            return response()->json([
                'data' => $clips->load([
                    'user:id,twitch_display_name,twitch_login',
                    'broadcaster:id,name,login',
                    'game:id,name',
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to retrieve featured clips.',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error.',
            ], 500);
        }
    }

    /**
     * Get recent clips
     */
    public function recent(Request $request): JsonResponse
    {
        try {
            $clips = $this->clipService->getRecentClips(
                $request->integer('limit', 20)
            );

            return response()->json([
                'data' => $clips->load([
                    'user:id,twitch_display_name,twitch_login',
                    'broadcaster:id,name,login',
                    'game:id,name',
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to retrieve recent clips.',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error.',
            ], 500);
        }
    }

    /**
     * Search clips
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        try {
            $clips = $this->clipService->searchClips(
                $request->string('q'),
                $request->integer('per_page', 15)
            );

            return response()->json([
                'data' => $clips->load([
                    'user:id,twitch_display_name,twitch_login',
                    'broadcaster:id,name,login',
                    'game:id,name',
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to search clips.',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error.',
            ], 500);
        }
    }

    /**
     * Get user clip statistics
     */
    public function stats(Request $request, User $user): JsonResponse
    {
        // Users can only see their own stats
        if (Auth::id() !== $user->id) {
            abort(403, 'Unauthorized');
        }

        try {
            $stats = $this->clipService->getUserStats($user);

            return response()->json([
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to retrieve user statistics.',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error.',
            ], 500);
        }
    }
}
