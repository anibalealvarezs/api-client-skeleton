<?php

namespace Anibalealvarezs\ApiSkeleton\Clients;

use Anibalealvarezs\ApiSkeleton\Client;
use Anibalealvarezs\ApiSkeleton\Enums\AuthType;
use Anibalealvarezs\ApiSkeleton\Enums\DelayUnit;
use GuzzleHttp\Exception\GuzzleException;

class NoAuthClient extends Client
{

    /**
     * @param string $baseUrl
     * @param array $defaultHeaders
     * @param string $userId
     * @param array $scopes
     * @param string $token
     * @param string|null $delayHeader
     * @param DelayUnit $delayUnit
     * @throws GuzzleException
     */
    function __construct(
        string $baseUrl,
        array $defaultHeaders = [],
        string $userId = "",
        array $scopes = [],
        string $token = "",
        ?string $delayHeader = null,
        DelayUnit $delayUnit = DelayUnit::second,
    ) {
        return parent::__construct(
            baseUrl: $baseUrl,
            defaultHeaders: $defaultHeaders,
            userId: $userId,
            scopes: $scopes,
            token: $token,
            authType: AuthType::none,
            delayHeader: $delayHeader,
            delayUnit: $delayUnit,
        );
    }
}