# API Skeleton - Methods Documentation

This document extends the usage instructions for the `Client` class in the `anibalealvarezs/api-client-skeleton` package, focusing on public methods for authentication and request handling.

Configuration methods (setters and getters) are documented in a separate file ([API Skeleton - Configuration Methods Documentation](docs/Configuration/README.MD)).

Check [this file](docs/Tests/README.MD) for documentation regarding the package's tests.

## Installation Instructions

Require the package in the `composer.json` file of your project, and map the package in the `repositories` section.

```json
{
    "require": {
        "php": ">=8.1",
        "anibalealvarezs/api-client-skeleton": "@dev"
    },
    "repositories": [
        {
          "type": "composer", "url": "https://satis.anibalalvarez.com/"
        }
    ]
}
```

## Usage

Create a new class that extends the corresponding client class according to the authentication method you want to use.

```php
class MyApi extends \Anibalealvarezs\ApiSkeleton\Clients\ApiKeyClient
{
    //
}
```

## Constructor Overriding

Override the constructor and call the parent constructor with the corresponding parameters.

```php
class MyApi extends \Anibalealvarezs\ApiSkeleton\Clients\ApiKeyClient
{
    /**
     * @param string $apiKey
     * @throws RequestException|GuzzleException
     */
    public function __construct(
        string $apiKey,
    ) {
        return parent::__construct(
            // parameters
        );
    }
}
```

### Available Authentication Clients

---

#### API Key (ApiKeyClient)

```php
class MyApi extends \Anibalealvarezs\ApiSkeleton\Clients\ApiKeyClient
{
    /**
     * @param string $apiKey
     * @throws RequestException|GuzzleException
     */
    public function __construct(
        string $apiKey,
    ) {
        return parent::__construct(
            baseUrl: 'https://my.api.url/',
            apiKey: $apiKey,
            authSettings: [
                'location' => 'header',
                'name' => 'Authorization',
            ],
            defaultHeaders: [
                'Content-Type' => 'application/json',
            ],
        );
    }
}
```

**Required parameters:**
- `baseUrl`: *String*  
  Base URL for API requests.
- `apiKey`: *String*  
  API key to be used for authentication.
- `authSettings`: *Array*  
  Authentication settings.  
  **Required keys:**
  - `location`: *String*  
    Location of the API key. Possible values: `header` or `query`.
  - `name`: *String*  
    Name of the header or query parameter that will contain the API key.
    **Optional keys:**
  - `headerPrefix`: *String*  
    Prefix to be added to the header value.  
    Example: `Bearer `  
    Notes:  
    This key is only required if `location` is set to `header`.  
    Include a trailing space if you want to separate the prefix from the value with a space.

**Optional parameters:**
- `defaultHeaders`: *Array*  
  Default headers that will be sent along with all requests (unless overridden on any method).  
  Example: `['Content-Type' => 'application/json']`

---

#### Bearer Token (BearerTokenClient)

```php
class MyApi extends \Anibalealvarezs\ApiSkeleton\Clients\BearerTokenClient
{
    /**
     * @param string $token
     * @throws RequestException|GuzzleException
     */
    public function __construct(
        string $token,
    ) {
        return parent::__construct(
            baseUrl: 'https://my.api.url/',
            token: $token,
            authSettings: [
                'headerPrefix' => 'Bearer ',
            ],
        );
    }
}
```

**Required parameters:**
- `baseUrl`: *String*  
  Base URL for API requests.
- `token`: *String*  
  Token to be used for authentication.
- `authSettings`: *Array*  
  Authentication settings.  
  **Optional keys:**
  - `headerPrefix`: *String*  
    Prefix to be added to the header value.  
    Example: `Bearer `  
    Note: Include a trailing space if you want to separate the prefix from the value with a space.

**Optional parameters:**
- `defaultHeaders`: *Array*  
  Default headers that will be sent along with all requests (unless overridden on any method).  
  Example: `['Content-Type' => 'application/json']`

---

#### OAuthV1 (OAuthV1Client)

```php
class MyApi extends \Anibalealvarezs\ApiSkeleton\Clients\OAuthV1Client
{
    /**
     * @param string $baseUrl
     * @param string $consumerId
     * @param string $consumerSecret
     * @param array $defaultHeaders
     * @param string $token
     * @param string $tokenSecret
     * @param string $version
     * @param string $realm
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
        );
    }
}
```

**Required parameters:**
- `baseUrl`: *String*  
  Base URL for API requests.
- `clientId`: *String*  
  Consumer ID to be used for the OAuth flow.
- `clientSecret`: *String*  
  Consumer Secret to be used for the OAuth flow.
