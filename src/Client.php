<?php

namespace Anibalealvarezs\ApiSkeleton;

use Anibalealvarezs\ApiSkeleton\Classes\Exceptions\ApiRequestException;
use Anibalealvarezs\ApiSkeleton\Classes\Exceptions\AuthenticationException;
use Anibalealvarezs\ApiSkeleton\Classes\Exceptions\DebugException;
use Anibalealvarezs\ApiSkeleton\Enums\AuthType;
use Anibalealvarezs\ApiSkeleton\Enums\DelayUnit;
use Anibalealvarezs\OAuthV1\Enums\SignatureMethod;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;
use Anibalealvarezs\OAuthV1\OAuthV1;

class Client
{
    protected string $baseUrl;
    protected ?GuzzleClient $guzzleClient = null;
    protected string $authUrl = "";
    protected string $tokenUrl = "";
    protected string $redirectUrl = "https://oauth.pstmn.io/v1/browser-callback";
    protected array $scopes = [];
    protected string $token = "";
    protected string $tokenSecret = "";
    protected string $clientId = "";
    protected string $clientSecret = "";
    protected string $refreshToken = "";
    protected array $refreshTokenHeaders = [];
    protected string $refreshAuthUrl = "";
    protected string $userId = "";
    protected string $password = "";
    protected array $headers = [];
    protected string $apiKey = "";
    protected AuthType $authType = AuthType::apiKey;
    protected array $authSettings = [];
    protected string $version = "";
    protected string $realm = "";
    protected bool $encodeParams = true;
    protected SignatureMethod $signatureMethod = SignatureMethod::HMAC_SHA1;
    protected OAuthV1 $oAuthV1;
    protected ?string $delayHeader = null;
    protected DelayUnit $delayUnit = DelayUnit::second;
    protected bool $debugMode = false;
    protected array $debugData = [];
    protected mixed $errorMessageParser = null;
    protected mixed $responseErrorDetector = null;
    protected mixed $failureHandler = null;

    /**
     * Client constructor.
     * @param string $baseUrl
     * @param GuzzleClient|null $guzzleClient
     * @param string $authUrl
     * @param string $refreshAuthUrl
     * @param string $tokenUrl
     * @param string $redirectUrl
     * @param string $clientId
     * @param string $clientSecret
     * @param array $defaultHeaders
     * @param string $refreshToken
     * @param array $refreshTokenHeaders
     * @param string $userId
     * @param string $password
     * @param string $apiKey
     * @param array $scopes
     * @param string $token
     * @param string $tokenSecret
     * @param AuthType $authType
     * @param array $authSettings
     * @param string $version
     * @param string $realm
     * @param bool $encodeParams
     * @param SignatureMethod $signatureMethod
     * @param string|null $delayHeader
     * @param DelayUnit $delayUnit
     * @param bool $debugMode
     * @param mixed $errorMessageParser
     * @throws Exception
     */
    public function __construct(
        string $baseUrl,
        ?GuzzleClient $guzzleClient = null,
        string $authUrl = "",
        string $refreshAuthUrl = "",
        string $tokenUrl = "",
        string $redirectUrl = "",
        string $clientId = "",
        string $clientSecret = "",
        array $defaultHeaders = [],
        string $refreshToken = "",
        array $refreshTokenHeaders = ["Content-Type" => "application/json"],
        string $userId = "",
        string $password = "",
        string $apiKey = "",
        array $scopes = [],
        string $token = "",
        string $tokenSecret = "",
        AuthType $authType = AuthType::apiKey,
        array $authSettings = [],
        string $version = "",
        string $realm = "",
        bool $encodeParams = true,
        SignatureMethod $signatureMethod = SignatureMethod::HMAC_SHA1,
        ?string $delayHeader = null,
        DelayUnit $delayUnit = DelayUnit::second,
        bool $debugMode = false,
        mixed $errorMessageParser = null,
        mixed $failureHandler = null,
        mixed $responseErrorDetector = null,
    ) {
        // Set properties
        $this->setBaseUrl($baseUrl);
        $this->setGuzzleClient($guzzleClient ?: new GuzzleClient());
        $this->setAuthUrl($authUrl);
        $this->setTokenUrl($tokenUrl);
        $this->setRefreshAuthUrl($refreshAuthUrl ?: $this->authUrl);
        $this->setRedirectUrl($redirectUrl);
        $this->setScopes($scopes);
        $this->setUserId($userId);
        $this->setPassword($password);
        $this->setClientId($clientId);
        $this->setClientSecret($clientSecret);
        $this->setRefreshToken($refreshToken);
        $this->setRefreshTokenHeaders($refreshTokenHeaders);
        $this->setAuthSettings($authSettings);
        $this->setHeaders($defaultHeaders);
        $this->setApiKey($apiKey);
        $this->setAuthType($authType);
        $this->setToken($token);
        $this->setTokenSecret($tokenSecret);
        $this->setVersion($version);
        $this->setRealm($realm);
        $this->setEncodeParams($encodeParams);
        $this->setSignatureMethod($signatureMethod);
        $this->setDelayHeader($delayHeader);
        $this->setDelayUnit($delayUnit);
        $this->setDebugMode($debugMode);
        $this->setErrorMessageParser($errorMessageParser);
        $this->setFailureHandler($failureHandler);
        $this->setResponseErrorDetector($responseErrorDetector);

        // Validate auth type
        $this->validateAuthType();
    }

