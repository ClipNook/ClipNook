<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Console\Command;

use function now;

final class AutoCleanupGdprData extends Command
{
    protected $signature = 'gdpr:auto-cleanup';

    protected $description = 'Automatically cleanup old GDPR data';

    public function handle(): int
    {
        $this->info('Starting automatic GDPR cleanup...');

        // Delete activity logs older than 90 days
        $deletedLogs = ActivityLog::where('created_at', '<', now()->subDays(90))
            ->delete();

        $this->info("Deleted {$deletedLogs} old activity logs");

        // Anonymize inactive users (2+ years)
        $inactiveUsers = User::where('last_activity_at', '<', now()->subYears(2))
            ->whereNull('anonymized_at')
            ->whereNull('deletion_requested_at')
            ->get();

        foreach ($inactiveUsers as $user) {
            $user->update([
                'twitch_id'            => 'anon_'.$user->id,
                'twitch_login'         => 'deleted',
                'twitch_display_name'  => 'Deleted User',
                'twitch_email'         => null,
                'twitch_access_token'  => null,
                'twitch_refresh_token' => null,
                'anonymized_at'        => now(),
            ]);
        }

        $this->info("Anonymized {$inactiveUsers->count()} inactive users");

        return Command::SUCCESS;
    }
}
