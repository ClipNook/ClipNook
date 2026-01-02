<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\DownloadTwitchImage;
use App\Models\Game;
use Illuminate\Console\Command;

use function str_replace;

final class DispatchMissingBoxArtDownloads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dispatch-missing-box-art-downloads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch box art download jobs for games that are missing local box art';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $games = Game::whereNotNull('box_art_url')
            ->whereNull('local_box_art_path')
            ->get();

        $this->info("Found {$games->count()} games without local box art.");

        foreach ($games as $game) {
            $boxArtUrl = str_replace(
                ['{width}', '{height}'],
                ['285', '380'],
                $game->box_art_url
            );
            $boxArtPath = 'games/box-art/'.$game->id.'.jpg';

            DownloadTwitchImage::dispatch($boxArtUrl, $boxArtPath, 'box_art', null, $game->id);
            $this->line("Dispatched download for game: {$game->name}");
        }

        $this->info('All missing box art downloads have been dispatched.');
    }
}
