<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Contracts\GDPRServiceInterface;
use Illuminate\Http\Request;

class GDPRController extends Controller
{
    public function __construct(
        private readonly GDPRServiceInterface $gdprService,
    ) {}
    /**
     * Export user data (Right to Data Portability)
     */
    public function exportData(Request $request)
    {
        $user = $request->user();
        $data = $this->gdprService->exportUserData($user);

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
        if (! $this->gdprService->deleteUserData($user)) {
            return response()->json(['error' => 'Failed to delete user data'], 500);
        }

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
