<?php

declare(strict_types=1);

namespace App\Livewire\Clips;

use App\Models\User;
use App\Services\Twitch\Contracts\ClipsInterface;
use App\Services\Twitch\TokenRefreshService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Livewire\Component;
use App\Services\CategoryService;

/**
 * Livewire component for submitting a Twitch clip.
 *
 * - Validates and resolves a Twitch clip identifier
 * - Ensures the broadcaster is registered and a streamer
 * - Enriches clip metadata (game/category, VOD)
 * - Automatically creates category with icon if needed
 * - Downloads and stores thumbnails locally (DSGVO compliant)
 */
class SubmitClip extends Component
{
    /** @var string User input (clip URL or ID) */
    public string $input = '';

    /** @var array|null Clip data after validation */
    public ?array $clip = null;

    /** @var string Status or error message */
    public string $message = '';

    /** @var bool Whether the clip is validated and ready to save */
    public bool $accepted = false;

    /** @var bool Loading state for validation */
    public bool $isChecking = false;

    /** @var bool Loading state for saving */
    public bool $isSaving = false;

    /**
     * Validate and resolve the provided clip input.
     */
    public function check(): void
    {
        $this->reset(['clip', 'message', 'accepted']);
        $this->input      = trim($this->input);
        $this->isChecking = true;


        if ($this->input === '') {
            $this->fail(__('clip.submit.messages.enter_input'));
            return;
        }

        $clipId = $this->extractClipId($this->input);
        if ($clipId === null) {
            $this->fail(__('clip.submit.messages.invalid_input'));

            return;
        }

        $user = Auth::user();
        if (! $user) {
            $this->fail(__('clip.submit.messages.only_users'));

            return;
        }

        /** @var TokenRefreshService $tokenService */
        $tokenService = app(TokenRefreshService::class);
        try {
            $token = $tokenService->getValidToken($user);

        } catch (\Throwable $e) {
            Log::warning('Token refresh failed', ['user_id' => $user->id ?? null, 'error' => $e->getMessage()]);
            $this->fail(__('clip.submit.messages.token_failed'));

            return;
        }
        if (empty($token)) {
            $this->fail(__('clip.submit.messages.token_failed'));

            return;
        }

        /** @var ClipsInterface $clipsService */
        $clipsService = app(ClipsInterface::class);
        // Set access token if required by the service implementation
        if (method_exists($clipsService, 'setAccessToken')) {
            $clipsService->setAccessToken($token);
        }
        $clip = $clipsService->getClipById($clipId);
        if ($clip === null) {
            $this->fail(__('clip.submit.messages.not_found'));

            return;
        }
        $broadcaster = User::where('twitch_id', $clip->broadcasterId)->first();
        if (! $broadcaster) {
            $this->fail(__('clip.submit.messages.broadcaster_not_registered'));

            return;
        }
        if (! $broadcaster->isStreamer() && $user->id !== $broadcaster->id) {
            $this->fail(__('clip.submit.messages.broadcaster_not_streamer'));

            return;
        }
        if ($broadcaster->allow_clip_sharing === false && $user->id !== $broadcaster->id) {
            $this->fail(__('clip.submit.messages.broadcaster_no_sharing'));

            return;
        }
        $this->clip = $clip->toArray();
        // Enrich with game/category name
        if (! empty($this->clip['game_id'] ?? null)) {
            $categoryService = app(CategoryService::class);
            $category = $categoryService->findOrCreate(
                $this->clip['game_name'] ?? 'Unknown',
                $this->clip['game_icon_url'] ?? null
            );
            try {
                $game = $clipsService->getGameById($this->clip['game_id']);
                if (! empty($game['name'])) {
                    $this->clip['game_name'] = $game['name'];
                }
            } catch (\Throwable $e) {
            }
        }
        // Enrich with VOD info
        if (! empty($this->clip['video_id'] ?? null)) {
            try {
                $video = $clipsService->getVideoById($this->clip['video_id']);
                if (! empty($video)) {
                    $this->clip['video_available'] = true;
                    $this->clip['video_url']       = 'https://www.twitch.tv/videos/'.$this->clip['video_id'];
                }
            } catch (\Throwable $e) {
            }
        }
        $this->accepted   = true;
        $this->message    = __('clip.submit.messages.validated');
        $this->isChecking = false;
    }

    /**
     * Helper to set error message and reset checking state.
     */
    private function fail(string $message): void
    {
        $this->message    = $message;
        $this->isChecking = false;
    }

    /**
     * Extracts the clip ID from a given input (URL or ID).
     */
    private function extractClipId(string $input): ?string
    {
        if (preg_match('/[?&]clip=([^&]+)/', $input, $m)) {
            return urldecode($m[1]);
        }
        if (Str::startsWith($input, ['http://', 'https://'])) {
            $path     = parse_url($input, PHP_URL_PATH) ?: '';
            $segments = array_values(array_filter(explode('/', $path)));
            $last     = end($segments);
            if ($last && preg_match('/[A-Za-z0-9_-]+-[A-Za-z0-9_-]+$/', $last)) {
                return $last;
            }
        }
        if (preg_match('/^[A-Za-z0-9_-]+-[A-Za-z0-9_-]+$/', $input)) {
            return $input;
        }

        return null;
    }

