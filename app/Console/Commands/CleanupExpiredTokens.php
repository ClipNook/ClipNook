<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:cleanup {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired API tokens from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of expired tokens...');

        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No tokens will be actually deleted');
        }

        // Count expired tokens before cleanup
        $expiredTokensCount = \Laravel\Sanctum\PersonalAccessToken::where('expires_at', '<', now())->count();

        $this->info("Found {$expiredTokensCount} expired tokens");

        if ($expiredTokensCount === 0) {
            $this->info('No expired tokens to clean up.');

            return self::SUCCESS;
        }

        if ($isDryRun) {
            $this->table(
                ['Token ID', 'Name', 'User ID', 'Expired At'],
                \Laravel\Sanctum\PersonalAccessToken::where('expires_at', '<', now())
                    ->with('tokenable:id,twitch_display_name')
                    ->get()
                    ->map(function ($token) {
                        return [
                            $token->id,
                            $token->name,
                            $token->tokenable->twitch_display_name ?? 'Unknown',
                            $token->expires_at?->format('Y-m-d H:i:s') ?? 'Never',
                        ];
                    })
            );
        } else {
            // Perform actual cleanup
            $deletedCount = \Laravel\Sanctum\PersonalAccessToken::where('expires_at', '<', now())->delete();

            $this->info("Successfully deleted {$deletedCount} expired tokens");

            // Log the cleanup
            \Illuminate\Support\Facades\Log::info('Expired tokens cleanup completed', [
                'deleted_count' => $deletedCount,
                'timestamp'     => now(),
            ]);
        }

        return self::SUCCESS;
    }
}
