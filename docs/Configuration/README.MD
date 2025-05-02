# API Skeleton - Configuration Methods Documentation

This document provides detailed documentation for the public setter and getter methods of the `Client` class in the `anibalealvarezs/api-client-skeleton` package. These methods are used to configure the API client’s behavior, authentication settings, and request parameters.

---

## Configuration Methods

### `setBaseUrl(string $baseUrl): void`

Sets the base URL for API requests.

**Parameters:**
- `baseUrl`: *String*  
  The base URL for API requests (e.g., `https://my.api.url/`).

**Throws:**
- `InvalidArgumentException`: If the base URL is empty or invalid.

**Example:**
```php
$client->setBaseUrl('https://my.api.url/');
```

---

### `getBaseUrl(): string`

Retrieves the base URL for API requests.

**Returns:**
- *String*: The configured base URL.

**Example:**
```php
$baseUrl = $client->getBaseUrl();
```

---

### `setApiKey(string $apiKey): void`

Sets the API key for authentication.

**Parameters:**
- `apiKey`: *String*  
  The API key to be used for authentication.

**Example:**
```php
$client->setApiKey('your-api-key');
```

---

### `getApiKey(): string`

Retrieves the configured API key.

**Returns:**
- *String*: The API key.

**Example:**
```php
$apiKey = $client->getApiKey();
```

---

### `setToken(string $token): void`

Sets the access token for authentication.

**Parameters:**
- `token`: *String*  
  The access token to be used for authentication.

**Example:**
```php
$client->setToken('your-access-token');
```

---

### `getToken(): string`

Retrieves the configured access token.

**Returns:**
- *String*: The access token.

**Example:**
```php
$token = $client->getToken();
```

---

### `setTokenSecret(string $tokenSecret): void`

Sets the token secret for OAuth v1 authentication.

**Parameters:**
- `tokenSecret`: *String*  
  The token secret to be used for OAuth v1 authentication.

**Example:**
```php
$client->setTokenSecret('your-token-secret');
```

---

### `getTokenSecret(): string`

Retrieves the configured token secret.

**Returns:**
- *String*: The token secret.

**Example:**
```php
$tokenSecret = $client->getTokenSecret();
```

---

### `setAuthType(AuthType $authType): void`

Sets the authentication type for the client.

**Parameters:**
- `authType`: *AuthType*  
  The authentication type (e.g., `AuthType::apiKey`, `AuthType::oAuthV1`, `AuthType::oAuthV2`, `AuthType::bearerToken`, `AuthType::basic`, `AuthType::none`).

**Example:**
```php
$client->setAuthType(AuthType::bearerToken);
```

---

### `getAuthType(): AuthType`

Retrieves the configured authentication type.

**Returns:**
- *AuthType*: The authentication type.

**Example:**
```php
$authType = $client->getAuthType();
```

---

### `setHeaders(array $headers): void`

Sets the default headers for all requests.

**Parameters:**
- `headers`: *Array*  
  An array of headers to be sent with all requests (e.g., `['Content-Type' => 'application/json']`).

**Example:**
```php
$client->setHeaders(['Content-Type' => 'application/json']);
```

---

### `getHeaders(): array`

Retrieves the configured default headers.

**Returns:**
- *Array*: The default headers.

**Example:**
```php
$headers = $client->getHeaders();
```

---

### `setAuthSettings(array $authSettings): void`

Sets the authentication settings for the client.

**Parameters:**
- `authSettings`: *Array*  
  Authentication settings specific to the authentication type.  
  Example for API Key: `['location' => 'header', 'name' => 'Authorization']`  
  Example for OAuth v2: `['location' => 'header', 'headerPrefix' => 'Bearer ']`

**Example:**
```php
$client->setAuthSettings(['location' => 'header', 'name' => 'Authorization']);
```

---

### `getAuthSettings(): array`

Retrieves the configured authentication settings.

**Returns:**
- *Array*: The authentication settings.

**Example:**
```php
$authSettings = $client->getAuthSettings();
```

---

### `setDebugMode(bool $debugMode): void`

Enables or disables debug mode, which collects additional request information for debugging purposes.

**Parameters:**
- `debugMode`: *Boolean*  
  Whether to enable debug mode (`true`) or disable it (`false`).

**Example:**
```php
$client->setDebugMode(true);
```

---

### `getDebugMode(): bool`

Retrieves the debug mode status.

**Returns:**
- *Boolean*: Whether debug mode is enabled.

**Example:**
```php
$isDebugMode = $client->getDebugMode();
```

---

### `addDebugData(array $debugData): void`

Adds debug data to the client’s debug information store.

**Parameters:**
- `debugData`: *Array*  
  Debug data to be added (e.g., request details, headers, or OAuth debug information).

**Example:**
```php
$client->addDebugData(['request' => 'GET /entity/count.json']);
```

---

### `getDebugData(): array`

Retrieves the collected debug data.

**Returns:**
- *Array*: The collected debug data.

**Example:**
```php
$debugData = $client->getDebugData();
```

---

### `setClientId(string $clientId): void`

