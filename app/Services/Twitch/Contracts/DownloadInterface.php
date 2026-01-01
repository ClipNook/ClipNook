<?php

declare(strict_types=1);

namespace App\Services\Twitch\Contracts;

interface DownloadInterface
{
    public function downloadThumbnail(string $url, string $savePath): bool;

    public function downloadProfileImage(string $url, string $savePath): bool;

    public function downloadBoxArt(string $url, string $savePath): bool;
}
