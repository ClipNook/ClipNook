# Data Transfer Objects (DTOs)

We use readonly classes for all API responses. This gives us type safety and prevents accidental mutation.

## Why DTOs?

- Type safety without arrays
- IDE autocompletion
- Immutable (readonly)
- Easy to serialize/debug

## ClipData

The most complex one - clips have tons of metadata:

```php
readonly class ClipData {
    public function __construct(
        public string $id,
        public string $url,
        public string $embedUrl,
        public string $broadcasterId,
        public string $broadcasterName,
        // ... 10 more fields
        public bool $isFeatured = false,
    ) {}
}
```

## PaginationData

For paginated responses:

```php
readonly class PaginationData {
    public function __construct(
        public array $data,           // array of DTOs
        public ?string $cursor,       // for next page
        public int $total = 0,        // if available
    ) {}
}
```

## From Array

Each DTO has a `fromArray()` static method:

```php
public static function fromArray(array $data): self {
    return new self(
        id: $data['id'],
        url: $data['url'],
        // ... map all fields
        createdAt: new DateTimeImmutable($data['created_at']),
    );
}
```

## Usage

```php
$clips = $clipsService->getClips('123');
foreach ($clips->data as $clip) {
    // $clip is ClipData instance
    echo $clip->title; // typed property
    echo $clip->viewCount; // int, not string
}
```

Much safer than working with raw arrays from the API.