<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Throwable;

class Notifications extends Component
{
    public int $unread = 0;

    public function mount(): void
    {
        try {
            // If notifications table doesn't exist yet (fresh install), avoid querying it
            if (! Schema::hasTable('notifications')) {
                $this->unread = 0;

                return;
            }

            $user = Auth::user();

            if ($user) {
                // Query unread count (do not eager-load all notifications)
                $this->unread = $user->unreadNotifications()->count();
            }
        } catch (Throwable $e) {
            // Fail gracefully and log the issue
            Log::warning('Failed to fetch unread notification count', ['message' => $e->getMessage()]);
            $this->unread = 0;
        }
    }

    /**
     * Optionally update unread count on poll
     */
    public function render()
    {
        return view('livewire.notifications', [
            'unread' => $this->unread,
        ]);
    }
}
