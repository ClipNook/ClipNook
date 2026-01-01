<?php

declare(strict_types=1);

namespace App\Services\GDPR;

use App\Contracts\GDPRServiceInterface;
use App\Models\ActivityLog;
use App\Models\Clip;
use App\Models\ClipComment;
use App\Models\ClipVote;
use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling GDPR compliance operations.
 *
 * Implements the right to access, right to erasure,
 * and data portability as required by GDPR.
 */
final readonly class GDPRService implements GDPRServiceInterface
{
	/**
	 * Export all user data in a portable JSON format.
	 *
	 * @param  User  $user
	 * @return array<string, mixed>
	 */
	public function exportUserData(User $user): array
	{
		return [
			'personal_information' => $this->getPersonalInformation($user),
			'preferences'          => $user->preferences ?? [],
			'roles'                => $this->getUserRoles($user),
			'submissions'          => $this->getUserSubmissions($user),
			'comments'             => $this->getUserComments($user),
			'votes'                => $this->getUserVotes($user),
			'activity_logs'        => $this->getUserActivityLogs($user),
			'consents'             => $this->getUserConsents($user),
			'exported_at'          => now()->toIso8601String(),
			'data_version'         => '1.0',
		];
	}

	/**
	 * Permanently delete user data in compliance with GDPR.
	 *
	 * @param  User  $user
	 * @return bool
	 */
	public function deleteUserData(User $user): bool
	{
		try {
			DB::beginTransaction();

			// Anonymize user contributions
			$this->anonymizeUserContributions($user);

			// Delete personal data
			$this->deletePersonalData($user);

			// Delete user record
			$user->delete();

			DB::commit();
			Log::info('User data deleted', ['user_id' => $user->id]);

			return true;
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error('Failed to delete user data', [
				'user_id' => $user->id,
				'error'   => $e->getMessage(),
			]);

			return false;
		}
	}

	/**
	 * Anonymize user data while preserving content.
	 *
	 * @param  User  $user
	 * @return bool
	 */
	public function anonymizeUser(User $user): bool
	{
		try {
			DB::beginTransaction();

			$user->update([
				'twitch_id'              => 'anonymous_'.$user->id,
				'twitch_login'           => 'anonymous_user',
				'twitch_display_name'    => 'Anonymous User',
				'twitch_email'           => null,
				'twitch_access_token'    => null,
				'twitch_refresh_token'   => null,
				'twitch_avatar'          => null,
				'custom_avatar_path'     => null,
				'description'            => null,
				'preferences'            => null,
				'scopes'                 => null,
				'notifications_email'    => null,
				'notifications_web'      => false,
				'notifications_ntfy'     => false,
				'ntfy_server_url'        => null,
				'ntfy_topic'             => null,
				'ntfy_auth_token'        => null,
			]);

			// Delete activity logs
			ActivityLog::where('user_id', $user->id)->delete();

			// Delete consents
			UserConsent::where('user_id', $user->id)->delete();

			DB::commit();
			Log::info('User anonymized', ['user_id' => $user->id]);

			return true;
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error('Failed to anonymize user', [
				'user_id' => $user->id,
				'error'   => $e->getMessage(),
			]);

			return false;
		}
	}

	/**
	 * Check if user has pending deletion request.
	 *
	 * @param  User  $user
	 * @return bool
	 */
	public function hasPendingDeletion(User $user): bool
	{
		return $user->deletion_requested_at !== null;
	}

	/**
	 * Schedule user data deletion after grace period.
	 *
	 * @param  User  $user
	 * @param  int  $gracePeriodDays
	 * @return bool
	 */
	public function scheduleDataDeletion(User $user, int $gracePeriodDays = 30): bool
	{
		try {
			$user->update([
				'deletion_requested_at' => now(),
				'deletion_scheduled_at' => now()->addDays($gracePeriodDays),
			]);

			Log::info('User deletion scheduled', [
				'user_id'      => $user->id,
				'scheduled_at' => $user->deletion_scheduled_at,
			]);

			return true;
		} catch (\Exception $e) {
			Log::error('Failed to schedule user deletion', [
				'user_id' => $user->id,
				'error'   => $e->getMessage(),
			]);

			return false;
		}
	}

	/**
	 * Cancel scheduled deletion.
	 *
	 * @param  User  $user
	 * @return bool
	 */
	public function cancelDataDeletion(User $user): bool
	{
		try {
			$user->update([
				'deletion_requested_at' => null,
				'deletion_scheduled_at' => null,
			]);

			Log::info('User deletion cancelled', ['user_id' => $user->id]);

			return true;
		} catch (\Exception $e) {
			Log::error('Failed to cancel user deletion', [
				'user_id' => $user->id,
				'error'   => $e->getMessage(),
			]);

			return false;
		}
	}

	/**
	 * Get personal information.
	 *
	 * @param  User  $user
	 * @return array<string, mixed>
	 */
	private function getPersonalInformation(User $user): array
	{
		return [
			'twitch_id'           => $user->twitch_id,
			'twitch_login'        => $user->twitch_login,
			'twitch_display_name' => $user->twitch_display_name,
			'twitch_email'        => $user->twitch_email,
			'description'         => $user->description,
			'avatar_source'       => $user->avatar_source,
			'created_at'          => $user->created_at?->toIso8601String(),
			'last_login_at'       => $user->last_login_at?->toIso8601String(),
			'last_activity_at'    => $user->last_activity_at?->toIso8601String(),
		];
	}

	/**
	 * Get user roles.
	 *
	 * @param  User  $user
	 * @return array<string, bool>
	 */
	private function getUserRoles(User $user): array
	{
		return [
			'is_viewer'    => $user->is_viewer,
			'is_cutter'    => $user->is_cutter,
			'is_streamer'  => $user->is_streamer,
			'is_moderator' => $user->is_moderator,
			'is_admin'     => $user->is_admin,
		];
	}

	/**
	 * Get user submissions.
	 *
	 * @param  User  $user
	 * @return array<int, array<string, mixed>>
	 */
	private function getUserSubmissions(User $user): array
	{
		return Clip::where('submitter_id', $user->id)
			->get()
			->map(fn (Clip $clip) => [
				'twitch_clip_id' => $clip->twitch_clip_id,
				'title'          => $clip->title,
				'status'         => $clip->status->value,
				'submitted_at'   => $clip->submitted_at?->toIso8601String(),
			])
			->toArray();
	}

	/**
	 * Get user comments.
	 *
	 * @param  User  $user
	 * @return array<int, array<string, mixed>>
	 */
	private function getUserComments(User $user): array
	{
		return ClipComment::where('user_id', $user->id)
			->get()
			->map(fn (ClipComment $comment) => [
				'content'    => $comment->content,
				'created_at' => $comment->created_at->toIso8601String(),
				'clip_id'    => $comment->clip_id,
			])
			->toArray();
	}

	/**
	 * Get user votes.
	 *
	 * @param  User  $user
	 * @return array<int, array<string, mixed>>
	 */
	private function getUserVotes(User $user): array
	{
		return ClipVote::where('user_id', $user->id)
			->get()
			->map(fn (ClipVote $vote) => [
				'clip_id'    => $vote->clip_id,
				'vote_type'  => $vote->vote_type->value,
				'created_at' => $vote->created_at->toIso8601String(),
			])
			->toArray();
	}

	/**
	 * Get user activity logs.
	 *
	 * @param  User  $user
	 * @return array<int, array<string, mixed>>
	 */
	private function getUserActivityLogs(User $user): array
	{
		return ActivityLog::where('user_id', $user->id)
			->orderBy('created_at', 'desc')
			->limit(1000)
			->get()
			->map(fn (ActivityLog $log) => [
				'action'      => $log->action,
				'description' => $log->description,
				'created_at'  => $log->created_at->toIso8601String(),
				'metadata'    => $log->metadata,
			])
			->toArray();
	}

	/**
	 * Get user consents.
	 *
	 * @param  User  $user
	 * @return array<int, array<string, mixed>>
	 */
	private function getUserConsents(User $user): array
	{
		return UserConsent::where('user_id', $user->id)
			->get()
			->map(fn (UserConsent $consent) => [
				'consent_type'    => $consent->consent_type,
				'consented'       => $consent->consented,
				'consent_version' => $consent->consent_version,
				'consented_at'    => $consent->consented_at?->toIso8601String(),
			])
			->toArray();
	}

	/**
	 * Anonymize user contributions.
	 *
	 * @param  User  $user
	 * @return void
	 */
	private function anonymizeUserContributions(User $user): void
	{
		// Anonymize comments
		ClipComment::where('user_id', $user->id)->update([
			'content'    => '[Comment deleted by user]',
			'is_deleted' => true,
			'deleted_at' => now(),
		]);

		// Delete votes
		ClipVote::where('user_id', $user->id)->delete();
	}

	/**
	 * Delete personal data.
	 *
	 * @param  User  $user
	 * @return void
	 */
	private function deletePersonalData(User $user): void
	{
		// Delete activity logs
		ActivityLog::where('user_id', $user->id)->delete();

		// Delete consents
		UserConsent::where('user_id', $user->id)->delete();

		// Delete sessions
		DB::table('sessions')->where('user_id', $user->id)->delete();

		// Delete API tokens
		$user->tokens()->delete();
	}
}
