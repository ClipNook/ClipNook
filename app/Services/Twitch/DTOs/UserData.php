<?php

declare(strict_types=1);

namespace App\Services\Twitch\DTOs;

readonly class UserData
{
    public function __construct(
        public string $id,
        public string $login,
        public string $displayName,
        public string $email,
        public string $profileImageUrl,
        public string $broadcasterType,
        public string $type,
        public array $scopes = [],
    ) {}

    /**
     * Create from API response
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        // Use array_key_exists to avoid PHP notices when keys are missing
        $id              = array_key_exists('user_id', $data) ? (string) $data['user_id'] : (string) ($data['id'] ?? '');
        $login           = $data['login'] ?? ($data['user_login'] ?? '');
        $displayName     = $data['display_name'] ?? ($data['displayName'] ?? $login);
        $email           = $data['email'] ?? '';
        $profileImageUrl = $data['profile_image_url'] ?? ($data['profileImageUrl'] ?? '');
        $broadcasterType = $data['broadcaster_type'] ?? '';
        $type            = $data['type'] ?? '';
        $scopes          = $data['scopes'] ?? [];

        return new self(
            id: $id,
            login: $login,
            displayName: $displayName,
            email: $email,
            profileImageUrl: $profileImageUrl,
            broadcasterType: $broadcasterType,
            type: $type,
            scopes: $scopes,
        );
    }

    /**
     * Convert to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'login'             => $this->login,
            'display_name'      => $this->displayName,
            'email'             => $this->email,
            'profile_image_url' => $this->profileImageUrl,
            'broadcaster_type'  => $this->broadcasterType,
            'type'              => $this->type,
            'scopes'            => $this->scopes,
        ];
    }
}
