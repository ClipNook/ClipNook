# Clips Service

Handles everything related to Twitch clips. This is probably the most complex service since clips have a lot of metadata.

## What it does

- Fetch clips for a broadcaster (with pagination)
- Get individual clips by ID
- Create clips from live streams
- Enrich clips with game and video data

## Rate Limits

We have per-action rate limits because different endpoints have different limits:

```php
'rate_limit_actions' => [
    'get_clips' => ['max' => 60, 'decay' => 60],     // 60 requests per minute
    'get_clips_by_ids' => ['max' => 120, 'decay' => 60], // 120 per minute
    'create_clip' => ['max' => 10, 'decay' => 60],   // Only 10 clips per minute!
],
```

## Creating Clips

This is the tricky one. Twitch requires the broadcaster to be live, and you can only create one clip every 90 seconds or so.

```php
try {
    $result = $clipsService->createClip('123456789', true); // hasDelay = true
    // Returns: ['id' => 'AwkwardHelplessSalamanderSwiftRage', 'edit_url' => '...']
} catch (ValidationException $e) {
    // Broadcaster not live, or rate limited
}
```

## Data Enrichment

When we fetch a clip, we also grab the game name and video details to make the UI richer:

```php
$clip = $clipsService->getClipById('AwkwardHelplessSalamanderSwiftRage');
echo $clip->title;        // "Amazing play!"
echo $clip->gameId;       // "493057" (game ID)
echo $clip->videoId;      // "1234567890" (VOD ID)

// But we also call getGameById() and getVideoById() to get names
$game = $clipsService->getGameById($clip->gameId);
echo $game['name']; // "World of Warcraft"
```

This makes the clip submission form much more user-friendly.