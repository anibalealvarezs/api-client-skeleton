<?php

namespace Anibalealvarezs\ApiSkeleton\Clients;

use Anibalealvarezs\ApiSkeleton\Client;
use Anibalealvarezs\ApiSkeleton\Enums\DelayUnit;
use GuzzleHttp\Exception\GuzzleException;

class ApiKeyClient extends Client
{

    /**
     * @param string $baseUrl
     * @param string $apiKey
     * @param array $authSettings
     * @param array $defaultHeaders
     * @param string|null $delayHeader
     * @param DelayUnit $delayUnit
     * @throws GuzzleException
     */
    function __construct(
        string $baseUrl,
        string $apiKey,
        array $authSettings,
        array $defaultHeaders = [],
        ?string $delayHeader = null,
        DelayUnit $delayUnit = DelayUnit::second,
    ) {
        return parent::__construct(
            baseUrl: $baseUrl,
            defaultHeaders: $defaultHeaders,
            apiKey: $apiKey,
            authSettings: $authSettings,
            delayHeader: $delayHeader,
            delayUnit: $delayUnit,
        );
    }
}