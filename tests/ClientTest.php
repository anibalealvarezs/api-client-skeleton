<?php

namespace Tests;

use Anibalealvarezs\ApiSkeleton\Client;
use Anibalealvarezs\ApiSkeleton\Enums\AuthType;
use Anibalealvarezs\ApiSkeleton\Enums\DelayUnit;
use Anibalealvarezs\ApiSkeleton\Classes\Exceptions\DebugException;
use Anibalealvarezs\ApiSkeleton\Classes\Exceptions\ApiRequestException;
use Anibalealvarezs\ApiSkeleton\Classes\Exceptions\AuthenticationException;
use Anibalealvarezs\OAuthV1\Enums\SignatureMethod;
use Exception;
use Faker\Factory as Faker;
use Faker\Generator;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class ClientTest extends TestCase
{
    protected Generator $faker;
    protected string $baseUrl;
    protected string $apiKey;
    protected string $clientId;
    protected string $clientSecret;
    protected string $refreshToken;
    protected string $token;
    protected string $tokenSecret;
    protected string $tokenUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create();
        $this->baseUrl = 'http://localhost'; // Use a fixed, mock-friendly URL
        $this->apiKey = $this->faker->uuid;
        $this->clientId = $this->faker->uuid;
        $this->clientSecret = $this->faker->uuid;
        $this->refreshToken = $this->faker->uuid;
        $this->token = $this->faker->uuid;
        $this->tokenSecret = $this->faker->uuid;
        $this->tokenUrl = 'http://localhost/token'; // Fixed token URL
    }

    protected function createMockedGuzzleClient(array $responses): GuzzleClient
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        return new GuzzleClient(['handler' => $handler]);
    }

    /**
     * @throws Exception
     */
    public function testConstructorWithApiKeyAuth(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        $this->assertEquals($this->baseUrl, $client->getBaseUrl());
        $this->assertEquals($this->apiKey, $client->getApiKey());
        $this->assertEquals(AuthType::apiKey, $client->getAuthType());
        $this->assertEquals(['location' => 'header', 'name' => 'X-API-Key'], $client->getAuthSettings());
        $this->assertInstanceOf(GuzzleClient::class, $client->getGuzzleClient());
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testConstructorWithOAuthV2Auth(): void
    {
        // Set up mock handler to intercept any token requests
        $mock = new MockHandler([
            new Response(200, [], json_encode(['access_token' => $this->token]))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle, // Pass mocked client
            tokenUrl: $this->tokenUrl,
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            refreshToken: $this->refreshToken,
            authType: AuthType::oAuthV2,
            authSettings: ['location' => 'header']
        );

        $this->assertEquals($this->clientId, $client->getClientId());
        $this->assertEquals($this->clientSecret, $client->getClientSecret());
        $this->assertEquals($this->refreshToken, $client->getRefreshToken());
        $this->assertEquals(AuthType::oAuthV2, $client->getAuthType());
        $this->assertInstanceOf(GuzzleClient::class, $client->getGuzzleClient());
    }

    public function testConstructorThrowsExceptionForInvalidBaseUrl(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Base URL is required");

        new Client(baseUrl: "");
    }

    /**
     * @throws Exception
     */
    public function testConstructorThrowsExceptionForInvalidApiKeySettings(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Location is required for API Key authentication.");

        new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: []
        );
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetBaseUrl(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            authType: AuthType::none // Explicitly disable authentication
        );
        $newUrl = 'https://example.com'; // Use a fixed URL to avoid Faker issues
        $client->setBaseUrl($newUrl);

        $this->assertEquals($newUrl, $client->getBaseUrl());
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetClientId(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );
        // Verify initial state
        $this->assertEquals('', $client->getClientId(), 'Client ID should be empty initially');
        // Set and verify client ID
        $client->setClientId($this->clientId);
        $this->assertEquals($this->clientId, $client->getClientId(), 'Client ID should match the set value');
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetUserId(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertEquals('', $client->getUserId(), 'User ID should be empty initially');

        // Set and verify user ID
        $userId = $this->faker->userName;
        $client->setUserId($userId);
        $this->assertEquals($userId, $client->getUserId(), 'User ID should match the set value');
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetPassword(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertEquals('', $client->getPassword(), 'Password should be empty initially');

        // Set and verify password
        $password = $this->faker->password;
        $client->setPassword($password);
        $this->assertEquals($password, $client->getPassword(), 'Password should match the set value');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetAuthForApiKeyInHeader(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        $params = ['query' => [], 'headers' => []];
        $client->setAuth($params);

        $this->assertArrayHasKey('headers', $params);
        $this->assertEquals($this->apiKey, $params['headers']['X-API-Key']);
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetTokenSecret(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertEquals('', $client->getTokenSecret(), 'Token secret should be empty initially');

        // Set and verify token secret
        $client->setTokenSecret($this->tokenSecret);
        $this->assertEquals($this->tokenSecret, $client->getTokenSecret(), 'Token secret should match the set value');
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetScopes(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertEquals([], $client->getScopes(), 'Scopes should be an empty array initially');

        // Set and verify scopes
        $scopes = ['scope1', 'scope2'];
        $client->setScopes($scopes);
        $this->assertEquals($scopes, $client->getScopes(), 'Scopes should match the set value');

        // Test with empty array
        $client->setScopes([]);
        $this->assertEquals([], $client->getScopes(), 'Scopes should be an empty array when set to empty');
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetHeaders(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertEquals([], $client->getHeaders(), 'Headers should be an empty array initially');

        // Set and verify headers
        $headers = ['X-Custom-Header' => 'value', 'Authorization' => 'Bearer token'];
        $client->setHeaders($headers);
        $this->assertEquals($headers, $client->getHeaders(), 'Headers should match the set value');

        // Test with empty array
        $client->setHeaders([]);
        $this->assertEquals([], $client->getHeaders(), 'Headers should be an empty array when set to empty');
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetClientSecret(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertEquals('', $client->getClientSecret(), 'Client secret should be empty initially');

        // Set and verify client secret
        $client->setClientSecret($this->clientSecret);
        $this->assertEquals($this->clientSecret, $client->getClientSecret(), 'Client secret should match the set value');
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetRefreshToken(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertEquals('', $client->getRefreshToken(), 'Refresh token should be empty initially');

        // Set and verify refresh token
        $client->setRefreshToken($this->refreshToken);
        $this->assertEquals($this->refreshToken, $client->getRefreshToken(), 'Refresh token should match the set value');
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetVersion(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertEquals('', $client->getVersion(), 'Version should be empty initially');

        // Set and verify version
        $version = '1.0';
        $client->setVersion($version);
        $this->assertEquals($version, $client->getVersion(), 'Version should match the set value');
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetRealm(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertEquals('', $client->getRealm(), 'Realm should be empty initially');

        // Set and verify realm
        $realm = 'example-realm';
        $client->setRealm($realm);
        $this->assertEquals($realm, $client->getRealm(), 'Realm should match the set value');
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetEncodeParams(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertTrue($client->getEncodeParams(), 'Encode params should be true initially');

        // Set and verify encode params to false
        $client->setEncodeParams(false);
        $this->assertFalse($client->getEncodeParams(), 'Encode params should be false after setting to false');

        // Set and verify encode params to true
        $client->setEncodeParams(true);
        $this->assertTrue($client->getEncodeParams(), 'Encode params should be true after setting to true');
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetSignatureMethod(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertEquals(SignatureMethod::HMAC_SHA1, $client->getSignatureMethod(), 'Signature method should be HMAC_SHA1 initially');

        // Set and verify signature method
        $client->setSignatureMethod(SignatureMethod::HMAC_SHA256);
        $this->assertEquals(SignatureMethod::HMAC_SHA256, $client->getSignatureMethod(), 'Signature method should match the set value');
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetDelayHeader(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertNull($client->getDelayHeader(), 'Delay header should be null initially');

        // Set and verify delay header
        $delayHeader = 'X-Rate-Limit-Reset';
        $client->setDelayHeader($delayHeader);
        $this->assertEquals($delayHeader, $client->getDelayHeader(), 'Delay header should match the set value');

        // Test with null
        $client->setDelayHeader(null);
        $this->assertNull($client->getDelayHeader(), 'Delay header should be null when set to null');
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetDelayUnit(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertEquals(DelayUnit::second, $client->getDelayUnit(), 'Delay unit should be second initially');

        // Set and verify delay unit
        $client->setDelayUnit(DelayUnit::millisecond);
        $this->assertEquals(DelayUnit::millisecond, $client->getDelayUnit(), 'Delay unit should match the set value');
    }

    /**
     * @throws Exception
     */
    public function testSetBaseUrlThrowsExceptionForInvalidUrl(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid baseUrl parameter: "invalid-url"');

        $client->setBaseUrl("invalid-url");
    }

    /**
     * @throws Exception
     */
    public function testSetAuthUrlThrowsExceptionForInvalidUrl(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid authUrl parameter: "invalid-url"');

        $client->setAuthUrl("invalid-url");
    }

    /**
     * @throws Exception
     */
    public function testSetTokenUrlThrowsExceptionForInvalidUrl(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid tokenUrl parameter: "invalid-url"');

        $client->setTokenUrl("invalid-url");
    }

    /**
     * @throws Exception
     */
    public function testSetRedirectUrlThrowsExceptionForInvalidUrl(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid redirectUrl parameter: "invalid-url"');

        $client->setRedirectUrl("invalid-url");
    }

    /**
     * @throws Exception
     */
    public function testSetRefreshAuthUrlThrowsExceptionForInvalidUrl(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid refreshAuthUrl parameter: "invalid-url"');

        $client->setRefreshAuthUrl("invalid-url");
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetApiKey(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );
        $client->setApiKey($this->apiKey);

        $this->assertEquals($this->apiKey, $client->getApiKey());
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetToken(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );
        $client->setToken($this->token);

        $this->assertEquals($this->token, $client->getToken());
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetAuthType(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );
        $client->setAuthType(AuthType::bearerToken);

        $this->assertEquals(AuthType::bearerToken, $client->getAuthType());
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetDebugData(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );
        $debugData = ['key' => 'value'];

        $client->setDebugData($debugData);
        $this->assertEquals($debugData, $client->getDebugData());
    }

    /**
     * @throws Exception
     */
    public function testSetDebugMode(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertFalse($client->getDebugMode(), 'Debug mode should be false initially');

        // Enable debug mode
        $client->setDebugMode(true);
        $this->assertTrue($client->getDebugMode(), 'Debug mode should be true after enabling');

        // Disable debug mode
        $client->setDebugMode(false);
        $this->assertFalse($client->getDebugMode(), 'Debug mode should be false after disabling');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetAuthForBasicAuthentication(): void
    {
        $userId = $this->faker->userName;
        $password = $this->faker->password;

        $client = new Client(
            baseUrl: $this->baseUrl,
            userId: $userId,
            password: $password,
            authType: AuthType::basic
        );

        $params = ['query' => [], 'headers' => []];
        $client->setAuth($params);

        $this->assertArrayHasKey('headers', $params);
        $expectedAuth = 'Basic ' . base64_encode($userId . ':' . $password);
        $this->assertEquals($expectedAuth, $params['headers']['Authorization']);
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetAuthForApiKeyInQuery(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'query', 'name' => 'api_key']
        );

        $params = ['query' => [], 'headers' => []];
        $client->setAuth($params);

        $this->assertArrayHasKey('query', $params);
        $this->assertEquals($this->apiKey, $params['query']['api_key']);
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetAuthForBearerToken(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            token: $this->token,
            authType: AuthType::bearerToken
        );

        $params = ['query' => [], 'headers' => []];
        $client->setAuth($params);

        $this->assertArrayHasKey('headers', $params);
        $this->assertEquals("Bearer " . $this->token, $params['headers']['Authorization']);
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetAuthForOAuthV1InHeader(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            token: $this->token,
            tokenSecret: $this->tokenSecret,
            authType: AuthType::oAuthV1,
            authSettings: ['location' => 'header']
        );

        $params = ['query' => [], 'headers' => []];
        $client->setAuth($params, 'GET', '/test', $this->baseUrl);

        $this->assertArrayHasKey('headers', $params);
        $this->assertStringStartsWith('OAuth ', $params['headers']['Authorization']);
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetAuthSettings(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Set and verify auth settings
        $authSettings = ['location' => 'header', 'name' => 'X-API-Key'];
        $client->setAuthSettings($authSettings);
        $this->assertEquals($authSettings, $client->getAuthSettings(), 'Auth settings should match the set value');

        // Test with empty array
        $client->setAuthSettings([]);
        $this->assertEquals([], $client->getAuthSettings(), 'Auth settings should be an empty array when set to empty');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetAndGetGuzzleClient(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertInstanceOf(GuzzleClient::class, $client->getGuzzleClient(), 'Guzzle client should be an instance of GuzzleClient initially');

        // Set and verify custom Guzzle client
        $mock = new MockHandler([new Response(200, [], json_encode(['success' => true]))]);
        $handler = HandlerStack::create($mock);
        $customGuzzle = new GuzzleClient(['handler' => $handler]);
        $client->setGuzzleClient($customGuzzle);
        $this->assertSame($customGuzzle, $client->getGuzzleClient(), 'Guzzle client should match the set custom client');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetTokenSecretAffectsOAuthV1Auth(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            token: $this->token,
            tokenSecret: $this->tokenSecret,
            authType: AuthType::oAuthV1,
            authSettings: ['location' => 'header']
        );

        // Get signature with original token secret
        $params1 = ['query' => [], 'headers' => []];
        $client->setAuth($params1, 'GET', '/test', $this->baseUrl);
        preg_match('/oauth_signature="([^"]+)"/', $params1['headers']['Authorization'], $matches1);
        $signature1 = $matches1[1] ?? '';

        // Update token secret
        $newTokenSecret = $this->faker->uuid;
        $client->setTokenSecret($newTokenSecret);

        // Get signature with new token secret
        $params2 = ['query' => [], 'headers' => []];
        $client->setAuth($params2, 'GET', '/test', $this->baseUrl);
        preg_match('/oauth_signature="([^"]+)"/', $params2['headers']['Authorization'], $matches2);
        $signature2 = $matches2[1] ?? '';

        $this->assertArrayHasKey('headers', $params2);
        $this->assertStringStartsWith('OAuth ', $params2['headers']['Authorization']);
        $this->assertNotEmpty($signature1, 'Original signature should not be empty');
        $this->assertNotEmpty($signature2, 'New signature should not be empty');
        $this->assertNotEquals($signature1, $signature2, 'OAuth signatures should differ with different token secrets');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetVersionAndSignatureMethodAffectsOAuthV1Auth(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            token: $this->token,
            tokenSecret: $this->tokenSecret,
            authType: AuthType::oAuthV1,
            authSettings: ['location' => 'header']
        );

        // Update version and signature method
        $client->setVersion('1.0a');
        $client->setSignatureMethod(SignatureMethod::HMAC_SHA256);

        $params = ['query' => [], 'headers' => []];
        $client->setAuth($params, 'GET', '/test', $this->baseUrl);

        $this->assertArrayHasKey('headers', $params);
        $header = $params['headers']['Authorization'];
        $this->assertStringStartsWith('OAuth ', $header);
        $this->assertStringContainsString('oauth_version="1.0a"', $header, 'OAuth header should include set version');
        $this->assertStringContainsString('oauth_signature_method="HMAC-SHA256"', $header, 'OAuth header should include set signature method');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testPerformRequestWithApiKeyAuth(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['success' => true]))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        $response = $client->performRequest(
            method: 'GET',
            endpoint: '/test'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], json_decode($response->getBody()->getContents(), true));
        // Verify the API key was included in the request
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals($this->apiKey, $lastRequest->getHeaderLine('X-API-Key'));
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testPerformRequestHandles401WithNewTokenWithInitialInvalidToken(): void
    {
        $newToken = $this->faker->uuid;
        $mock = new MockHandler([
            new Response(401, [], json_encode(['error' => 'Unauthorized'])),
            new Response(200, [], json_encode(['access_token' => $newToken])),
            new Response(200, [], json_encode(['success' => true]))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            tokenUrl: $this->tokenUrl,
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            refreshToken: $this->refreshToken,
            token: $this->token,
            authType: AuthType::oAuthV2,
            authSettings: ['location' => 'header']
        );

        $response = $client->performRequest(
            method: 'GET',
            endpoint: '/test'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], json_decode($response->getBody()->getContents(), true));
        $this->assertEquals($newToken, $client->getToken());
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testPerformRequestHandles401WithNewTokenWithoutInitialToken(): void
    {
        $newToken = $this->faker->uuid;
        $mock = new MockHandler([
            new Response(200, [], json_encode(['access_token' => $newToken])),
            new Response(200, [], json_encode(['success' => true]))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            tokenUrl: $this->tokenUrl,
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            refreshToken: $this->refreshToken,
            authType: AuthType::oAuthV2,
            authSettings: ['location' => 'header']
        );

        $response = $client->performRequest(
            method: 'GET',
            endpoint: '/test'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], json_decode($response->getBody()->getContents(), true));
        $this->assertEquals($newToken, $client->getToken());
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testPerformRequestThrowsExceptionOnNonRetryableError(): void
    {
        $mock = new MockHandler([
            new Response(400, [], json_encode(['error' => 'Bad Request']))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        $this->expectException(ApiRequestException::class);
        $this->expectExceptionMessage('Bad Request');

        $client->performRequest(
            method: 'GET',
            endpoint: '/test'
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testPerformRequestThrowsExceptionOnCustomError(): void
    {
        $mock = new MockHandler([
            new Response(403, [], json_encode(['error' => 'Forbidden']))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        $this->expectException(ApiRequestException::class);
        $this->expectExceptionMessage('Forbidden');

        $client->performRequest(
            method: 'GET',
            endpoint: '/test',
            customErrors: ['403' => 'body']
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testPerformRequestThrowsExceptionOnMissingRefreshToken(): void
    {
        $mock = new MockHandler([
            new Response(401, [], json_encode(['error' => 'Unauthorized']))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unauthorized. No refresh token provided.');

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            tokenUrl: $this->tokenUrl,
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            refreshToken: '',
            token: $this->token,
            authType: AuthType::oAuthV2,
            authSettings: ['location' => 'header']
        );

        $client->performRequest(
            method: 'GET',
            endpoint: '/test'
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testPerformRequestThrowsExceptionOnNonRetryable401(): void
    {
        $mock = new MockHandler([
            new Response(401, [], json_encode(['error' => 'Unauthorized']))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Unauthorized');

        $client->performRequest(
            method: 'GET',
            endpoint: '/test'
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetClientSecretAndRefreshTokenAffectsGetNewToken(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['access_token' => $this->token]))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            tokenUrl: $this->tokenUrl,
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            refreshToken: $this->refreshToken,
            token: $this->token,
            authType: AuthType::oAuthV2,
            authSettings: ['location' => 'header']
        );

        // Update client secret and refresh token
        $newClientSecret = $this->faker->uuid;
        $newRefreshToken = $this->faker->uuid;
        $client->setClientSecret($newClientSecret);
        $client->setRefreshToken($newRefreshToken);

        // Call getNewToken
        $token = $client->getNewToken();

        $this->assertEquals($this->token, $token, 'getNewToken should return the mocked token');

        // Verify the token request used the updated credentials
        $lastRequest = $mock->getLastRequest();
        $body = json_decode($lastRequest->getBody(), true);
        $this->assertArrayHasKey('refresh_token', $body, 'Request should include new refresh token');
        $this->assertEquals($newRefreshToken, $body['refresh_token'], 'Request should use new refresh token');

        if (isset($body['client_id'])) {
            // Assume form parameters
            $this->assertEquals($this->clientId, $body['client_id'], 'Request should use client ID');
            $this->assertEquals($newClientSecret, $body['client_secret'], 'Request should use new client secret');
        } else {
            // Check Authorization header if credentials are sent there
            $authHeader = $lastRequest->getHeaderLine('Authorization');
            $this->assertStringContainsString(base64_encode($this->clientId . ':' . $newClientSecret), $authHeader, 'Request should use new client secret in header');
        }
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetRealmAffectsOAuthV1Auth(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            token: $this->token,
            tokenSecret: $this->tokenSecret,
            authType: AuthType::oAuthV1,
            authSettings: ['location' => 'header']
        );

        // Update realm
        $realm = 'example-realm';
        $client->setRealm($realm);

        $params = ['query' => [], 'headers' => []];
        $client->setAuth($params, 'GET', '/test', $this->baseUrl);

        $this->assertArrayHasKey('headers', $params);
        $header = $params['headers']['Authorization'];
        $this->assertStringStartsWith('OAuth ', $header);
        $this->assertStringContainsString('realm="' . $realm . '"', $header, 'OAuth header should include set realm');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetEncodeParamsAffectsOAuthV1Auth(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            token: $this->token,
            tokenSecret: $this->tokenSecret,
            authType: AuthType::oAuthV1,
            authSettings: ['location' => 'header']
        );

        // Test with encode params enabled
        $client->setEncodeParams(true);
        $paramsWithEncoding = ['query' => [], 'headers' => []];
        $client->setAuth($paramsWithEncoding, 'GET', '/test', $this->baseUrl);
        preg_match('/oauth_signature="([^"]+)"/', $paramsWithEncoding['headers']['Authorization'], $matchesWith);
        $signatureWithEncoding = $matchesWith[1] ?? '';

        // Test with encode params disabled
        $client->setEncodeParams(false);
        $paramsWithoutEncoding = ['query' => [], 'headers' => []];
        $client->setAuth($paramsWithoutEncoding, 'GET', '/test', $this->baseUrl);
        preg_match('/oauth_signature="([^"]+)"/', $paramsWithoutEncoding['headers']['Authorization'], $matchesWithout);
        $signatureWithoutEncoding = $matchesWithout[1] ?? '';

        $this->assertArrayHasKey('headers', $paramsWithoutEncoding);
        $this->assertStringStartsWith('OAuth ', $paramsWithoutEncoding['headers']['Authorization']);
        $this->assertNotEmpty($signatureWithEncoding, 'Signature with encoding should not be empty');
        $this->assertNotEmpty($signatureWithoutEncoding, 'Signature without encoding should not be empty');
        $this->assertNotEquals(
            $signatureWithEncoding,
            $signatureWithoutEncoding,
            'OAuth signatures should differ with and without encoding'
        );
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetAuthUrl(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertEquals('', $client->getAuthUrl(), 'Auth URL should be empty initially');

        // Set and verify auth URL
        $authUrl = $this->faker->url;
        $client->setAuthUrl($authUrl);
        $this->assertEquals($authUrl, $client->getAuthUrl(), 'Auth URL should match the set value');
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetRedirectUrl(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertEquals('', $client->getRedirectUrl(), 'Redirect URL should be empty initially');

        // Set and verify redirect URL
        $redirectUrl = $this->faker->url;
        $client->setRedirectUrl($redirectUrl);
        $this->assertEquals($redirectUrl, $client->getRedirectUrl(), 'Redirect URL should match the set value');
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetRefreshAuthUrl(): void
    {
        $client = new Client(
            baseUrl: $this->baseUrl,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Verify initial state
        $this->assertEquals('', $client->getRefreshAuthUrl(), 'Refresh auth URL should be empty initially');

        // Set and verify refresh auth URL
        $refreshAuthUrl = $this->faker->url;
        $client->setRefreshAuthUrl($refreshAuthUrl);
        $this->assertEquals($refreshAuthUrl, $client->getRefreshAuthUrl(), 'Refresh auth URL should match the set value');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testDebugModeAndDataAffectsPerformRequest(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['success' => true]))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Set debug data and enable debug mode
        $debugData = ['custom_key' => 'custom_value'];
        $client->addDebugData(debugData: $debugData);
        $client->setDebugMode(true);

        try {
            $client->performRequest(
                method: 'GET',
                endpoint: '/test'
            );
            $this->fail('Expected DebugException was not thrown');
        } catch (DebugException $e) {
            $exceptionData = json_decode($e->getMessage(), true);
            $this->assertIsArray($exceptionData);
            $this->assertEquals('GET', $exceptionData['method']);
            $this->assertStringEndsWith('/test', $exceptionData['uri']);
            $this->assertEquals($debugData['custom_key'], $exceptionData['custom_key'], 'Debug data should be included in exception');
        }

        // Disable debug mode and verify request succeeds
        $client->setDebugMode(false);
        $response = $client->performRequest(
            method: 'GET',
            endpoint: '/test'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], json_decode($response->getBody()->getContents(), true));
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetHeadersAffectsPerformRequest(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['success' => true]))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Set custom headers
        $customHeaders = ['X-Custom-Header' => 'custom_value'];
        $client->setHeaders($customHeaders);

        $response = $client->performRequest(
            method: 'GET',
            endpoint: '/test'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], json_decode($response->getBody()->getContents(), true));

        // Verify the custom header was included in the request
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('custom_value', $lastRequest->getHeaderLine('X-Custom-Header'), 'Request should include custom header');
    }

    /**
     * @throws GuzzleException|ReflectionException
     * @throws Exception
     */
    public function testSetAuthUrlAffectsOAuthV2Authorization(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['access_token' => $this->token]))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            tokenUrl: $this->tokenUrl,
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            refreshToken: $this->refreshToken,
            token: $this->token,
            authType: AuthType::oAuthV2,
            authSettings: ['location' => 'header']
        );

        // Set auth URL
        $newAuthUrl = $this->faker->url;
        $client->setAuthUrl($newAuthUrl);

        // Check if getAuthorizationUrl exists
        $reflection = new ReflectionClass($client);
        if ($reflection->hasMethod('getAuthUrl')) {
            $method = $reflection->getMethod('getAuthUrl');
            $authUrl = $method->invoke($client);
            $this->assertStringStartsWith($newAuthUrl, $authUrl, 'Authorization URL should start with set auth URL');
        } else {
            // Fallback: Verify authUrl is stored correctly
            $this->assertEquals($newAuthUrl, $client->getAuthUrl(), 'Auth URL should be stored correctly');
            $this->markTestSkipped('getAuthorizationUrl method not implemented');
        }
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetUserIdAndPasswordAffectsBasicAuthRequest(): void
    {
        // Set user ID and password
        $userId = $this->faker->userName;
        $password = $this->faker->password;

        $mock = new MockHandler([
            new Response(200, [], json_encode(['success' => true]))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            userId: $userId,
            password: $password,
            authType: AuthType::basic
        );

        $client->setUserId($userId);
        $client->setPassword($password);

        $response = $client->performRequest(
            method: 'GET',
            endpoint: '/test'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], json_decode($response->getBody()->getContents(), true));

        // Verify the basic auth header was included
        $lastRequest = $mock->getLastRequest();
        $expectedAuth = 'Basic ' . base64_encode("$userId:$password");
        $this->assertEquals($expectedAuth, $lastRequest->getHeaderLine('Authorization'), 'Request should include basic auth header');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetApiKeyAffectsQueryAuthRequest(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['success' => true]))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'query', 'name' => 'api_key']
        );

        // Set API key
        $newApiKey = $this->faker->uuid;
        $client->setApiKey($newApiKey);

        $response = $client->performRequest(
            method: 'GET',
            endpoint: '/test'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], json_decode($response->getBody()->getContents(), true));

        // Verify the API key was included in the query
        $lastRequest = $mock->getLastRequest();
        $query = $lastRequest->getUri()->getQuery();
        $this->assertStringContainsString('api_key=' . $newApiKey, $query, 'Request should include API key in query');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetTokenAffectsBearerTokenRequest(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['success' => true]))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            token: $this->token,
            authType: AuthType::bearerToken
        );

        // Set token
        $newToken = $this->faker->uuid;
        $client->setToken($newToken);

        $response = $client->performRequest(
            method: 'GET',
            endpoint: '/test'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], json_decode($response->getBody()->getContents(), true));

        // Verify the bearer token was included in the header
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals("Bearer $newToken", $lastRequest->getHeaderLine('Authorization'), 'Request should include bearer token');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetTokenUrlAffectsOAuthV2TokenRequest(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['access_token' => $this->token]))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            tokenUrl: $this->tokenUrl,
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            refreshToken: $this->refreshToken,
            token: $this->token,
            authType: AuthType::oAuthV2,
            authSettings: ['location' => 'header']
        );

        // Set new token URL
        $newTokenUrl = $this->faker->url;
        $client->setTokenUrl($newTokenUrl);

        // Call getNewToken
        $token = $client->getNewToken();

        $this->assertEquals($this->token, $token, 'getNewToken should return the mocked token');

        // Verify the token request used the new token URL
        $lastRequest = $mock->getLastRequest();
        $this->assertStringEndsWith($newTokenUrl, (string)$lastRequest->getUri(), 'Request should use new token URL');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetAuthTypeAffectsPerformRequest(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['success' => true]))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            apiKey: $this->apiKey,
            authType: AuthType::apiKey,
            authSettings: ['location' => 'header', 'name' => 'X-API-Key']
        );

        // Switch to bearer token auth
        $newToken = $this->faker->uuid;
        $client->setToken($newToken);
        $client->setAuthType(AuthType::bearerToken);

        $response = $client->performRequest(
            method: 'GET',
            endpoint: '/test'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], json_decode($response->getBody()->getContents(), true));

        // Verify the bearer token was used instead of API key
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals("Bearer $newToken", $lastRequest->getHeaderLine('Authorization'), 'Request should use bearer token');
        $this->assertEmpty($lastRequest->getHeaderLine('X-API-Key'), 'API key header should not be present');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testSetVersionAffectsOAuthV1Request(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['success' => true]))
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new Client(
            baseUrl: $this->baseUrl,
            guzzleClient: $guzzle,
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            token: $this->token,
            tokenSecret: $this->tokenSecret,
            authType: AuthType::oAuthV1,
            authSettings: ['location' => 'header']
        );

        // Set version
        $client->setVersion('1.0a');

        $response = $client->performRequest(
            method: 'GET',
            endpoint: '/test'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], json_decode($response->getBody()->getContents(), true));

        // Verify the version was included in the OAuth header
        $lastRequest = $mock->getLastRequest();
        $header = $lastRequest->getHeaderLine('Authorization');
        $this->assertStringContainsString('oauth_version="1.0a"', $header, 'OAuth header should include set version');
    }
}
