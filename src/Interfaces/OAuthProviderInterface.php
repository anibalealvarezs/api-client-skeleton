<?php

declare(strict_types=1);

namespace Anibalealvarezs\ApiSkeleton\Interfaces;

/**
 * Interface OAuthProviderInterface
 * Defines the contract for an OAuth2 Authentication Provider
 */
interface OAuthProviderInterface
{
    /**
     * Get the authorization URL to start the OAuth flow.
     *
     * @param string $redirectUri
     * @param array $config (including scopes, custom params)
     * @return string
     */
    public function getAuthUrl(string $redirectUri, array $config = []): string;

    /**
     * Handle the OAuth callback and return the normalized token data.
     *
     * @param string $code
     * @param string $redirectUri
     * @return array {access_token: string, refresh_token?: string, user_id?: string, scopes?: array, expires_in?: int}
     */
    public function handleCallback(string $code, string $redirectUri): array;
}