- `authSettings`: *Array*  
  Authentication settings.  
  **Required keys:**
  - `location`: *String*  
    Location of the API key. Possible values: `header`, `body` or `query`.   
    **Optional keys:**
  - `name`: *String*  
    Name of the header or query parameter that will contain the API key.   
    Notes:  
    This key is only required if `location` is set to `query`.
  - `headerPrefix`: *String*  
    Prefix to be added to the header value.  
    Example: `OAuth `  
    Notes:  
    This key is only required if `location` is set to `header`. If missing, `OAuth ` will be used as default.  
    Include a trailing space if you want to separate the prefix from the value with a space.
- `token`: *String*  
  Access token to be used for authentication.
- `tokenSecret`: *String*  
  Token SECRET to be used for authentication.
- `version`: *String*  
  OAuth version to be used.  
  Possible values: `1.0`, `1.0a` or `2.0`.  
  Default value: `1.0`
- `realm`: *String*  
  Realm to be used for the OAuth flow.  
  Default value: `""`

**Optional parameters:**
- `defaultHeaders`: *Array*  
  Default headers that will be sent along with all requests (unless overridden on any method).  
  Example: `['Content-Type' => 'application/json']`

---

#### OAuthV2 (OAuthV2Client)

```php
class MyApi extends \Anibalealvarezs\ApiSkeleton\Clients\OAuthV2Client
{
    /**
     * @param string $baseUrl
     * @param string $redirectUrl
     * @param string $clientId
     * @param string $clientSecret
     * @param string $refreshToken
     * @param array $scopes
     * @param string $token
     * @throws GuzzleException
     */
    public function __construct(
        string $baseUrl,
        string $redirectUrl,
        string $clientId,
        string $clientSecret,
        string $refreshToken,
        array $scopes = [],
        string $token = "",
    ) {
        return parent::__construct(
            baseUrl: $baseUrl,
            authUrl: "https://my.api.url/",
            tokenUrl: "https://my.api.url/token",
            refreshAuthUrl: null,
            redirectUrl: $redirectUrl,
            clientId: $clientId,
            clientSecret: $clientSecret,
            refreshToken: $refreshToken,
            authSettings: [
                'location' => 'header',
                'headerPrefix' => 'Bearer ',
            ],
            refreshTokenHeaders: [
                "Content-Type" => "application/x-www-form-urlencoded;charset=UTF-8",
            ],
            scopes: $scopes,
            token: $token,
        );
    }
}
```

**Required parameters:**
- `baseUrl`: *String*  
  Base URL for API requests.
- `authUrl`: *String*  
  URL to be used for the OAuth authorization flow.
- `tokenUrl`: *String*  
  URL to be used for the OAuth token flow.
- `refreshAuth sunUrl`: *String*  
  URL to be used for the OAuth refresh token flow (if different from `authUrl`). Can be `null` if the refresh token flow is not supported.
- `redirectUrl`: *String*  
  URL to be used as the redirect URL for the OAuth flow.
- `clientId`: *String*  
  Client ID to be used for the OAuth flow.
- `clientSecret`: *String*  
  Client Secret to be used for the OAuth flow.
- `refreshToken`: *String*  
  Refresh token to refresh the access token.
- `authSettings`: *Array*  
  Authentication settings.  
  **Required keys:**
  - `location`: *String*  
    Location of the API key. Possible values: `header` or `query`.
  - `name`: *String*  
    Name of the header or query parameter that will contain the API key.   
    **Optional keys:**
  - `headerPrefix`: *String*  
    Prefix to be added to the header value.  
    Example: `Bearer `  
    Notes:  
    This key is only required if `location` is set to `header`.  
    Include a trailing space if you want to separate the prefix from the value with a space.

**Optional parameters:**
- `defaultHeaders`: *Array*  
  Default headers that will be sent along with all requests (unless overridden on any method).  
  Example: `['Content-Type' => 'application/json']`
- `refreshTokenHeaders`: *Array*  
  Headers to be sent along with the refresh token request.  
  Example: `['Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8']`
- `scopes`: *Array*  
  Scopes that will limit the access token capabilities.
- `token`: *String*  
  Access token to be used with every request.

## Authentication Methods

The `Client` class provides public methods to handle authentication setup and token management.

### `setAuth(array &$params, string $method = "", string $endpoint = "", string $baseUrl = ""): void`

Configures authentication parameters for a request based on the client’s authentication type.

**Parameters:**
- `params`: *Array* (passed by reference)  
  Request parameters (e.g., `query`, `headers`) that will be modified to include authentication details.
- `method`: *String* (optional)  
  HTTP method for the request (e.g., `GET`, `POST`). Required for OAuth v1.
- `endpoint`: *String* (optional)  
  API endpoint for the request (e.g., `entity/count.json`). Required for OAuth v1.
