<?php

namespace Anibalealvarezs\ApiSkeleton\Clients;

use Anibalealvarezs\ApiSkeleton\Client;
use Anibalealvarezs\ApiSkeleton\Enums\AuthType;
use Anibalealvarezs\ApiSkeleton\Enums\DelayUnit;
use Exception;
use GuzzleHttp\Client as GuzzleClient;

class BearerTokenClient extends Client
{

    /**
     * @param string $baseUrl
     * @param string $token
     * @param array $authSettings
     * @param array $defaultHeaders
     * @param string|null $delayHeader
     * @param DelayUnit $delayUnit
     * @param GuzzleClient|null $guzzleClient
     * @throws Exception
     */
    function __construct(
        string $baseUrl,
        string $token,
        array $authSettings = [],
        array $defaultHeaders = [],
        ?string $delayHeader = null,
        DelayUnit $delayUnit = DelayUnit::second,
        ?GuzzleClient $guzzleClient = null,
    ) {
        return parent::__construct(
            baseUrl: $baseUrl,
            guzzleClient: $guzzleClient,
            defaultHeaders: $defaultHeaders,
            token: $token,
            authType: AuthType::bearerToken,
            authSettings: $authSettings,
            delayHeader: $delayHeader,
            delayUnit: $delayUnit,
        );
    }
}