Sets the client ID for OAuth authentication.

**Parameters:**
- `clientId`: *String*  
  The client ID to be used for OAuth authentication.

**Example:**
```php
$client->setClientId('your-client-id');
```

---

### `getClientId(): string`

Retrieves the configured client ID.

**Returns:**
- *String*: The client ID.

**Example:**
```php
$clientId = $client->getClientId();
```

---

### `setClientSecret(string $clientSecret): void`

Sets the client secret for OAuth authentication.

**Parameters:**
- `clientSecret`: *String*  
  The client secret to be used for OAuth authentication.

**Example:**
```php
$client->setClientSecret('your-client-secret');
```

---

### `getClientSecret(): string`

Retrieves the configured client secret.

**Returns:**
- *String*: The client secret.

**Example:**
```php
$clientSecret = $client->getClientSecret();
```

---

### `updateClientSecret(string $clientSecret): void`

Updates the client secret and clears the current token.

**Parameters:**
- `clientSecret`: *String*  
  The new client secret to be used for OAuth authentication.

**Example:**
```php
$client->updateClientSecret('new-client-secret');
```

---

### `setRefreshToken(string $refreshToken): void`

Sets the refresh token for OAuth v2 authentication.

**Parameters:**
- `refreshToken`: *String*  
  The refresh token to be used for OAuth v2 authentication.

**Example:**
```php
$client->setRefreshToken('your-refresh-token');
```

---

### `getRefreshToken(): string`

Retrieves the configured refresh token.

**Returns:**
- *String*: The refresh token.

**Example:**
```php
$refreshToken = $client->getRefreshToken();
```

---

### `updateRefreshToken(string $refreshToken): void`

Updates the refresh token and clears the current token.

**Parameters:**
- `refreshToken`: *String*  
  The new refresh token to be used for OAuth v2 authentication.

**Example:**
```php
$client->updateRefreshToken('new-refresh-token');
```

---

### `setRefreshTokenHeaders(array $refreshTokenHeaders): void`

Sets the headers to be used for refresh token requests in OAuth v2.

**Parameters:**
- `refreshTokenHeaders`: *Array*  
  Headers to be sent with refresh token requests (e.g., `['Content-Type' => 'application/x-www-form-urlencoded']`).

**Example:**
```php
$client->setRefreshTokenHeaders(['Content-Type' => 'application/x-www-form-urlencoded']);
```

---

### `getRefreshTokenHeaders(): array`

Retrieves the configured refresh token headers.

**Returns:**
- *Array*: The refresh token headers.

**Example:**
```php
$refreshTokenHeaders = $client->getRefreshTokenHeaders();
```

---

### `setUserId(string $userId): void`

Sets the user ID for basic authentication.

**Parameters:**
- `userId`: *String*  
  The user ID to be used for basic authentication.

**Example:**
```php
$client->setUserId('your-user-id');
```

---

### `getUserId(): string`

Retrieves the configured user ID.

**Returns:**
- *String*: The user ID.

**Example:**
```php
$userId = $client->getUserId();
```

---

### `setPassword(string $password): void`

Sets the password for basic authentication.

**Parameters:**
- `password`: *String*  
  The password to be used for basic authentication.

**Example:**
```php
$client->setPassword('your-password');
```

---

### `getPassword(): string`

Retrieves the configured password.

**Returns:**
- *String*: The password.

**Example:**
```php
$password = $client->getPassword();
```

---

### `setAuthUrl(string $authUrl): void`

Sets the authorization URL for OAuth flows.

**Parameters:**
- `authUrl`: *String*  
  The URL to be used for OAuth authorization (e.g., `https://my.api.url/auth`).

**Throws:**
- `InvalidArgumentException`: If the URL is invalid.

**Example:**
```php
$client->setAuthUrl('https://my.api.url/auth');
```

---

### `getAuthUrl(): string`

Retrieves the configured authorization URL.

**Returns:**
- *String*: The authorization URL.

**Example:**
```php
$authUrl = $client->getAuthUrl();
```

---

### `setRefreshAuthUrl(string $refreshAuthUrl): void`

Sets the refresh authorization URL for OAuth flows.

**Parameters:**
- `refreshAuthUrl`: *String*  
  The URL to be used for OAuth refresh token flow (e.g., `https://my.api.url/refresh`).

**Throws:**
- `InvalidArgumentException`: If the URL is invalid.

**Example:**
```php
$client->setRefreshAuthUrl('https://my.api.url/refresh');
```

---

### `getRefreshAuthUrl(): string`

Retrieves the configured refresh authorization URL.

**Returns:**
- *String*: The refresh authorization URL.

**Example:**
```php
$refreshAuthUrl = $client->getRefreshAuthUrl();
```

---

### `setTokenUrl(string $tokenUrl): void`

Sets the token URL for OAuth flows.

**Parameters:**
- `tokenUrl`: *String*  
  The URL to be used for OAuth token requests (e.g., `https://my.api.url/token`).

**Throws:**
- `InvalidArgumentException`: If the URL is invalid.

**Example:**
```php
$client->setTokenUrl('https://my.api.url/token');
```

