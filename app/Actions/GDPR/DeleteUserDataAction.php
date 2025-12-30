<?php

namespace App\Actions\GDPR;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DeleteUserDataAction
{
    /**
     * Execute the user data deletion/anonymization
     */
    public function execute(User $user, bool $softDelete = true): void
    {
        DB::transaction(function () use ($user, $softDelete) {
            // Log the deletion action
            $user->activityLogs()->create([
                'action'      => 'account_deletion_completed',
                'description' => 'User account data deleted/anonymized',
                'metadata'    => [
                    'deletion_type' => $softDelete ? 'soft_delete' : 'hard_delete',
                    'ip_address'    => request()->ip(),
                    'user_agent'    => request()->userAgent(),
                ],
            ]);

            // Anonymize personal data
            $anonymizedData = [
                'twitch_id'               => 'deleted_'.Str::random(10),
                'twitch_login'            => 'deleted_user_'.$user->id,
                'twitch_display_name'     => 'Deleted User',
                'twitch_email'            => null,
                'twitch_access_token'     => null,
                'twitch_refresh_token'    => null,
                'twitch_token_expires_at' => null,
                'twitch_avatar'           => null,
                'custom_avatar_path'      => null,
                'description'             => null,
                'preferences'             => [],
                'scopes'                  => [],
            ];

            $user->update($anonymizedData);

            // Delete avatar files
            if ($user->custom_avatar_path) {
                Storage::delete($user->custom_avatar_path);
            }

            // Handle clips (anonymize, don't delete)
            if (method_exists($user, 'clips')) {
                $user->clips()->update([
                    'creator_name'  => 'Anonymous',
                    'creator_id'    => null,
                    'submission_ip' => null,
                ]);
            }

            // Anonymize old activity logs (keep for audit purposes)
            $user->activityLogs()
                ->where('created_at', '<', now()->subDays(30))
                ->update([
                    'ip_address'      => null,
                    'user_agent_hash' => null,
                    'metadata'        => DB::raw("JSON_SET(metadata, '$.anonymized', true)"),
                ]);

            // Revoke all consents
            if (method_exists($user, 'consents')) {
                $user->consents()->update([
                    'consented'    => false,
                    'consented_at' => null,
                ]);
            }

            // Soft delete or hard delete based on parameter
            if ($softDelete) {
                $user->delete(); // Keeps record for audit but marks as deleted
            } else {
                $user->activityLogs()->delete();
                $user->consents()->delete();
                $user->forceDelete(); // Hard delete - completely remove from database
            }
        });
    }
}
