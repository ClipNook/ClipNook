<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RotateIpSalts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gdpr:rotate-ip-salts {--cleanup : Also cleanup old salts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rotate IP pseudonymization salts for enhanced GDPR compliance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting IP salt rotation...');

        // Get current active salt info
        $currentSalt = IpPseudonymizationSalt::where('is_active', true)->first();
        if ($currentSalt) {
            $this->info("Current salt active since: {$currentSalt->valid_from->format('Y-m-d H:i:s')}");
        }

        // Rotate to new salt
        $newSalt = IpPseudonymizationSalt::rotateSalt();

        $this->info('Successfully rotated to new salt');
        $this->info("New salt ID: {$newSalt->id}");
        $this->info("New salt valid from: {$newSalt->valid_from->format('Y-m-d H:i:s')}");

        // Optional cleanup
        if ($this->option('cleanup')) {
            $this->info('Cleaning up old salts...');
            $deletedCount = IpPseudonymizationSalt::cleanupOldSalts();
            $this->info("Cleaned up {$deletedCount} old salts");
        }

        // Log the rotation
        \Illuminate\Support\Facades\Log::info('IP pseudonymization salt rotated', [
            'old_salt_id' => $currentSalt?->id,
            'new_salt_id' => $newSalt->id,
            'timestamp'   => now(),
        ]);

        $this->info('IP salt rotation completed successfully');

        return self::SUCCESS;
    }
}
