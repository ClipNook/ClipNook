<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'twitch_id'    => $this->twitch_id,
            'display_name' => $this->display_name,
            'avatar_url'   => $this->avatar_url,
            'email'        => $this->when($request->user()?->isAdmin(), $this->twitch_email),

            // Profile data
            'profile' => [
                'intro'                 => $this->intro,
                'available_for_jobs'    => $this->available_for_jobs,
                'allow_clip_sharing'    => $this->allow_clip_sharing,
                'completion_percentage' => $this->profileCompletion(),
                'completed_steps'       => $this->completedProfileSteps(),
                'missing_steps'         => $this->missingProfileSteps(),
            ],

            // Roles
            'roles' => [
                'is_streamer'  => $this->isStreamer(),
                'is_cutter'    => $this->isCutter(),
                'is_moderator' => $this->isModerator(),
                'is_admin'     => $this->isAdmin(),
                'primary_role' => $this->primary_role,
                'has_any_role' => $this->hasAnyRole(),
            ],

            // Preferences
            'preferences' => $this->getCachedPreferences(),

            // Timestamps
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
            'created_at_human'   => $this->createdAtHuman(),
            'updated_at_human'   => $this->updatedAtHuman(),
            'profile_updated_at' => $this->profileUpdatedAt(),

            // Relationships (conditionally loaded)
            $this->mergeWhen($request->has('include'), [
                'notifications_count' => $this->when($request->has('include.notifications'), $this->unreadNotificationsCount()),
                'streamer_profile'    => StreamerProfileResource::make($this->whenLoaded('streamerProfile')),
                'cutter_profile'      => CutterProfileResource::make($this->whenLoaded('cutterProfile')),
            ]),
        ];
    }
}
