<?php

declare(strict_types=1);

namespace App\Actions\GDPR;

use App\Models\User;

use function config;
use function now;

final class ExportUserDataAction
{
    public function execute(User $user): array
    {
        // Eager load all related data to avoid N+1 queries
        $user->load([
            'broadcasterClips',
            'submittedClips',
            'clipComments',
            'clipVotes',
            'broadcasterSettings',
            'broadcasterPermissions',
            'grantedPermissions',
            'tokens',
        ]);

        return [
            'metadata'      => $this->generateMetadata($user),
            'personal_data' => $this->extractPersonalData($user),
            'content_data'  => $this->extractContentData($user),
            'activity_data' => $this->extractActivityData($user),
            'settings_data' => $this->extractSettingsData($user),
        ];
    }

    private function generateMetadata(User $user): array
    {
        return [
            'export_date'         => now()->toIso8601String(),
            'user_id'             => $user->id,
            'data_format_version' => '1.0',
            'application'         => config('app.name'),
            'gdpr_article'        => 'Article 20 - Right to data portability',
        ];
    }

    private function extractPersonalData(User $user): array
    {
        return [
            'id'                  => $user->id,
            'twitch_id'           => $user->twitch_id,
            'twitch_login'        => $user->twitch_login,
            'twitch_display_name' => $user->twitch_display_name,
            'twitch_email'        => $user->twitch_email,
            'created_at'          => $user->created_at?->toIso8601String(),
            'updated_at'          => $user->updated_at?->toIso8601String(),
            'last_activity_at'    => $user->last_activity_at?->toIso8601String(),
        ];
    }

    private function extractContentData(User $user): array
    {
        return [
            'broadcaster_clips' => $user->broadcasterClips->map(static fn ($clip) => [
                'id'          => $clip->id,
                'uuid'        => $clip->uuid,
                'title'       => $clip->title,
                'description' => $clip->description,
                'url'         => $clip->url,
                'duration'    => $clip->duration,
                'view_count'  => $clip->view_count,
                'upvotes'     => $clip->upvotes,
                'downvotes'   => $clip->downvotes,
                'status'      => $clip->status,
                'created_at'  => $clip->created_at?->toIso8601String(),
            ])->toArray(),
            'submitted_clips' => $user->submittedClips->map(static fn ($clip) => [
                'id'           => $clip->id,
                'uuid'         => $clip->uuid,
                'title'        => $clip->title,
                'status'       => $clip->status,
                'submitted_at' => $clip->submitted_at?->toIso8601String(),
            ])->toArray(),
            'comments' => $user->clipComments->map(static fn ($comment) => [
                'id'         => $comment->id,
                'clip_id'    => $comment->clip_id,
                'content'    => $comment->content,
                'created_at' => $comment->created_at?->toIso8601String(),
            ])->toArray(),
        ];
    }

    private function extractActivityData(User $user): array
    {
        return [
            'votes' => $user->clipVotes->map(static fn ($vote) => [
                'clip_id'    => $vote->clip_id,
                'vote_type'  => $vote->vote_type,
                'created_at' => $vote->created_at?->toIso8601String(),
            ])->toArray(),
            'api_tokens' => $user->tokens->map(static fn ($token) => [
                'name'         => $token->name,
                'abilities'    => $token->abilities,
                'last_used_at' => $token->last_used_at?->toIso8601String(),
                'created_at'   => $token->created_at?->toIso8601String(),
            ])->toArray(),
        ];
    }

    private function extractSettingsData(User $user): array
    {
        return [
            'roles' => [
                'is_streamer'  => $user->is_streamer,
                'is_moderator' => $user->is_moderator,
                'is_admin'     => $user->is_admin,
            ],
            'broadcaster_settings' => $user->broadcasterSettings ? [
                'clip_submission_permission' => $user->broadcasterSettings->clip_submission_permission,
                'auto_approve_trusted'       => $user->broadcasterSettings->auto_approve_trusted,
                'allow_comments'             => $user->broadcasterSettings->allow_comments,
            ] : null,
            'permissions_granted_to_others' => $user->broadcasterPermissions->map(static fn ($perm) => [
                'user_id'            => $perm->user_id,
                'can_submit_clips'   => $perm->can_submit_clips,
                'can_edit_clips'     => $perm->can_edit_clips,
                'can_delete_clips'   => $perm->can_delete_clips,
                'can_moderate_clips' => $perm->can_moderate_clips,
            ])->toArray(),
            'permissions_granted_by_others' => $user->grantedPermissions->map(static fn ($perm) => [
                'broadcaster_id'     => $perm->broadcaster_id,
                'can_submit_clips'   => $perm->can_submit_clips,
                'can_edit_clips'     => $perm->can_edit_clips,
                'can_delete_clips'   => $perm->can_delete_clips,
                'can_moderate_clips' => $perm->can_moderate_clips,
            ])->toArray(),
            'appearance_settings'      => $user->appearance_settings,
            'notification_preferences' => $user->notification_preferences,
        ];
    }
}
