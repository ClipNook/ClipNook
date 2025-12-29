<?php

declare(strict_types=1);

namespace App\Services\Twitch\Contracts;

use App\Services\Twitch\DTOs\TokenData;
use App\Services\Twitch\DTOs\UserData;

interface OAuthInterface
{
    /**
     * Generate OAuth authorization URL
     *
     * @param  string  $state  CSRF protection state
     * @param  array<string>  $scopes  Optional: Override default scopes
     */
    public function getAuthorizationUrl(string $state, array $scopes = []): string;

    /**
     * Exchange authorization code for access token
     *
     * @param  string  $code  Authorization code from callback
     */
    public function getAccessToken(string $code): TokenData;

    /**
     * Refresh an expired access token
     */
    public function refreshToken(string $refreshToken): TokenData;

    /**
     * Revoke an access token
     */
    public function revokeToken(string $token): bool;

    /**
     * Validate token and get user info
     */
    public function validateToken(string $token): UserData;

    /**
     * Fetch user information from Helix `/users` endpoint using access token
     */
    public function getUser(string $accessToken): UserData;

    /**
     * Fetch a specific user by Twitch id using an access token
     */
    public function getUserById(string $accessToken, string $userId): UserData;
}
