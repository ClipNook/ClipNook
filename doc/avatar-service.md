# Avatar Service

Handles user avatars from Twitch. We give users control over their avatars for privacy reasons.

## Privacy First

Users can:
- Use their Twitch avatar
- Upload a custom avatar
- Disable avatars entirely
- Restore from Twitch if they change it

## Storage Options

Avatars can be stored in multiple ways:

1. **Twitch URL**: Just store the URL, no local storage
2. **Local Storage**: Download and store in `storage/app/public/avatars/`
3. **Disabled**: Show default SVG avatar

## Upload Flow

```php
$avatarService = app(AvatarService::class);

// User uploads file
$request->validate([
    'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
]);

$path = $request->file('avatar')->store('avatars', 'public');
$user->update(['custom_avatar_path' => $path]);
```

## Restore from Twitch

If user changes their Twitch avatar, they can restore it:

```php
try {
    $avatarService->restoreFromTwitch($user, $accessToken);
    // Downloads and stores locally
} catch (ValidationException $e) {
    // No avatar on Twitch
}
```

## Cleanup

When deleting users, we clean up their avatar files:

```php
// In User model
protected static function booted(): void {
    static::deleting(function (User $user) {
        $user->deleteAvatar();
    });
}
```

## Default Avatar

We have a fallback SVG for when avatars are disabled:

```php
public function getAvatarUrlAttribute(): string {
    if ($this->isAvatarDisabled()) {
        return asset('images/avatar-default.svg');
    }
    // ... rest of logic
}
```

This gives users full control over their visual representation.