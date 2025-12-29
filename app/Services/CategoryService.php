<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    /**
     * Find or create a category by name, download and store icon if provided.
     *
     * @param string $name
     * @param string|null $iconUrl
     * @param string|null $description
     * @return Category
     */
    public function findOrCreate(string $name, ?string $iconUrl = null, ?string $description = null): Category
    {
        $slug = Str::slug($name);
        $category = Category::where('slug', $slug)->first();
        if ($category) {
            return $category;
        }

        $iconPath = null;
        if ($iconUrl) {
            try {
                $iconPath = $this->downloadAndStoreIcon($iconUrl, $slug);
            } catch (\Throwable $e) {
                Log::warning('Category icon download failed', [
                    'url' => $iconUrl,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return Category::create([
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
            'icon_path'   => $iconPath,
        ]);
    }

    /**
     * Download and store the icon locally, return the storage path.
     *
     * @param string $url
     * @param string $slug
     * @return string|null
     */
    public function downloadAndStoreIcon(string $url, string $slug): ?string
    {
        $contents = @file_get_contents($url);
        if (! $contents) {
            throw new \RuntimeException('Failed to download icon from: ' . $url);
        }
        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
        $filename = 'categories/icons/' . $slug . '-' . uniqid() . '.' . $ext;
        Storage::disk('public')->put($filename, $contents);
        return 'storage/' . $filename;
    }
}
