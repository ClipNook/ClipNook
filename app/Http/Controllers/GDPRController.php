<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class GDPRController extends Controller
{
    /**
     * Export user data (Right to Data Portability)
     */
    public function exportData(Request $request)
    {
        $user = $request->user();

        $data = [
            'personal_information' => [
                'twitch_id'           => $user->twitch_id,
                'twitch_login'        => $user->twitch_login,
                'twitch_display_name' => $user->twitch_display_name,
                'twitch_email'        => $user->twitch_email,
                'description'         => $user->description,
                'created_at'          => $user->created_at,
                'last_login_at'       => $user->last_login_at,
                'last_activity_at'    => $user->last_activity_at,
            ],
            'preferences' => $user->preferences ?? [],
            'roles'       => [
                'is_viewer'    => $user->is_viewer,
                'is_streamer'  => $user->is_streamer,
                'is_moderator' => $user->is_moderator,
                'is_admin'     => $user->is_admin,
            ],
            'activity_logs' => $user->activityLogs()
                ->orderBy('created_at', 'desc')
                ->take(1000)
                ->get()
                ->map(function ($log) {
                    return [
                        'action'      => $log->action,
                        'description' => $log->description,
                        'created_at'  => $log->created_at,
                        'metadata'    => $log->metadata,
                    ];
                }),
            'exported_at'             => now(),
            'data_portability_rights' => [
                'This data export is provided under GDPR Article 20 (Right to Data Portability)',
                'You have the right to receive your personal data in a structured, commonly used format',
                'You have the right to transmit this data to another controller',
            ],
        ];

        // Add clips data if clips exist
        if (method_exists($user, 'clips')) {
            $data['clips'] = $user->clips()
                ->withTrashed()
                ->get()
                ->map(function ($clip) {
                    return [
                        'id'               => $clip->id,
                        'twitch_clip_id'   => $clip->twitch_clip_id,
                        'title'            => $clip->title,
                        'broadcaster_name' => $clip->broadcaster_name,
                        'game_name'        => $clip->game_name,
                        'view_count'       => $clip->view_count,
                        'duration'         => $clip->duration,
                        'status'           => $clip->status,
                        'created_at'       => $clip->created_at,
                        'deleted_at'       => $clip->deleted_at,
                    ];
                });
        }

        return response()->json($data, 200, [
            'Content-Type'        => 'application/json',
            'Content-Disposition' => 'attachment; filename="my-data-'.now()->format('Y-m-d').'.json"',
        ]);
    }

    /**
     * Request account deletion (Right to be Forgotten)
     */
    public function requestDeletion(Request $request)
    {
        $user   = $request->user();
        $reason = $request->input('reason');

        // Log the deletion request
        $user->activityLogs()->create([
            'action'      => 'account_deletion_requested',
            'description' => 'User requested account deletion',
            'metadata'    => [
                'reason'     => $reason,
                'ip_address' => pseudonymize_ip(request()->ip()),
                'user_agent' => request()->userAgent(),
            ],
        ]);

        // Send confirmation email
        $user->notify(new AccountDeletionRequested($user));

        return response()->json([
            'message'              => 'Account deletion request submitted. You will receive a confirmation email.',
            'estimated_completion' => now()->addDays(30)->format('Y-m-d'),
        ]);
    }

    /**
     * Confirm account deletion
     */
    public function confirmDeletion(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = $request->user();

        // Verify the deletion token (implement proper token verification)
        if (! $this->verifyDeletionToken($user, $request->token)) {
            return response()->json(['error' => 'Invalid deletion token'], 400);
        }

        // Perform the deletion
        app(\App\Actions\GDPR\DeleteUserDataAction::class)->execute($user);

        // Log out the user
        auth()->logout();

        return response()->json([
            'message'         => 'Your account has been successfully deleted.',
            'gdpr_compliance' => 'All your personal data has been anonymized or removed in accordance with GDPR.',
        ]);
    }

    /**
     * Get data processing consent status
     */
    public function getConsents(Request $request)
    {
        $user = $request->user();

        $consents = $user->consents()
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($consent) {
                return [
                    'type'         => $consent->consent_type,
                    'version'      => $consent->consent_version,
                    'consented'    => $consent->consented,
                    'consented_at' => $consent->consented_at,
                    'updated_at'   => $consent->updated_at,
                ];
            });

        return response()->json([
            'consents'          => $consents,
            'required_consents' => [
                'terms'           => config('gdpr.consent_versions.terms', '1.0'),
                'privacy_policy'  => config('gdpr.consent_versions.privacy_policy', '1.0'),
                'data_processing' => config('gdpr.consent_versions.data_processing', '1.0'),
            ],
        ]);
    }

    /**
     * Update consent preferences
     */
    public function updateConsents(Request $request)
    {
        $request->validate([
            'consents'             => 'required|array',
            'consents.*.type'      => 'required|string|in:terms,privacy_policy,data_processing',
            'consents.*.consented' => 'required|boolean',
        ]);

        $user = $request->user();

        foreach ($request->consents as $consentData) {
            $user->consents()->updateOrCreate(
                [
                    'consent_type' => $consentData['type'],
                ],
                [
                    'consented'       => $consentData['consented'],
                    'consented_at'    => $consentData['consented'] ? now() : null,
                    'ip_address'      => request()->ip(),
                    'user_agent_hash' => hash('sha256', request()->userAgent()),
                    'consent_version' => config("gdpr.consent_versions.{$consentData['type']}", '1.0'),
                ]
            );

            // Log consent change
            $user->activityLogs()->create([
                'action'      => 'consent_updated',
                'description' => "Consent for {$consentData['type']} ".($consentData['consented'] ? 'granted' : 'revoked'),
                'metadata'    => [
                    'consent_type' => $consentData['type'],
                    'consented'    => $consentData['consented'],
                    'ip_address'   => pseudonymize_ip(request()->ip()),
                ],
            ]);
        }

        return response()->json([
            'message' => 'Consent preferences updated successfully.',
        ]);
    }

    /**
     * Get data retention information
     */
    public function getRetentionInfo(Request $request)
    {
        $user          = $request->user();
        $retentionDays = config('gdpr.data_retention_days', 2555); // 7 years default

        return response()->json([
            'data_retention_policy' => [
                'description'                => 'We retain your personal data for a maximum of '.$retentionDays.' days after your last activity.',
                'retention_period_days'      => $retentionDays,
                'last_activity'              => $user->last_activity_at,
                'data_deletion_date'         => $user->last_activity_at?->addDays($retentionDays),
                'can_request_early_deletion' => true,
            ],
            'data_categories_retained' => [
                'account_information' => 'Basic account details',
                'activity_logs'       => 'User activity history (anonymized after 30 days)',
                'clips'               => 'Submitted clips (anonymized, not deleted)',
                'consent_history'     => 'Consent preferences history',
            ],
        ]);
    }

    /**
     * This is a simplified example
     * Implement proper token verification (e.g., signed tokens with expiration)
     */
    private function verifyDeletionToken(User $user, string $token): bool
    {
        $expectedToken = hash_hmac('sha256', $user->id.$user->email.'deletion', config('app.key'));

        return hash_equals($expectedToken, $token);
    }
}
