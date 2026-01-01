<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;

/**
 * Interface for user data export and deletion (GDPR compliance).
 *
 * This contract defines methods for handling user data requests
 * in accordance with GDPR regulations.
 */
interface GDPRServiceInterface
{
	/**
	 * Export all user data in a portable format.
	 *
	 * @param  User  $user  The user whose data to export
	 * @return array{
	 *     personal_information: array<string, mixed>,
	 *     preferences: array<string, mixed>,
	 *     roles: array<string, bool>,
	 *     activity_logs: array<int, array<string, mixed>>,
	 *     exported_at: string
	 * }
	 */
	public function exportUserData(User $user): array;

	/**
	 * Permanently delete a user and all associated data.
	 *
	 * This method handles anonymization of content (comments, votes)
	 * and complete deletion of personal information.
	 *
	 * @param  User  $user  The user to delete
	 * @return bool True if deletion was successful
	 */
	public function deleteUserData(User $user): bool;

	/**
	 * Anonymize a user's data while preserving content.
	 *
	 * Removes personal information but keeps contributions
	 * attributed to an anonymous user.
	 *
	 * @param  User  $user  The user to anonymize
	 * @return bool True if anonymization was successful
	 */
	public function anonymizeUser(User $user): bool;

	/**
	 * Check if a user has requested deletion.
	 *
	 * @param  User  $user  The user to check
	 * @return bool True if deletion is pending
	 */
	public function hasPendingDeletion(User $user): bool;

	/**
	 * Schedule user data deletion after a grace period.
	 *
	 * @param  User  $user  The user to schedule for deletion
	 * @param  int  $gracePeriodDays  Days before actual deletion
	 * @return bool True if scheduling was successful
	 */
	public function scheduleDataDeletion(User $user, int $gracePeriodDays = 30): bool;

	/**
	 * Cancel a scheduled data deletion.
	 *
	 * @param  User  $user  The user whose deletion to cancel
	 * @return bool True if cancellation was successful
	 */
	public function cancelDataDeletion(User $user): bool;
}