    /**
     * Save the validated clip to the database.
     */
    public function saveClip(): void
    {
        if (! $this->accepted || empty($this->clip)) {
            $this->message = __('clip.submit.messages.no_clip_to_save');

            return;
        }
        $user = Auth::user();
        if (! $user) {
            $this->fail(__('clip.submit.messages.no_clip_to_save'));

            return;
        }

        // Limit: Maximal X Clips pro User pro Tag (aus config/clip.php)
        $maxPerDay = Config::get('clip.max_per_user_per_day', 10);
        $today      = now()->startOfDay();
        $clipsToday = $user->submittedClips()
            ->where('created_at', '>=', $today)
            ->count();
        if ($clipsToday >= $maxPerDay) {
            $this->fail(__('clip.submit.messages.limit_reached'));

            return;
        }
        $this->isSaving = true;
        try {
            $broadcaster = User::where('twitch_id', $this->clip['broadcaster_id'] ?? $this->clip['broadcasterId'] ?? null)->first();
            if (! $broadcaster) {
                $this->message  = __('clip.submit.messages.broadcaster_not_registered');
                $this->isSaving = false;

                return;
            }
            // Category: create if needed, with icon
            $categoryId = null;
            if (! empty($this->clip['game_name'])) {
                $categorySlug = Str::slug($this->clip['game_name']);
                $category     = \App\Models\Category::where('slug', $categorySlug)->first();
                if (! $category) {
                    $iconPath = null;
                    if (! empty($this->clip['game_id'])) {
                        try {
                            $clipsService = app(\App\Services\Twitch\Contracts\ClipsInterface::class);
                            $game         = $clipsService->getGameById($this->clip['game_id']);
                            if (! empty($game['box_art_url'])) {
                                $gameThumbUrl = str_replace(['{width}', '{height}'], ['285', '380'], $game['box_art_url']);
                                $iconFilename = 'category-icons/'.$categorySlug.'-'.Str::random(8).'.jpg';
                                $iconData     = @file_get_contents($gameThumbUrl);
                                if ($iconData) {
                                    \Illuminate\Support\Facades\Storage::disk('public')->put($iconFilename, $iconData);
                                    $iconPath = 'storage/'.$iconFilename;
                                }
                            }
                        } catch (\Throwable $e) {
                        }
                    }
                    $category = \App\Models\Category::create([
                        'name'      => $this->clip['game_name'],
                        'slug'      => $categorySlug,
                        'icon_path' => $iconPath,
                    ]);
                }
                $categoryId = $category->id;
            }
            // Clip thumbnail: download and store locally
            $thumbnailPath = $this->clip['thumbnail_url'] ?? null;
            if ($thumbnailPath && ! Str::startsWith($thumbnailPath, ['/', 'storage/'])) {
                $filename = 'clips/thumbnails/'.($this->clip['id'] ?? Str::random(16)).'.jpg';
                try {
                    $imageData = @file_get_contents($thumbnailPath);
                    if ($imageData) {
                        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $imageData);
                        $thumbnailPath = 'storage/'.$filename;
                    }
                } catch (\Throwable $e) {
                    $thumbnailPath = null;
                }
            }
            // Save clip
            $clip = new \App\Models\Clip([
                'twitch_clip_id'   => $this->clip['id'] ?? null,
                'title'            => $this->clip['title'] ?? null,
                'description'      => $this->clip['description'] ?? null,
                'category_id'      => $categoryId,
                'thumbnail_path'   => $thumbnailPath,
                'broadcaster_id'   => $broadcaster->id,
                'submitted_by_id'  => Auth::id(),
                'is_public'        => true,
                'duration'         => $this->clip['duration'] ?? null,
                'creator_name'     => $this->clip['creator_name'] ?? $this->clip['creatorName'] ?? null,
                'game_id'          => $this->clip['game_id'] ?? null,
                'video_id'         => $this->clip['video_id'] ?? null,
                'clip_created_at'  => $this->clip['created_at'] ?? null,
            ]);
            $clip->save();
            $this->message       = __('clip.submit.messages.saved');
            $this->clip['db_id'] = $clip->id;
        } catch (\Throwable $e) {
            Log::error('Failed to save clip', ['error' => $e->getMessage(), 'clip' => $this->clip]);
            $this->message = __('clip.submit.messages.save_failed');
        }
        $this->isSaving = false;
    }

    /**
     * Helper to set input from UI (examples)
     */
    public function setInput(string $value): void
    {
        $this->input = $value;
        $this->check();
    }

    public function render()
    {
        return view('livewire.clips.submit-clip');
    }
}
