<?php

namespace Anibalealvarezs\ApiSkeleton\Clients;

use Anibalealvarezs\ApiSkeleton\Client;
use Anibalealvarezs\ApiSkeleton\Enums\AuthType;
use Anibalealvarezs\ApiSkeleton\Enums\DelayUnit;
use Anibalealvarezs\ApiSkeleton\Enums\EncodingMethod;
use GuzzleHttp\Exception\GuzzleException;

class BasicClient extends Client
{

    /**
     * @param string $baseUrl
     * @param string $username
     * @param string $password
     * @param array $authSettings
     * @param EncodingMethod $encodingMethod
     * @param array $defaultHeaders
     * @param string|null $delayHeader
     * @param DelayUnit $delayUnit
     * @throws GuzzleException
     */
    function __construct(
        string $baseUrl,
        string $username,
        string $password,
        array $authSettings = [],
        EncodingMethod $encodingMethod = EncodingMethod::none,
        array $defaultHeaders = [],
        ?string $delayHeader = null,
        DelayUnit $delayUnit = DelayUnit::second,
    ) {
        return parent::__construct(
            baseUrl: $baseUrl,
            defaultHeaders: $defaultHeaders,
            token: $this->encodeCredentials($username, $password, $encodingMethod),
            authType: AuthType::basic,
            authSettings: $authSettings,
            delayHeader: $delayHeader,
            delayUnit: $delayUnit,
        );
    }

    /**
     * @param string $username
     * @param string $password
     * @param EncodingMethod $encodingMethod
     * @return string
     */
    protected function encodeCredentials(string $username, string $password, EncodingMethod $encodingMethod): string
    {
        return match ($encodingMethod) {
            EncodingMethod::base64 => base64_encode($username . ":" . $password),
            EncodingMethod::url => urlencode($username) . ":" . urlencode($password),
            default => $username . ":" . $password,
        };
    }
}