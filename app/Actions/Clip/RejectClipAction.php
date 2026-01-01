<?php

declare(strict_types=1);

namespace App\Actions\Clip;

use App\Models\Clip;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RejectClipAction
{
    /**
     * Reject a clip with a reason
     */
    public function execute(Clip $clip, User $moderator, string $reason): bool
    {
        return DB::transaction(function () use ($clip, $moderator, $reason) {
            $clip->reject($reason, $moderator);
            Log::info('Clip rejected', [
                'clip_id'      => $clip->id,
                'moderator_id' => $moderator->id,
                'reason'       => $reason,
            ]);

            return true;
        });
    }
}

namespace App\Actions\Clip;

class RejectClipAction {}
