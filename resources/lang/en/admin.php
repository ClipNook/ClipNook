<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the admin interface
    | for clip moderation and administration tasks.
    |
    */

    'clip_moderation'             => 'Clip Moderation',
    'clip_moderation_description' => 'Review, approve, reject, and manage submitted clips',

    // Stats
    'pending_clips'  => 'Pending',
    'approved_clips' => 'Approved',
    'rejected_clips' => 'Rejected',
    'flagged_clips'  => 'Flagged',

    // Filters
    'filter_by_status'   => 'Filter by Status',
    'all_clips'          => 'All Clips',
    'pending'            => 'Pending',
    'approved'           => 'Approved',
    'rejected'           => 'Rejected',
    'flagged'            => 'Flagged',
    'search'             => 'Search',
    'search_placeholder' => 'Search by title, ID, broadcaster, or submitter...',

    // Table Headers
    'clip'        => 'Clip',
    'broadcaster' => 'Broadcaster',
    'submitter'   => 'Submitter',
    'status'      => 'Status',
    'submitted'   => 'Submitted',
    'actions'     => 'Actions',

    // Actions
    'approve'   => 'Approve',
    'reject'    => 'Reject',
    'feature'   => 'Feature',
    'unfeature' => 'Unfeature',
    'delete'    => 'Delete',
    'featured'  => 'Featured',

    // Modal
    'reject_clip'                   => 'Reject Clip',
    'rejection_reason'              => 'Rejection Reason',
    'rejection_reason_placeholder'  => 'Please provide a detailed reason for rejecting this clip...',
    'cancel'                        => 'Cancel',

    // Messages
    'no_clips_found'        => 'No clips found',
    'try_different_filter'  => 'Try adjusting your filters or search query',
    'clip_approved'         => 'Clip has been approved successfully',
    'clip_rejected'         => 'Clip has been rejected',
    'clip_deleted'          => 'Clip has been deleted',
    'clip_featured_toggled' => 'Featured status updated',
    'confirm_delete'        => 'Are you sure you want to delete this clip? This action cannot be undone.',
];
