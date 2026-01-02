<?php

declare(strict_types=1);

namespace App\Livewire\Settings\Tabs;

use App\Actions\GDPR\ExportUserDataAction;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

use function __;
use function app;
use function route;
use function view;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_UNICODE;

final class PrivacyTab extends Component
{
    public bool $confirmDeleteClips = false;

    public bool $confirmDeleteComments = false;

    public bool $confirmDeleteRatings = false;

    public bool $confirmDeleteAccount = false;

    public function exportData(): void
    {
        try {
            $user         = Auth::user();
            $exportAction = app(ExportUserDataAction::class);

            $data = $exportAction->execute($user);

            $this->dispatch('download-data', data: json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->dispatch('notify', type: 'success', message: __('Data export prepared. Download will start shortly.'));
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error exporting data: :error', ['error' => $e->getMessage()]));
        }
    }

    public function deleteAllClips(): void
    {
        if (! $this->confirmDeleteClips) {
            $this->dispatch('notify', type: 'error', message: __('Please confirm the deletion.'));

            return;
        }

        try {
            $user = Auth::user();

            DB::transaction(static function () use ($user): void {
                $user->broadcasterClips()->delete();
            });

            $this->confirmDeleteClips = false;
            $this->dispatch('notify', type: 'success', message: __('All clips deleted.'));
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error deleting clips: :error', ['error' => $e->getMessage()]));
        }
    }

    public function deleteAllComments(): void
    {
        if (! $this->confirmDeleteComments) {
            $this->dispatch('notify', type: 'error', message: __('Please confirm the deletion.'));

            return;
        }

        try {
            $user = Auth::user();

            DB::transaction(static function () use ($user): void {
                $user->clipComments()->delete();
            });

            $this->confirmDeleteComments = false;
            $this->dispatch('notify', type: 'success', message: __('All comments deleted.'));
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error deleting comments: :error', ['error' => $e->getMessage()]));
        }
    }

    public function deleteAllRatings(): void
    {
        if (! $this->confirmDeleteRatings) {
            $this->dispatch('notify', type: 'error', message: __('Please confirm the deletion.'));

            return;
        }

        try {
            $user = Auth::user();

            DB::transaction(static function () use ($user): void {
                $user->clipVotes()->delete();
            });

            $this->confirmDeleteRatings = false;
            $this->dispatch('notify', type: 'success', message: __('All ratings deleted.'));
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error deleting ratings: :error', ['error' => $e->getMessage()]));
        }
    }

    public function deleteAccount(): void
    {
        if (! $this->confirmDeleteAccount) {
            $this->dispatch('notify', type: 'error', message: __('Please confirm account deletion.'));

            return;
        }

        try {
            $user = Auth::user();

            DB::transaction(static function () use ($user): void {
                // Alle zugehörigen Daten löschen
                $user->broadcasterClips()->delete();
                $user->submittedClips()->delete();
                $user->clipComments()->delete();
                $user->clipVotes()->delete();
                $user->broadcasterSettings()->delete();
                $user->tokens()->delete();

                // Account löschen
                $user->delete();
            });

            Auth::logout();
            $this->redirect(route('home'));
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error deleting account: :error', ['error' => $e->getMessage()]));
        }
    }

    public function render()
    {
        return view('livewire.settings.tabs.privacy-tab', [
            'user' => Auth::user(),
        ]);
    }
}
