# API Skeleton

## Installation Instructions

Require the package in the `composer.json` file of your project, and map the package in the `repositories` section (including its dependencies).

```json
{
    "require": {
        "php": ">=8.1",
        "anibalealvarezs/api-client-skeleton": "@dev"
    },
    "repositories": [
        {
            "type": "git",
            "url": "git@github.com:anibalealvarezs/api-client-skeleton.git"
        },
        {
            "type": "git",
            "url": "git@github.com:anibalealvarezs/oauthv-1.git"
        }
    ]
}
```

Note: In order to require the package, you need to have a valid SSH key configured in your CHMW's GitLab account.

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

Required parameters:
- `baseUrl`: *String*  
  Base URL for API requests.
- `apiKey`: *String*  
  API key to be used for authentication.
- `authSettings`: *Array*  
  Authentication settings.  
    Required keys:
    - `location`: *String*  
      Location of the API key. Possible values: `header` or `query`.
    - `name`: *String*  
      Name of the header or query parameter that will contain the API key.
    Optional keys:
    - `headerPrefix`: *String*  
      Prefix to be added to the header value.  
      Example: `Bearer `  
      Notes:  
        This key is only required if `location` is set to `header`.  
        Include a trailing space if you want to separate the prefix from the value with a space.  

Optional parameters:
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

Required parameters:
- `baseUrl`: *String*  
  Base URL for API requests.
- `token`: *String*  
  Token to be used for authentication.
- `authSettings`: *Array*  
  Authentication settings.  
    Optional keys:
    - `headerPrefix`: *String*  
      Prefix to be added to the header value.  
      Example: `Bearer `  
      Note: Include a trailing space if you want to separate the prefix from the value with a space.

Optional parameters:
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

Required parameters:
- `baseUrl`: *String*  
  Base URL for API requests.
- `clientId`: *String*  
  Consumer ID to be used for the OAuth flow.
- `clientSecret`: *String*  
  Consumer Secret to be used for the OAuth flow.
- `authSettings`: *Array*  
  Authentication settings.  
  Required keys:
  - `location`: *String*  
    Location of the API key. Possible values: `header`, `body` or `query`.   
  Optional keys:
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
  Token Secret to be used for authentication.
- `version`: *String*  
  OAuth version to be used.  
  Possible values: `1.0`, `1.0a` or `2.0`.  
  Default value: `1.0`
- `realm`: *String*  
  Realm to be used for the OAuth flow.  
  Default value: `""`

Optional parameters:
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

Required parameters:
- `baseUrl`: *String*  
  Base URL for API requests.
- `authUrl`: *String*  
  URL to be used for the OAuth authorization flow.
- `tokenUrl`: *String*  
  URL to be used for the OAuth token flow.
- `refreshAuthUrl`: *String*  
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
  Required keys:
  - `location`: *String*  
    Location of the API key. Possible values: `header` or `query`.
  - `name`: *String*  
    Name of the header or query parameter that will contain the API key.   
  Optional keys:
  - `headerPrefix`: *String*  
    Prefix to be added to the header value.  
    Example: `Bearer `  
    Notes:  
    This key is only required if `location` is set to `header`.  
    Include a trailing space if you want to separate the prefix from the value with a space.

Optional parameters:
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

## Endpoint Methods

All endpoint methods must execute at the end the `performRequest()` method, which is responsible for sending the request to the API and returning the response's body.
Example:

```php
    public function getEntityCount(
        // Optional parameters
    ): array {
        $query =[
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

### performRequest() method parameters:

Required parameters:
- `method`: *String*  
  HTTP method to be used for the request.  
  Possible values: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`.
- `endpoint`: *String*  
  Endpoint to be used for the request.  
  Example: `entity/count.json`

Optional parameters:
- `query`: *Array*  
  Query parameters to be sent along with the request.  
  Example: `['limit' => 10]`
- `body`: *Array*  
  Body parameters to be sent along with the request.  
  Example: `['name' => 'John Doe']`
- `formParams`: *Array*  
  Form parameters to be sent along with the request.  
  Example: `['name' => 'John Doe']`
- `baseUrl`: *String*  
  Base URL to be used for the request.  
  Example: `https://my.api.url/`
- `headers`: *Array*  
  Headers to be sent along with the request.  
  Example: `['Content-Type' => 'application/json']`
- `additionalHeaders`: *Array*  
  Additional headers to be appended to the current list of headers.  
  Example: `['Content-Type' => 'application/json']`
- `cookies`: *Array*  
  Cookies to be sent along with the request.  
  Example: `['name' => 'John Doe']`
- `verify`: *Boolean*  
  Whether to verify the SSL certificate or not.  
  Example: `true`
- `allowNewToken`: *Boolean*  
  Whether to allow a new token to be generated or not.  
  Example: `true`
- `pathToSave`: *String*  
  Local path to save the file to.  
  Example: `/path/to/save/file`
- `stream`: *Boolean*  
  Whether to stream the response or not.  
  Example: `true`
- `errorMessageNesting`: *Array*  
  Keys to be used to get the error message from the response.
  Example: `['error' => ['message']]`
- `sleep`: *Integer*  
  Number of seconds to wait before retrying the request.  
  Example: `5`