---

### `getTokenUrl(): string`

Retrieves the configured token URL.

**Returns:**
- *String*: The token URL.

**Example:**
```php
$tokenUrl = $client->getTokenUrl();
```

---

### `setRedirectUrl(string $redirectUrl): void`

Sets the redirect URL for OAuth flows.

**Parameters:**
- `redirectUrl`: *String*  
  The URL to redirect to after OAuth authorization (e.g., `https://my.app/callback`).

**Throws:**
- `InvalidArgumentException`: If the URL is invalid.

**Example:**
```php
$client->setRedirectUrl('https://my.app/callback');
```

---

### `getRedirectUrl(): string`

Retrieves the configured redirect URL.

**Returns:**
- *String*: The redirect URL.

**Example:**
```php
$redirectUrl = $client->getRedirectUrl();
```

---

### `setScopes(array $scopes): void`

Sets the scopes for OAuth authentication.

**Parameters:**
- `scopes`: *Array*  
  The scopes to limit the access token capabilities (e.g., `['read', 'write']`).

**Example:**
```php
$client->setScopes(['read', 'write']);
```

---

### `getScopes(): array`

Retrieves the configured scopes.

**Returns:**
- *Array*: The scopes.

**Example:**
```php
$scopes = $client->getScopes();
```

---

### `setVersion(string $version): void`

Sets the OAuth version.

**Parameters:**
- `version`: *String*  
  The OAuth version to be used (e.g., `1.0`, `1.0a`, `2.0`).

**Example:**
```php
$client->setVersion('1.0');
```

---

### `getVersion(): string`

Retrieves the configured OAuth version.

**Returns:**
- *String*: The OAuth version.

**Example:**
```php
$version = $client->getVersion();
```

---

### `setRealm(string $realm): void`

Sets the realm for OAuth v1 authentication.

**Parameters:**
- `realm`: *String*  
  The realm to be used for OAuth v1 authentication.

**Example:**
```php
$client->setRealm('my-realm');
```

---

### `getRealm(): string`

Retrieves the configured realm.

**Returns:**
- *String*: The realm.

**Example:**
```php
$realm = $client->getRealm();
```

---

### `setEncodeParams(bool $encodeParams): void`

Sets whether to encode parameters for OAuth v1 requests.

**Parameters:**
- `encodeParams`: *Boolean*  
  Whether to encode parameters (`true`) or not (`false`).

**Example:**
```php
$client->setEncodeParams(true);
```

---

### `getEncodeParams(): bool`

Retrieves the parameter encoding setting.

**Returns:**
- *Boolean*: Whether parameters are encoded.

**Example:**
```php
$encodeParams = $client->getEncodeParams();
```

---

### `setSignatureMethod(SignatureMethod $signatureMethod): void`

Sets the signature method for OAuth v1 authentication.

**Parameters:**
- `signatureMethod`: *SignatureMethod*  
  The signature method (e.g., `SignatureMethod::HMAC_SHA1`, `SignatureMethod::HMAC_SHA256`).

**Example:**
```php
$client->setSignatureMethod(SignatureMethod::HMAC_SHA256);
```

---

### `getSignatureMethod(): SignatureMethod`

Retrieves the configured signature method.

**Returns:**
- *SignatureMethod*: The signature method.

**Example:**
```php
$signatureMethod = $client->getSignatureMethod();
```

---

### `setOAuthV1(?OAuthV1 $oAuth = null): void`

Sets the OAuth v1 instance for authentication.

**Parameters:**
- `oAuth`: *OAuthV1|null*  
  The OAuth v1 instance to use, or `null` to create a new instance with current settings.

**Example:**
```php
$client->setOAuthV1();
```

---

### `getOAuthV1(): OAuthV1`

Retrieves the configured OAuth v1 instance.

**Returns:**
- *OAuthV1*: The OAuth v1 instance.

**Example:**
```php
$oAuthV1 = $client->getOAuthV1();
```

---

### `setDelayHeader(?string $delayHeader): void`

Sets the header name for rate limit delay information.

**Parameters:**
- `delayHeader`: *String|null*  
  The header name containing rate limit delay information (e.g., `Retry-After`).

**Example:**
```php
$client->setDelayHeader('Retry-After');
```

---

### `getDelayHeader(): ?string`

Retrieves the configured delay header name.

**Returns:**
- *String|null*: The delay header name.

**Example:**
```php
$delayHeader = $client->getDelayHeader();
```

---

### `setDelayUnit(DelayUnit $delayUnit): void`

Sets the unit for rate limit delay calculations.

**Parameters:**
- `delayUnit`: *DelayUnit*  
  The delay unit (e.g., `DelayUnit::second`, `DelayUnit::millisecond`, `DelayUnit::microsecond`).

**Example:**
```php
$client->setDelayUnit(DelayUnit::second);
```

---

### `getDelayUnit(): DelayUnit`

Retrieves the configured delay unit.

**Returns:**
- *DelayUnit*: The delay unit.

**Example:**
```php
$delayUnit = $client->getDelayUnit();
```

---