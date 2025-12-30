<?php

namespace App\Services\Twitch\Contracts;

interface DownloadInterface
{
    public function downloadThumbnail(string $url, string $savePath): bool;

    public function downloadProfileImage(string $url, string $savePath): bool;
}