- `baseUrl`: *String* (optional)  
  Base URL for the request. Defaults to the client’s base URL.

**Throws:**
- `Exception`: If authentication setup fails due to invalid settings or missing credentials.

**Example:**
```php
$params = ['query' => [], 'headers' => []];
$client->setAuth($params, 'GET', 'entity/count.json');
```

---

### `getNewToken(): ?string`

Fetches a new access token using the refresh token for OAuth v2 authentication.

struttural integrity

**Returns:**
- *String|null*: The new access token or `null` if the request fails.

**Throws:**
- `GuzzleException`: If the token request fails.

**Example:**
```php
$newToken = $client->getNewToken();
```

---

## Request Handling Methods

The `Client` class provides public methods to perform and manage API requests.

### `performRequest(...)`

Sends an HTTP request to the specified API endpoint and handles authentication, retries, and error responses.

**Parameters:**

**Required:**
- `method`: *String*  
  HTTP method to be used for the request (e.g., `GET`, `POST`, `PUT`, `PATCH`, `DELETE`).
- `endpoint`: *String*  
  Endpoint for the request (e.g., `entity/count.json`).

**Optional:**
- `query`: *Array*  
  Query parameters for the request (e.g., `['limit' => 10]`).
- `body`: *String|Array*  
  Request body (e.g., JSON string or array of parameters).
- `form_params`: *Array*  
  Form parameters for the request (e.g., `['name' => 'John Doe']`).
- `baseUrl`: *String*  
  Base URL for the request. Defaults to the client’s base URL.
- `headers`: *Array*  
  Headers for the request (e.g., `['Content-Type' => 'application/json']`).
- `additionalHeaders`: *Array*  
  Additional headers to append to the request headers.
- `cookies`: *CookieJar|null*  
  Cookies to be sent with the request.
- `verify`: *Boolean*  
  Whether to verify the SSL certificate (default: `false`).
- `allowNewToken`: *Boolean*  
  Whether to allow automatic token refresh for OAuth v2 (default: `true`).
- `pathToSave`: *String*  
  Local path to save the response (e.g., `/path/to/save/file`).
- `stream`: *Boolean|null*  
  Whether to stream the response.
- `errorMessageNesting`: *Array|null*  
  Keys to extract error messages from the response (e.g., `['error' => 'message']`).
- `sleep`: *Integer*  
  Microseconds to wait before retrying (default: `0`).
- `customErrors`: *Array*  
  Custom error mappings for specific HTTP status codes.
- `ignoreAuth`: *Boolean*  
  Whether to skip authentication for the request (default: `false`).

**Returns:**
- *Response*: The Guzzle HTTP response object.

**Throws:**
- `ApiRequestException`: For general request errors.
- `AuthenticationException`: For authentication failures.
- `DebugException`: When debug mode is enabled, containing debug data.
- `GuzzleException`: For Guzzle-related errors.
- `Exception`: For other unexpected errors.

**Example:**
```php
$response = $client->performRequest(
    method: 'GET',
    endpoint: 'entity/count.json',
    query: ['limit' => 10],
    headers: ['Content-Type' => 'application/json']
);
return json_decode($response->getBody()->getContents(), true);
```

---

## Endpoint Methods

All endpoint methods must execute the `performRequest()` method at the end, which is responsible for sending the request to the API and returning the response's body.

**Example:**
```php
public function getEntityCount(
    // Optional parameters
): array {
    $query = [
        // Query parameters
    ];

    // Request the spreadsheet data
    $response = $this->performRequest(
        method: "GET",
        endpoint: "entity/count.json",
        query: $query,
    );
    // Return response
    return json_decode($response->getBody()->getContents(), true);
}
```

---

## Usage Example

Below is an example of creating a custom API client that extends `ApiKeyClient` and uses the authentication and request handling methods:

```php
class MyApi extends \Anibalealvarezs\ApiSkeleton\Clients\ApiKeyClient
{
    public function __construct(string $apiKey)
    {
        parent::__construct(
            baseUrl: 'https://my.api.url/',
            apiKey: $apiKey,
            authSettings: [
                'location' => 'header',
                'name' => 'Authorization',
                'headerPrefix' => 'Bearer '
            ],
            defaultHeaders: [
                'Content-Type' => 'application/json'
            ]
        );
    }

    public function getEntityCount(int $limit = 10): array
    {
        $query = ['limit' => $limit];
        $response = $this->performRequest(
            method: 'GET',
            endpoint: 'entity/count.json',
            query: $query
        );
        return json_decode($response->getBody()->getContents(), true);
    }
}
```

This example demonstrates how to configure the client with an API key and define a custom endpoint method that uses `performRequest` to fetch data.

---
