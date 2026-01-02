<?php

declare(strict_types=1);

namespace App\Actions\GDPR;

use App\Models\User;

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
            'tokens',
        ]);

        return [
            'user'                 => $user->toArray(),
            'broadcaster_clips'    => $user->broadcasterClips->toArray(),
            'submitted_clips'      => $user->submittedClips->toArray(),
            'clip_comments'        => $user->clipComments->toArray(),
            'clip_votes'           => $user->clipVotes->toArray(),
            'broadcaster_settings' => $user->broadcasterSettings?->toArray() ?? [],
            'tokens'               => $user->tokens->toArray(),
        ];
    }
}
