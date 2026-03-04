<?php

namespace Tests;

use Anibalealvarezs\ApiSkeleton\Client;
use Anibalealvarezs\ApiSkeleton\Enums\AuthType;
use Anibalealvarezs\ApiSkeleton\Classes\Exceptions\ApiRequestException;
use Anibalealvarezs\ApiSkeleton\Classes\Exceptions\AuthenticationException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ErrorHandlingTest extends TestCase
{
    protected string $baseUrl = 'http://localhost';

    protected function createMockedClient(array $responses, array $config = []): Client
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        return new Client(...(array_merge(['baseUrl' => $this->baseUrl, 'guzzleClient' => $guzzle, 'authType' => AuthType::none], $config)));
    }

    public function testFalsy200ErrorDetection(): void
    {
        $client = $this->createMockedClient([
            new Response(200, [], json_encode(['success' => false, 'message' => 'API Error']))
        ]);

        $client->setResponseErrorDetector('success');
        $client->setErrorMessageParser('message');

        $this->expectException(ApiRequestException::class);
        $this->expectExceptionMessage('API Error');

        $client->performRequest('GET', '/test');
    }

    public function testFalsy200ErrorDetectionWithDotNotation(): void
    {
        $client = $this->createMockedClient([
            new Response(200, [], json_encode(['meta' => ['status' => 'error'], 'error' => ['detail' => 'Nested Error']]))
        ]);

        $client->setResponseErrorDetector('meta.status');
        $client->setErrorMessageParser('error.detail');

        $this->expectException(ApiRequestException::class);
        $this->expectExceptionMessage('Nested Error');

        $client->performRequest('GET', '/test');
    }

    public function test429RateLimitHandling(): void
    {
        // Mock 429 followed by 200
        $client = $this->createMockedClient([
            new Response(429, ['Retry-After' => '1'], 'Too Many Requests'),
            new Response(200, [], json_encode(['success' => true]))
        ]);

        $client->setDelayHeader('Retry-After');
        
        // Use a small sleep or mock usleep if possible, but the code uses usleep directly.
        // To avoid long tests, we might want to check the logic without actually waiting long.
        // However, for simplicity, let's just assert it works.
        
        $response = $client->performRequest('GET', '/test');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testOAuth2TokenRefreshLoopPrevention(): void
    {
        // Mock persistent 401s
        $client = $this->createMockedClient([
            new Response(401, [], 'Unauthorized'), // Initial request fails
            new Response(200, [], json_encode(['access_token' => 'new-token'])), // Token refresh succeeds
            new Response(401, [], 'Still Unauthorized'), // Retry fails
        ], [
            'authType' => AuthType::oAuthV2,
            'clientId' => 'id',
            'clientSecret' => 'secret',
            'refreshToken' => 'refresh',
            'tokenUrl' => 'http://localhost/token',
            'token' => 'initial-token',
            'authSettings' => ['location' => 'header']
        ]);

        $this->expectException(ApiRequestException::class);
        $this->expectExceptionCode(401);

        $client->performRequest('GET', '/test');
    }

    public function testFalsy200InRetryLogic(): void
    {
        // Mock 401 -> Refresh -> 200 (but body error)
        $client = $this->createMockedClient([
            new Response(401, [], 'Unauthorized'), // Initial request fails
            new Response(200, [], json_encode(['access_token' => 'new-token'])), // Token refresh succeeds
            new Response(200, [], json_encode(['success' => false, 'message' => 'Error after refresh'])), // Retry returns falsy 200
        ], [
            'authType' => AuthType::oAuthV2,
            'clientId' => 'id',
            'clientSecret' => 'secret',
            'refreshToken' => 'refresh',
            'tokenUrl' => 'http://localhost/token',
            'token' => 'initial-token',
            'authSettings' => ['location' => 'header']
        ]);

        $client->setResponseErrorDetector('success');
        $client->setErrorMessageParser('message');

        $this->expectException(ApiRequestException::class);
        $this->expectExceptionMessage('Error after refresh');

        $client->performRequest('GET', '/test');
    }

    public function testDefaultBehaviorWithoutDetector(): void
    {
        $client = $this->createMockedClient([
            new Response(200, [], json_encode(['success' => false, 'message' => 'Ignored Error']))
        ]);

        // No detector set
        $response = $client->performRequest('GET', '/test');
        $this->assertEquals(200, $response->getStatusCode());
        $contents = json_decode($response->getBody()->getContents(), true);
        $this->assertFalse($contents['success']);
    }
}
