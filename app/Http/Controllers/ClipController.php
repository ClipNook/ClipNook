<?php

namespace App\Http\Controllers;

use App\Actions\Clip\SubmitClipAction;
use App\Models\Clip;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ClipController extends Controller
{
    public function __construct(
        private SubmitClipAction $submitClipAction
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $query = Clip::with(['user:id,twitch_display_name,twitch_login'])
                ->approved()
                ->orderBy('created_at', 'desc');

            // Filter by featured
            if ($request->boolean('featured')) {
                $query->featured();
            }

            // Filter by user
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

            $clips = $query->paginate(20);

            return response()->json($clips);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created clip
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'twitch_clip_id' => 'required|string|regex:/^[A-Za-z0-9_-]+$/',
        ]);

        try {
            $clip = $this->submitClipAction->execute(
                Auth::user(),
                $request->string('twitch_clip_id')
            );

            return response()->json([
                'message' => 'Clip submitted successfully and is pending moderation.',
                'clip'    => $clip->load('user:id,twitch_display_name,twitch_login'),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified clip
     */
    public function show(Clip $clip): JsonResponse
    {
        // Only show approved clips to regular users
        if (! Auth::check() || ! Auth::user()->can('moderate clips')) {
            Gate::authorize('view', $clip);
        }

        return response()->json($clip->load(['user:id,twitch_display_name,twitch_login', 'moderator:id,twitch_display_name']));
    }

    /**
     * Update the specified clip (moderation actions)
     */
    public function update(Request $request, Clip $clip): JsonResponse
    {
        Gate::authorize('moderate', $clip);

        $request->validate([
            'action' => 'required|in:approve,reject,flag,toggle_featured',
            'reason' => 'required_if:action,reject,flag|string|nullable',
        ]);

        $moderator = Auth::user();

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
            'clip'    => $clip->load(['user:id,twitch_display_name,twitch_login', 'moderator:id,twitch_display_name']),
        ]);
    }

    /**
     * Remove the specified clip
     */
    public function destroy(Clip $clip): JsonResponse
    {
        Gate::authorize('delete', $clip);

        $clip->delete();

        return response()->json([
            'message' => 'Clip deleted successfully.',
        ]);
    }

    /**
     * Get pending clips for moderation
     */
    public function pending(Request $request): JsonResponse
    {
        Gate::authorize('moderate clips');

        $clips = Clip::with(['user:id,twitch_display_name,twitch_login'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($clips);
    }

    /**
     * Get user's clips
     */
    public function userClips(Request $request, User $user): JsonResponse
    {
        // Users can only see their own clips, or approved clips of others
        if (Auth::id() !== $user->id && ! Auth::user()?->can('moderate clips')) {
            $query = $user->clips()->approved();
        } else {
            $query = $user->clips();
        }

        $clips = $query->with('user:id,twitch_display_name,twitch_login')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($clips);
    }
}
