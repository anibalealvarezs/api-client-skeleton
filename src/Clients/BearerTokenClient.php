<?php

namespace Anibalealvarezs\ApiSkeleton\Clients;

use Anibalealvarezs\ApiSkeleton\Client;
use Anibalealvarezs\ApiSkeleton\Enums\AuthType;
use Anibalealvarezs\ApiSkeleton\Enums\DelayUnit;
use GuzzleHttp\Exception\GuzzleException;

class BearerTokenClient extends Client
{

    /**
     * @param string $baseUrl
     * @param string $token
     * @param array $authSettings
     * @param array $defaultHeaders
     * @param string|null $delayHeader
     * @param DelayUnit $delayUnit
     * @throws GuzzleException
     */
    function __construct(
        string $baseUrl,
        string $token,
        array $authSettings = [],
        array $defaultHeaders = [],
        ?string $delayHeader = null,
        DelayUnit $delayUnit = DelayUnit::second,
    ) {
        return parent::__construct(
            baseUrl: $baseUrl,
            defaultHeaders: $defaultHeaders,
            token: $token,
            authType: AuthType::bearerToken,
            authSettings: $authSettings,
            delayHeader: $delayHeader,
            delayUnit: $delayUnit,
        );
    }
}