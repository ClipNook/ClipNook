<?php

declare(strict_types=1);

namespace App\Actions\Clip;

use App\Models\Clip;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class DeleteClipAction
{
    /**
     * Delete a clip and its associated files.
     */
    public function execute(Clip $clip, User $user): bool
    {
        return DB::transaction(static function () use ($clip, $user) {
            // Delete local thumbnail if exists
            if ($clip->local_thumbnail_path) {
                Storage::disk('public')->delete($clip->local_thumbnail_path);
            }

            $clipId = $clip->id;
            $clip->delete();

            Log::info('Clip deleted', [
                'user_id' => $user->id,
                'clip_id' => $clipId,
            ]);

            return true;
        });
    }
}
