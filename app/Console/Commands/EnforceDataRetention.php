<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Console\Command;

class EnforceDataRetention extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'gdpr:enforce-retention {--dry-run : Run without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Enforce GDPR data retention policies - delete old activity logs and anonymize inactive users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🗂️ Enforcing data retention policies...');

        // Delete old activity logs (>90 days)
        $cutoffDate   = now()->subDays(90);
        $oldLogsCount = ActivityLog::where('created_at', '<', $cutoffDate)->count();

        if ($dryRun) {
            $this->info("📊 Would delete {$oldLogsCount} activity logs older than 90 days");
        } else {
            $deletedLogs = ActivityLog::where('created_at', '<', $cutoffDate)->delete();
            $this->info("🗑️ Deleted {$deletedLogs} old activity logs");
        }

        // Anonymize inactive user accounts (>2 years)
        $inactiveCutoff = now()->subYears(2);
        $inactiveUsers  = User::where('last_activity_at', '<', $inactiveCutoff)
            ->whereNull('deleted_at')
            ->get();

        if ($inactiveUsers->isEmpty()) {
            $this->info('✅ No inactive users found for anonymization');
        } else {
            $this->warn("🚨 Found {$inactiveUsers->count()} inactive users (>2 years):");

            foreach ($inactiveUsers as $user) {
                $this->line("   - {$user->twitch_login} (last activity: {$user->last_activity_at?->format('Y-m-d')})");

                if (! $dryRun) {
                    $this->info("   📝 Anonymizing user: {$user->twitch_login}");
                    app(\App\Actions\GDPR\DeleteUserDataAction::class)->execute($user, true);
                }
            }

            if (! $dryRun) {
                $this->info("✅ Anonymized {$inactiveUsers->count()} inactive users");
            }
        }

        // Clean up orphaned data
        $this->cleanupOrphanedData($dryRun);

        $this->info('🎉 Data retention enforcement completed');

        return Command::SUCCESS;
    }

    /**
     * Clean up orphaned data.
     */
    private function cleanupOrphanedData(bool $dryRun): void
    {
        // Clean up activity logs for deleted users
        $orphanedLogs = ActivityLog::whereDoesntHave('user')->count();

        if ($orphanedLogs > 0) {
            if ($dryRun) {
                $this->info("📊 Would delete {$orphanedLogs} orphaned activity logs");
            } else {
                ActivityLog::whereDoesntHave('user')->delete();
                $this->info("🧹 Cleaned up {$orphanedLogs} orphaned activity logs");
            }
        }
    }
}
