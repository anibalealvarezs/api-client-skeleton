<?php

namespace Anibalealvarezs\ApiSkeleton\Clients;

use Anibalealvarezs\ApiSkeleton\Client;
use Anibalealvarezs\ApiSkeleton\Enums\AuthType;
use Anibalealvarezs\ApiSkeleton\Enums\DelayUnit;
use Exception;
use GuzzleHttp\Client as GuzzleClient;

class OAuthV2Client extends Client
{

    /**
     * @param string $baseUrl
     * @param string $authUrl
     * @param string $tokenUrl
     * @param string $refreshAuthUrl
     * @param string $redirectUrl
     * @param string $clientId
     * @param string $clientSecret
     * @param string $refreshToken
     * @param array $authSettings
     * @param array $defaultHeaders
     * @param array $refreshTokenHeaders
     * @param string $userId
     * @param array $scopes
     * @param string $token
     * @param string|null $delayHeader
     * @param DelayUnit $delayUnit
     * @param GuzzleClient|null $guzzleClient
     * @throws Exception
     */
    function __construct(
        string $baseUrl,
        string $authUrl,
        string $tokenUrl,
        string $refreshAuthUrl,
        string $redirectUrl,
        string $clientId,
        string $clientSecret,
        string $refreshToken,
        array $authSettings,
        array $defaultHeaders = [],
        array $refreshTokenHeaders = ["Content-Type" => "application/json"],
        string $userId = "",
        array $scopes = [],
        string $token = "",
        ?string $delayHeader = null,
        DelayUnit $delayUnit = DelayUnit::second,
        ?GuzzleClient $guzzleClient = null,
    ) {
        parent::__construct(
            baseUrl: $baseUrl,
            guzzleClient: $guzzleClient,
            authUrl: $authUrl,
            refreshAuthUrl: $refreshAuthUrl,
            tokenUrl: $tokenUrl,
            redirectUrl: $redirectUrl,
            clientId: $clientId,
            clientSecret: $clientSecret,
            defaultHeaders: $defaultHeaders,
            refreshToken: $refreshToken,
            refreshTokenHeaders: $refreshTokenHeaders,
            userId: $userId,
            scopes: $scopes,
            token: $token,
            authType: AuthType::oAuthV2,
            authSettings: $authSettings,
            delayHeader: $delayHeader,
            delayUnit: $delayUnit,
        );
    }
}