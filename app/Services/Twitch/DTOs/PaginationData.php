<?php

declare(strict_types=1);

namespace App\Services\Twitch\DTOs;

/**
 * @template T
 */
readonly class PaginationData
{
    /**
     * @param  array<T>  $data
     */
    public function __construct(
        public array $data,
        public ?string $cursor = null,
        public int $total = 0,
    ) {}

    /**
     * Check if there are more pages
     */
    public function hasMore(): bool
    {
        return $this->cursor !== null;
    }

    /**
     * Convert to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(
                fn ($item) => method_exists($item, 'toArray') ? $item->toArray() : $item,
                $this->data
            ),
            'cursor' => $this->cursor,
            'total'  => $this->total,
        ];
    }
}
