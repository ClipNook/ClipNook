<?php

declare(strict_types=1);

namespace App\Actions\Clip;

use App\Models\Clip;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApproveClipAction
{
    /**
     * Approve a clip
     */
    public function execute(Clip $clip, User $moderator): bool
    {
        return DB::transaction(function () use ($clip, $moderator) {
            $clip->approve($moderator);

            Log::info('Clip approved', [
                'clip_id'      => $clip->id,
                'moderator_id' => $moderator->id,
            ]);

            return true;
        });
    }
}
