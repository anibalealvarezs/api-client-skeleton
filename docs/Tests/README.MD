# API Skeleton - Testing Instructions

This document provides instructions for running the unit tests for the `anibalealvarezs/api-client-skeleton` package, specifically for the `ClientTest.php` test class. The tests verify the functionality of the `Client` class, including its constructor, authentication methods, and request handling capabilities.

## Prerequisites

Before running the tests, ensure the following requirements are met:

- **PHP**: Version 8.1 or higher is required, as specified in the package's `composer.json`.
- **Composer**: Installed globally or locally to manage dependencies.
- **PHPUnit**: Installed via Composer as a development dependency (typically included in the package's `require-dev` section).
- **Dependencies**: The package and its dependencies must be installed via Composer.
- **Mock Server**: The tests use mocked HTTP responses via Guzzle's `MockHandler`. No external server is required, as the tests are designed to run with a mock-friendly URL (`http://localhost`).

## Installation

To set up the package and its dependencies, include the package in your project's `composer.json` file and map the repository:

```json
{
    "require": {
        "php": ">=8.1",
        "anibalealvarezs/api-client-skeleton": "@dev"
    },
    "repositories": [
        {
          "type": "composer",
          "url": "https://satis.anibalalvarez.com/"
        }
    ],
    "require-dev": {
        "phpunit/phpunit": "^9.5"
    }
}
```

Run the following command to install the package and its dependencies:

```bash
composer install
```

This will install the `anibalealvarezs/api-client-skeleton` package, PHPUnit, and other required dependencies (e.g., Guzzle, Faker).

## Test Setup

The `ClientTest.php` class, located in the `tests/` directory, is designed to test the `Client` class comprehensively. The test class uses the following key components:

- **Faker**: Generates random test data (e.g., UUIDs for API keys, tokens, etc.).
- **Guzzle MockHandler**: Simulates HTTP responses to avoid real network requests, ensuring tests are fast and reliable.
- **PHPUnit**: Provides the testing framework for assertions and test execution.

The `setUp` method in `ClientTest.php` initializes the test environment by:
- Creating a Faker instance for generating test data.
- Setting up mock-friendly URLs (`http://localhost` and `http://localhost/token`).
- Generating random values for authentication parameters (e.g., `apiKey`, `clientId`, `token`).

No additional configuration is required, as the tests are self-contained and do not rely on external services.

## Running the Tests

To run the tests for the `ClientTest.php` class, use the following command from the root directory of your project:

```bash
./vendor/bin/phpunit --verbose tests/ClientTest.php
```

### Command Breakdown
- `./vendor/bin/phpunit`: Executes the PHPUnit binary installed via Composer.
- `--verbose`: Enables verbose output, displaying detailed information about each test case, including test names and results.
- `tests/ClientTest.php`: Specifies the test file to run, limiting execution to the `ClientTest` class.

### Expected Output
When running the command, you will see output similar to the following (assuming all tests pass):

```
PHPUnit 9.5.28 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.1.0
Configuration: /path/to/project/phpunit.xml

...............................................................  63 / 63 (100%)

Time: 00:00.123, Memory: 10.00 MB

OK (63 tests, 123 assertions)
```

- The dots (`.`) represent successful test cases.
- The summary indicates the total number of tests, execution time, memory usage, and any failures or errors.
- With `--verbose`, additional details about each test method (e.g., `testConstructorWithApiKeyAuth`, `testPerformRequestWithApiKeyAuth`) will be displayed.

### Troubleshooting
If tests fail, check the following:
- **Dependencies**: Ensure all dependencies are installed (`composer install`) and match the required versions.
- **PHP Version**: Verify that PHP 8.1 or higher is used (`php -v`).
- **Composer Autoloader**: Run `composer dump-autoload` to regenerate the autoloader if classes are not found.
- **Test Data**: The tests use Faker for random data, but the mock responses are fixed. Ensure the `MockHandler` responses align with the test expectations.
- **Verbose Output**: The `--verbose` flag provides detailed error messages, including stack traces for failed assertions or exceptions.

## Test Coverage

The `ClientTest.php` class includes 63 test methods that cover:
- **Constructor Validation**: Tests for valid and invalid inputs (e.g., `testConstructorWithApiKeyAuth`, `testConstructorThrowsExceptionForInvalidBaseUrl`).
- **Setter and Getter Methods**: Verification of configuration methods (e.g., `testSetAndGetBaseUrl`, `testSetAndGetClientId`).
- **Authentication**: Tests for various authentication types (e.g., API Key, OAuth v1, OAuth v2, Bearer Token, Basic) and their impact on requests (e.g., `testSetAuthForApiKeyInHeader`, `testSetAuthForOAuthV1InHeader`).
- **Request Handling**: Tests for the `performRequest` method, including successful responses, error handling (e.g., 401, 403), and token refresh (e.g., `testPerformRequestHandles401WithNewTokenWithInitialInvalidToken`).
- **Debug Mode**: Verification of debug data collection (e.g., `testDebugModeAndDataAffectsPerformRequest`).

To generate a test coverage report, add the `--coverage-text` or `--coverage-html` option:

```bash
./vendor/bin/phpunit --verbose --coverage-text tests/ClientTest.php
```

This will display a coverage report in the terminal or generate an HTML report if `--coverage-html` is used (requires PHPUnit to be configured with coverage reporting).

## Additional Notes
- **Mocking**: The tests rely on Guzzle's `MockHandler` to simulate HTTP responses. Ensure the `GuzzleHttp\Handler\MockHandler` class is available in your dependencies.
- **Isolation**: Each test method is isolated, with the `setUp` method resetting the test environment to prevent state leakage.
- **Extending Tests**: To add new test cases, extend the `ClientTest` class in `tests/ClientTest.php`. Follow the existing pattern of using `MockHandler` for HTTP responses and Faker for test data.
- **PHPUnit Configuration**: If a `phpunit.xml` file exists in the project root, it will be used automatically. Ensure it includes the `tests/` directory and any necessary bootstrap settings (e.g., `vendor/autoload.php`).