    /**
     * @return GuzzleClient
     */
    public function getGuzzleClient(): GuzzleClient
    {
        return $this->guzzleClient;
    }

    /**
     * @param GuzzleClient $param
     * @return void
     */
    public function setGuzzleClient(GuzzleClient $param): void
    {
        $this->guzzleClient = $param;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return void
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return void
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return void
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getTokenSecret(): string
    {
        return $this->tokenSecret;
    }

    /**
     * @param string $tokenSecret
     * @return void
     */
    public function setTokenSecret(string $tokenSecret): void
    {
        $this->tokenSecret = $tokenSecret;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     * @return void
     */
    public function setBaseUrl(string $baseUrl): void
    {
        if (!$baseUrl) {
            throw new InvalidArgumentException("Base URL is required");
        }
        if (filter_var($baseUrl, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Invalid baseUrl parameter: "' . $baseUrl . '"');
        }
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return string
     */
    public function getAuthUrl(): string
    {
        return $this->authUrl;
    }

    /**
     * @param string $authUrl
     * @return void
     */
    public function setAuthUrl(string $authUrl): void
    {
        if ($authUrl && filter_var($authUrl, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Invalid authUrl parameter: "' . $authUrl . '"');
        }
        $this->authUrl = $authUrl;
    }

    /**
     * @return string
     */
    public function getRefreshAuthUrl(): string
    {
        return $this->refreshAuthUrl;
    }

    /**
     * @param string $refreshAuthUrl
     * @return void
     */
    public function setRefreshAuthUrl(string $refreshAuthUrl): void
    {
        if ($refreshAuthUrl && filter_var($refreshAuthUrl, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Invalid refreshAuthUrl parameter: "' . $refreshAuthUrl . '"');
        }
        $this->refreshAuthUrl = $refreshAuthUrl;
    }

    /**
     * @return string
     */
    public function getTokenUrl(): string
    {
        return $this->tokenUrl;
    }

    /**
     * @param string $tokenUrl
     * @return void
     */
    public function setTokenUrl(string $tokenUrl): void
    {
        if ($tokenUrl && filter_var($tokenUrl, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Invalid tokenUrl parameter: "' . $tokenUrl . '"');
        }
        $this->tokenUrl = $tokenUrl;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     * @return void
     */
    public function setRedirectUrl(string $redirectUrl): void
    {
        if ($redirectUrl && filter_var($redirectUrl, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Invalid redirectUrl parameter: "' . $redirectUrl . '"');
        }
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @param array $scopes
     * @return void
     */
    public function setScopes(array $scopes): void
    {
        $this->scopes = $scopes;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     * @return void
     */
    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     * @return void
     */
    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @param string $clientSecret
     * @return void
     */
    public function updateClientSecret(string $clientSecret): void
    {
        $this->setClientSecret($clientSecret);
        $this->setToken('');
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     * @return void
     */
    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @param string $refreshToken
     * @return void
     */
    public function updateRefreshToken(string $refreshToken): void
    {
        $this->setRefreshToken($refreshToken);
        $this->setToken('');
    }

    /**
     * @return array
     */
    public function getRefreshTokenHeaders(): array
    {
        return $this->refreshTokenHeaders;
    }

    /**
     * @param array $refreshTokenHeaders
     * @return void
     */
    public function setRefreshTokenHeaders(array $refreshTokenHeaders): void
    {
        $this->refreshTokenHeaders = $refreshTokenHeaders;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     * @return void
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return AuthType
     */
    public function getAuthType(): AuthType
    {
        return $this->authType;
    }

    /**
     * @param AuthType $authType
     * @return void
     */
    public function setAuthType(AuthType $authType): void
    {
        $this->authType = $authType;
    }

    /**
     * @return array
     */
    public function getAuthSettings(): array
    {
        return $this->authSettings;
    }

    /**
     * @param array $authSettings
     * @return void
     */
    public function setAuthSettings(array $authSettings): void
    {
        $this->authSettings = $authSettings;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return void
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getRealm(): string
    {
        return $this->realm;
    }

    /**
     * @param string $realm
     * @return void
     */
    public function setRealm(string $realm): void
    {
        $this->realm = $realm;
    }

    /**
     * @return bool
     */
    public function getEncodeParams(): bool
    {
        return $this->encodeParams;
    }

    /**
     * @param bool $encodeParams
     * @return void
     */
    public function setEncodeParams(bool $encodeParams): void
    {
        $this->encodeParams = $encodeParams;
    }

    /**
     * @return SignatureMethod
     */
    public function getSignatureMethod(): SignatureMethod
    {
        return $this->signatureMethod;
    }

    /**
     * @param SignatureMethod $signatureMethod
     * @return void
     */
    public function setSignatureMethod(SignatureMethod $signatureMethod): void
    {
        $this->signatureMethod = $signatureMethod;
    }

    /**
     * @return OAuthV1
     */
    public function getOAuthV1(): OAuthV1
    {
        return $this->oAuthV1;
    }

    /**
     * @param OAuthV1|null $oAuth
     * @return void
     */
    public function setOAuthV1(?OAuthV1 $oAuth = null): void
    {
        if ($oAuth) {
            $this->oAuthV1 = $oAuth;
            return;
        }
        $oAuth = new OAuthV1(
            consumerId: $this->getClientId(),
            consumerSecret: $this->getClientSecret(),
            token: $this->getToken(),
            tokenSecret: $this->getTokenSecret(),
            realm: $this->getRealm() ?: "",
            signatureMethod: SignatureMethod::HMAC_SHA256,
            version: $this->getVersion() ?: "1.0",
        );
        $this->oAuthV1 = $oAuth;
    }

    /**
     * @return string|null
     */
    public function getDelayHeader(): ?string
    {
        return $this->delayHeader;
    }

    /**
     * @param string|null $delayHeader
     * @return void
     */
    public function setDelayHeader(?string $delayHeader): void
    {
        $this->delayHeader = $delayHeader;
    }

    /**
     * @return DelayUnit
     */
    public function getDelayUnit(): DelayUnit
    {
        return $this->delayUnit;
    }

    /**
     * @param DelayUnit $delayUnit
     * @return void
     */
    public function setDelayUnit(DelayUnit $delayUnit): void
    {
        $this->delayUnit = $delayUnit;
    }

    /**
     * @return bool
     */
    public function getDebugMode(): bool
    {
        return $this->debugMode;
    }

    /**
     * @param bool $debugMode
     * @return void
     */
    public function setDebugMode(bool $debugMode): void
    {
        $this->debugMode = $debugMode;
    }

    /**
     * @return mixed
     */
    public function getErrorMessageParser(): mixed
    {
        return $this->errorMessageParser;
    }

    /**
     * @param mixed $errorMessageParser
     * @return void
     */
    public function setErrorMessageParser(mixed $errorMessageParser): void
    {
        $this->errorMessageParser = $errorMessageParser;
    }

    /**
     * @return mixed
     */
    public function getResponseErrorDetector(): mixed
    {
        return $this->responseErrorDetector;
    }

    /**
     * @param mixed $responseErrorDetector
     * @return void
     */
    public function setResponseErrorDetector(mixed $responseErrorDetector): void
    {
        $this->responseErrorDetector = $responseErrorDetector;
    }

    /**
     * @return mixed
     */
    public function getFailureHandler(): mixed
    {
        return $this->failureHandler;
    }

    /**
     * @param mixed $failureHandler
     * @return void
     */
    public function setFailureHandler(mixed $failureHandler): void
    {
        $this->failureHandler = $failureHandler;
    }

    /**
     * @return array
     */
    public function getDebugData(): array
    {
        return $this->debugData;
    }

    /**
     * @param array $debugData
     * @return void
     */
    public function setDebugData(array $debugData): void
    {
        $this->debugData = $debugData;
    }

    /**
     * @param array $debugData
     * @return void
     */
    public function addDebugData(array $debugData): void
    {
        $this->setDebugData([...$this->debugData, ...$debugData]);
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function validateAuthType(): void
    {
        switch ($this->getAuthType()) {
            case AuthType::apiKey:
                if (!$this->getApiKey()) {
                    throw new InvalidArgumentException("API Key is required for API Key authentication");
                }
                if (!isset($this->getAuthSettings()['location'])) {
                    throw new InvalidArgumentException("Location is required for API Key authentication.");
                }
                if (!in_array($this->getAuthSettings()['location'], ['query', 'header'])) {
                    throw new InvalidArgumentException("Location must be either 'query' or 'header' for API Key authentication.");
                }
                if (!isset($this->getAuthSettings()['name'])) {
                    throw new InvalidArgumentException("Name is required for API Key authentication.");
                }
                break;
            case AuthType::oAuthV1:
                if (!$this->getClientId()) {
                    throw new InvalidArgumentException("Unauthorized. No Client ID provided.");
                }
                if (!$this->getClientSecret()) {
                    throw new InvalidArgumentException("Unauthorized. No Client Secret provided.");
                }
                if (!$this->getToken()) {
                    throw new InvalidArgumentException("Unauthorized. No Token provided.");
                }
                if (!$this->getTokenSecret()) {
                    throw new InvalidArgumentException("Unauthorized. No Token Secret provided.");
                }
                if (!isset($this->getAuthSettings()['location'])) {
                    throw new InvalidArgumentException("Location is required for OAuth v1 authentication.");
                }
                if (!in_array($this->getAuthSettings()['location'], ['query', 'header', 'body'])) {
                    throw new InvalidArgumentException("Location must be either 'query', 'header' or 'body' for OAuth v1 authentication.");
                }
                break;
            case AuthType::oAuthV2:
                if (!$this->getClientId()) {
                    throw new InvalidArgumentException("Unauthorized. No Client ID provided.");
                }
                if (!$this->getClientSecret()) {
                    throw new InvalidArgumentException("Unauthorized. No Client Secret provided.");
                }
                if (!$this->getRefreshToken()) {
                    throw new InvalidArgumentException("Unauthorized. No refresh token provided.");
                }
                if (!isset($this->getAuthSettings()['location'])) {
                    throw new InvalidArgumentException("Location is required for OAuth v2 authentication.");
                }
                if (!in_array($this->getAuthSettings()['location'], ['query', 'header'])) {
                    throw new InvalidArgumentException("Location must be either 'query' or 'header' for OAuth v2 authentication.");
                }
                if (($this->getAuthSettings()['location'] == 'query') && !isset($this->getAuthSettings()['name'])) {
                    throw new InvalidArgumentException("Name is required for OAuth v2 authentication.");
                }
                break;
            case AuthType::bearerToken:
                if (!$this->getToken()) {
                    throw new InvalidArgumentException("Token is required for Bearer Token authentication");
                }
                break;
            case AuthType::basic:
                if (!$this->getUserId()) {
                    throw new InvalidArgumentException("Valid User ID is required for Basic authentication");
                }
                if (!$this->getPassword()) {
                    throw new InvalidArgumentException("Valid Password is required for Basic authentication");
                }
                break;
            case AuthType::none:
                break;
        }
    }

    /**
     * @param array $params
     * @param string $method
     * @param string $endpoint
     * @param string $baseUrl
     * @throws Exception
     * @throws GuzzleException
     */
    public function setAuth(
        array &$params,
        string $method = "",
        string $endpoint = "",
        string $baseUrl = "",
    ): void {
        switch ($this->getAuthType()) {
            case AuthType::apiKey:
                if ($this->getAuthSettings()['location'] == 'query') {
                    $params['query'][$this->getAuthSettings()['name']] = $this->getApiKey();
                } elseif ($this->getAuthSettings()['location'] == 'header') {
                    $params['headers'][$this->getAuthSettings()['name']] = ($this->getAuthSettings()['headerPrefix'] ?? '') . $this->getApiKey();
                }
                break;
            case AuthType::oAuthV1:
                if ($this->getAuthSettings()['location'] == 'query') {
                    $params['query'][$this->getAuthSettings()['name']] = $this->getApiKey();
                } elseif ($this->getAuthSettings()['location'] == 'header') {
                    $this->setOAuthV1();
                    $authorizationHeader = $this->getOAuthV1()
                        ->setTimestamp(timestamp: (string) time())
                        ->getAuthorizationHeader(
                            method: $method,
                            url: ($baseUrl ?: $this->getBaseUrl()) . $endpoint,
                            queryParams: $params['query'],
                            prefix: ($this->getAuthSettings()['headerPrefix'] ?? 'OAuth '),
                        );
                    $params['headers']['Authorization'] = $authorizationHeader['string'];
                    if ($this->getDebugMode()) {
                        $this->addDebugData(debugData: $authorizationHeader['debugData']);
                    }
                }
                break;
            case AuthType::oAuthV2:
                if ($this->getAuthSettings()['location'] == 'query') {
                    $params['query'][$this->getAuthSettings()['name']] = $this->getApiKey();
                } elseif ($this->getAuthSettings()['location'] == 'header') {
                    if (!$this->getToken()) {
                        $this->setToken($this->getNewToken());
                    }
                    $params['headers']['Authorization'] = ($this->getAuthSettings()['headerPrefix'] ?? '') . $this->getToken();
                }
                break;
            case AuthType::bearerToken:
                $params['headers']['Authorization'] = ($this->getAuthSettings()['headerPrefix'] ?? 'Bearer ') . $this->getToken();
                break;
            case AuthType::basic:
                $params['headers']['Authorization'] = ($this->getAuthSettings()['headerPrefix'] ?? 'Basic ') . base64_encode($this->getUserId() . ':' . $this->getPassword());
                break;
            case AuthType::none:
                break;
        }
    }

    /**
     * @return string|null
     * @throws GuzzleException
     */
    public function getNewToken(): ?string
    {
        $body = [
            "client_id" => $this->getClientId(),
            "client_secret" => $this->getClientSecret(),
            "refresh_token" => $this->getRefreshToken(),
            "grant_type" => "refresh_token"
        ];

        $response = $this->performRequest(
            method: "POST",
            endpoint: "",
            body: json_encode($body),
            baseUrl: $this->getTokenUrl(),
            headers: $this->getRefreshTokenHeaders(),
            allowNewToken: false,
            ignoreAuth: true,
        );
        $data = json_decode($response->getBody()->getContents());

        return $data->access_token;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $query
     * @param string|array $body
     * @param array $form_params
     * @param string $baseUrl
     * @param array $headers
     * @param array $additionalHeaders
     * @param ?CookieJar $cookies
     * @param bool $verify
     * @param bool $allowNewToken
     * @param string $pathToSave
     * @param bool|null $stream
     * @param mixed $errorMessageNesting
     * @param int $sleep
     * @param array $customErrors
     * @param bool $ignoreAuth
     * @return ResponseInterface
     * @throws ApiRequestException
     * @throws AuthenticationException
     * @throws DebugException
     * @throws GuzzleException
     * @throws Exception
     */
    public function performRequest(
        string $method,
        string $endpoint,
        array $query = [],
        string|array $body = "",
        array $form_params = [],
        string $baseUrl = "",
        array $headers = [],
        array $additionalHeaders = [], // Ex: ["Amazon-Advertising-API-Scope" => 'profileId'];
        ?CookieJar $cookies = null,
        bool $verify = false,
        bool $allowNewToken = true,
        string $pathToSave = "",
        ?bool $stream = null,
        mixed $errorMessageNesting = null, // Ex: 'error.message' or ['error.message', 'msg'] or fn($data) => $data['err']
        int $sleep = 0,
        array $customErrors = [], // Ex: ['403' => 'body'] or ['500' => 'code'] or ['404' => 'message']
        bool $ignoreAuth = false,
        mixed $onFailure = null,
    ): mixed {
        $params = [
            'query' => $query,
            'headers' => !empty($headers) ? $headers : $this->headers,
            'verify' => $verify
        ];

        if (!empty($additionalHeaders)) {
            foreach ($additionalHeaders as $key => $value) {
                $params["headers"][$key] = $value;
            }
        }

        if ($cookies) {
            $params["cookies"] = $cookies;
        }
        if ($body) {
            $params["body"] = $body;
        }
        if ($form_params) {
            $params["form_params"] = $form_params;
        }

        if ($sleep > 0) {
            usleep($sleep);
        }

        if ($pathToSave) {
            $resource = Utils::tryFopen($pathToSave, 'w');
            if ($stream) {
                $params["save_to"] = Utils::streamFor($resource);
            } else {
                $params["sink"] = $resource;
            }
        }

        if (!$ignoreAuth) {
            $this->setAuth(
                params: $params,
                method: $method,
                endpoint: $endpoint,
                baseUrl: ($baseUrl ?: $this->getBaseUrl()),
            );
        }

        $guzzle = $this->guzzleClient ?? new GuzzleClient();

        try {
            if ($this->getDebugMode()) {
                $request = new Request(
                    method: $method,
                    uri: ($baseUrl ?: $this->getBaseUrl()) . $endpoint,
                    headers: $params['headers'],
                    body: $params['body'] ?? (isset($params['form_params']) ? http_build_query($params['form_params'], '', '&') : null),
                );
                $debugData = [
                    'method' => $request->getMethod(),
                    'query_params' => $params['query'],
                    'uri' => $request->getUri()->withQuery(http_build_query($params['query'], '', '&', PHP_QUERY_RFC3986)),
                    'headers' => $request->getHeaders(),
                    'body' => $request->getBody()->getContents(),
                    'target' => $request->getRequestTarget(),
                    'protocol_version' => $request->getProtocolVersion(),
                ];
                if ($this->getAuthType() == AuthType::oAuthV1) {
                    $debugData['oauth_debug_data'] = $this->getDebugData();
                }
                $this->addDebugData(debugData: $debugData);
                throw new DebugException(json_encode($this->getDebugData(), JSON_PRETTY_PRINT));
            }
            $response = $guzzle->request(
                method: $method,
                uri: ($baseUrl ?: $this->getBaseUrl()) . $endpoint,
                options: $params,
            );

            // Check for 'falsy 200' errors
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $detector = $this->getResponseErrorDetector();
                if ($detector) {
                    $body = $response->getBody();
                    $body->rewind();
                    $contents = json_decode($body->getContents(), true);
                    $body->rewind();

                    if ($contents && $this->isErrorBody($contents, $detector)) {
                        return $this->handleException(
                            exception: new ApiRequestException(
                                $this->parseErrorData($contents, $errorMessageNesting ?? $this->getErrorMessageParser()),
                                $response->getStatusCode()
                            ),
                            onFailure: $onFailure
                        );
                    }
                }
            }

            return $response;
        } catch (RequestException $e) {
            // Exponential or custom back-off for rate limit
            if ($e->getCode() == 429) {
                if ($e->hasResponse() && ($delayHeader = $this->getDelayHeader())) {
                    $dynamicSleep = (int) $e->getResponse()->getHeaderLine($delayHeader) *
                        match ($this->getDelayUnit()) {
                            DelayUnit::second => 1000000,
                            DelayUnit::millisecond => 1000,
                            DelayUnit::microsecond => 1,
                        };
                }

                return $this->performRequest(
                    method: $method,
                    endpoint: $endpoint,
                    query: $query,
                    body: $body,
                    form_params: $form_params,
                    baseUrl: $baseUrl,
                    headers: $headers,
                    cookies: $cookies,
                    verify: $verify,
                    allowNewToken: $allowNewToken,
                    pathToSave: $pathToSave,
                    stream: $stream,
                    errorMessageNesting: $errorMessageNesting,
                    sleep: $dynamicSleep ?? ($sleep > 0 ? $sleep * 2 : 1000000), // Default: 1 second = 1000000 microseconds
                );
            }

            if ($e->getCode() != 401) {
                return $this->handleException(
                    exception: new ApiRequestException(
                        $this->getErrorMessage(exception: $e, errorMessageNesting: $errorMessageNesting),
                        $e->getCode(),
                        $e
                    ),
                    onFailure: $onFailure
                );
            }

            if (in_array($e->getCode(), array_keys($customErrors))) {
                return $this->handleException(
                    exception: new ApiRequestException(
                        match ($customErrors[$e->getCode()]) {
                            'body' => $e->getResponse()->getBody()->getContents(),
                            'code' => (string) $e->getCode(),
                            'message' => $e->getMessage(),
                            default => (string) $customErrors[$e->getCode()],
                        },
                        $e->getCode(),
                        $e
                    ),
                    onFailure: $onFailure
                );
            }

            if (!$allowNewToken || $this->authType != AuthType::oAuthV2) {
                return $this->handleException(
                    exception: new AuthenticationException(
                        $this->getErrorMessage(exception: $e, errorMessageNesting: $errorMessageNesting),
                        $e->getCode(),
                        $e
                    ),
                    onFailure: $onFailure
                );
            }

            if (!$this->getRefreshToken()) {
                throw new AuthenticationException("Unauthorized. No refresh token provided.", 401);
            }

            $this->setToken($this->getNewToken());

            if (!$ignoreAuth) {
                $this->setAuth(
                    params: $params,
                    method: $method,
                    endpoint: $endpoint,
                    baseUrl: ($baseUrl ?: $this->getBaseUrl()),
                );
            }

            // Retry request
            try {
                $response = $guzzle->request(
                    method: $method,
                    uri: ($baseUrl ?: $this->getBaseUrl()) . $endpoint,
                    options: $params,
                );

                // Check for 'falsy 200' errors on retry
                if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                    $detector = $this->getResponseErrorDetector();
                    if ($detector) {
                        $body = $response->getBody();
                        $body->rewind();
                        $contents = json_decode($body->getContents(), true);
                        $body->rewind();

                        if ($contents && $this->isErrorBody($contents, $detector)) {
                            return $this->handleException(
                                exception: new ApiRequestException(
                                    $this->parseErrorData($contents, $errorMessageNesting ?? $this->getErrorMessageParser()),
                                    $response->getStatusCode()
                                ),
                                onFailure: $onFailure
                            );
                        }
                    }
                }

                return $response;
            } catch (RequestException $e) {
                return $this->handleException(
                    exception: new ApiRequestException(
                        $this->getErrorMessage(exception: $e, errorMessageNesting: $errorMessageNesting),
                        $e->getCode(),
                        $e
                    ),
                    onFailure: $onFailure
                );
            }
        }
    }

    /**
     * @param RequestException $exception
     * @param mixed $errorMessageNesting
     * @return string
     */
    protected function getErrorMessage(RequestException $exception, mixed $errorMessageNesting = null): string
    {
        $parser = $errorMessageNesting ?? $this->getErrorMessageParser();

        if (!$exception->hasResponse() || !$parser) {
            return $exception->getMessage();
        }

        $body = $exception->getResponse()->getBody();
        $body->rewind();
        $contents = json_decode($body->getContents(), true);

        if (!$contents) {
            return $exception->getMessage();
        }

        return self::parseErrorData($contents, $parser);
    }

    /**
     * @param array $data
     * @param mixed $parser
     * @return string
     */
    protected static function parseErrorData(array $data, mixed $parser): string
    {
        // 1. Support for callables
        if (is_callable($parser)) {
            return $parser($data);
        }

        // 2. Original array structure support (for backward compatibility)
        if (is_array($parser) && !empty($parser) && is_array(reset($parser))) {
            return self::getNestedErrorMessage($data, $parser);
        }

        // 3. Candidate paths (strings or dot notation)
        $candidates = (array) $parser;
        foreach ($candidates as $path) {
            if (!is_string($path)) {
                continue;
            }
            $val = self::getValueByPath($data, $path);
            if ($val && (is_string($val) || is_numeric($val))) {
                return (string) $val;
            }
        }

        // Fallback: Return original JSON if no match found
        return json_encode($data);
    }

    /**
     * @param array $data
     * @param string $path
     * @return mixed
     */
    protected static function getValueByPath(array $data, string $path): mixed
    {
        if (!str_contains($path, '.')) {
            return $data[$path] ?? null;
        }

        foreach (explode('.', $path) as $key) {
            if (!is_array($data) || !isset($data[$key])) {
                return null;
            }
            $data = $data[$key];
        }

        return $data;
    }

    /**
     * @param Exception $exception
     * @param mixed $onFailure
     * @return mixed
     * @throws Exception
     */
    protected function handleException(Exception $exception, mixed $onFailure = null): mixed
    {
        $handler = $onFailure ?? $this->getFailureHandler();

        if (is_callable($handler)) {
            return $handler($exception);
        }

        if (!is_null($handler)) {
            return $handler;
        }

        throw $exception;
    }

    /**
     * @param array $contents
     * @param mixed $nesting
     * @return string
     */
    protected static function getNestedErrorMessage(array $contents, mixed $nesting): string
    {
        $key = array_keys($nesting)[0];
        if (is_array($nesting[$key])) {
            if (isset($contents[$key])) {
                return self::getNestedErrorMessage(contents: $contents[$key], nesting: $nesting[$key]);
            }
        }
        if (isset($contents[$nesting[$key]])) {
            return $contents[$nesting[$key]];
        }
        return json_encode($contents);
    }
    /**
     * @param array $data
     * @param mixed $detector
     * @return bool
     */
    protected function isErrorBody(array $data, mixed $detector): bool
    {
        // 1. Support for callables
        if (is_callable($detector)) {
            return $detector($data);
        }

        // 2. Dot notation check
        $val = self::getValueByPath($data, (string) $detector);

        // If the key exists:
        // - if it's "success" and it's false, it's an error.
        // - if it's "error" and it's truthy, it's an error.
        if (str_contains(strtolower((string) $detector), 'success')) {
            return ($val === false || $val === 'false' || $val === 0 || $val === '0');
        }

        return !empty($val);
    }
}
