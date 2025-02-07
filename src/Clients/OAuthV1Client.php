<?php

namespace Anibalealvarezs\ApiSkeleton\Clients;

use Anibalealvarezs\ApiSkeleton\Client;
use Anibalealvarezs\ApiSkeleton\Enums\AuthType;
use Anibalealvarezs\ApiSkeleton\Enums\DelayUnit;
use Anibalealvarezs\OAuthV1\Enums\SignatureMethod;
use GuzzleHttp\Exception\GuzzleException;

class OAuthV1Client extends Client
{

    /**
     * @param string $baseUrl
     * @param string $consumerId
     * @param string $consumerSecret
     * @param string $token
     * @param string $tokenSecret
     * @param array $defaultHeaders
     * @param string $version
     * @param string $realm
     * @param SignatureMethod $signatureMethod
     * @param string|null $delayHeader
     * @param DelayUnit $delayUnit
     * @throws GuzzleException
     */
    function __construct(
        string $baseUrl,
        string $consumerId,
        string $consumerSecret,
        string $token,
        string $tokenSecret,
        array $defaultHeaders = [],
        string $version = "1.0",
        string $realm = "",
        SignatureMethod $signatureMethod = SignatureMethod::HMAC_SHA1,
        ?string $delayHeader = null,
        DelayUnit $delayUnit = DelayUnit::second,
    ) {
        return parent::__construct(
            baseUrl: $baseUrl,
            clientId: $consumerId,
            clientSecret: $consumerSecret,
            defaultHeaders: $defaultHeaders,
            token: $token,
            tokenSecret: $tokenSecret,
            authType: AuthType::oAuthV1,
            authSettings: [
                'location' => 'header',
                'headerPrefix' => 'OAuth ',
            ],
            version: $version,
            realm: $realm,
            signatureMethod: $signatureMethod,
            delayHeader: $delayHeader,
            delayUnit: $delayUnit,
        );
    }
}