<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function in_array;
use function strtolower;

final class SetUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:role
						{identifier : User ID or Twitch login}
						{--role=moderator : Role to set (admin or moderator)}
						{--remove : Remove the role instead of adding it}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Set a user as admin or moderator';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $identifier = $this->argument('identifier');
        $role       = strtolower($this->option('role'));
        $remove     = $this->option('remove');

        // Validate identifier
        if (empty($identifier)) {
            $this->error('Identifier cannot be empty');

            return self::FAILURE;
        }

        // Validate role
        if (! in_array($role, ['admin', 'moderator'], true)) {
            $this->error('Role must be either "admin" or "moderator"');

            return self::FAILURE;
        }

        // Find user by ID or Twitch login
        $user = User::where('id', $identifier)
            ->orWhere('twitch_login', $identifier)
            ->first();

        if (! $user) {
            $this->error("User not found: {$identifier}");

            return self::FAILURE;
        }

        // Confirm admin operations
        if ($role === 'admin' && ! $remove) {
            if (! $this->confirm("Are you sure you want to grant admin privileges to {$user->twitch_display_name} (@{$user->twitch_login})?", false)) {
                $this->info('Operation cancelled.');

                return self::SUCCESS;
            }
        }

        // Set or remove role
        $roleColumn = $role === 'admin' ? 'is_admin' : 'is_moderator';

        $user->update([$roleColumn => ! $remove]);

        // Show result message
        if ($remove) {
            $this->info("Removed {$role} role from {$user->twitch_display_name} (@{$user->twitch_login})");
        } else {
            $this->info("Set {$role} role for {$user->twitch_display_name} (@{$user->twitch_login})");
        }

        return self::SUCCESS;
    }
}
