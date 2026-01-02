<?php

declare(strict_types=1);

namespace App\Livewire\Settings\Tabs;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

use function __;
use function session;
use function view;

final class SessionsTab extends Component
{
    public function revokeToken(int $tokenId): void
    {
        try {
            $user = Auth::user();
            $user->tokens()->where('id', $tokenId)->delete();

            $this->dispatch('notify', type: 'success', message: __('Token revoked successfully.'));
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error revoking token: :error', ['error' => $e->getMessage()]));
        }
    }

    public function revokeAllTokens(): void
    {
        try {
            $user         = Auth::user();
            $currentToken = $user->currentAccessToken();

            // Alle Tokens außer dem aktuellen löschen
            $user->tokens()->where('id', '!=', $currentToken?->id)->delete();

            $this->dispatch('notify', type: 'success', message: __('All other sessions revoked.'));
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error revoking sessions: :error', ['error' => $e->getMessage()]));
        }
    }

    public function logoutOtherDevices(): void
    {
        try {
            $user             = Auth::user();
            $currentSessionId = session()->getId();

            // Alle anderen Sessions löschen
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $currentSessionId)
                ->delete();

            $this->dispatch('notify', type: 'success', message: __('Logged out of all other devices.'));
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error logging out: :error', ['error' => $e->getMessage()]));
        }
    }

    public function render()
    {
        $user = Auth::user();

        return view('livewire.settings.tabs.sessions-tab', [
            'user'     => $user,
            'tokens'   => $user->tokens()->orderBy('created_at', 'desc')->get(),
            'sessions' => DB::table('sessions')
                ->where('user_id', $user->id)
                ->orderBy('last_activity', 'desc')
                ->get()
                ->map(static fn ($session) => (object) [
                    'id'            => $session->id,
                    'ip_address'    => $session->ip_address,
                    'user_agent'    => $session->user_agent,
                    'last_activity' => Carbon::createFromTimestamp($session->last_activity),
                    'is_current'    => $session->id === session()->getId(),
                ]),
        ]);
    }
}
