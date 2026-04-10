<?php

namespace Anibalealvarezs\ApiSkeleton\Interfaces;

use Symfony\Component\HttpFoundation\Response;
use DateTime;

/**
 * Interface SyncDriverInterface
 * Defines the contract for a Data Channel Driver
 */
interface SyncDriverInterface
{
    /**
     * Authenticate the driver with a specific provider.
     */
    public function setAuthProvider(AuthProviderInterface $provider): void;

    /**
     * Perform the synchronization loop for a date range.
     */
    public function sync(DateTime $startDate, DateTime $endDate, array $config = []): Response;

    /**
     * Get the channel identifier (e.g. google_search_console).
     */
    public function getChannel(): string;

    /**
     * Get the raw API client instance for the driver.
     *
     * @param array $config
     * @return mixed
     */
    public function getApi(array $config = []): mixed;

    /**
     * Get the current AuthProvider instance.
     *
     * @return AuthProviderInterface|null
     */
    public function getAuthProvider(): ?AuthProviderInterface;

    /**
     * Get the list of environment variables that are updatable for this driver.

     *
     * @return array
     */
    public function getUpdatableCredentials(): array;
    /**
     * Get the configuration schema for the driver.
     *
     * @return array
     */
    public function getConfigSchema(): array;
    /**
     * Validate and prepare the configuration for the driver.
     * Use this to apply defaults and normalize structures.
     *
     * @param array $config
     * @return array
     */
    public function validateConfig(array $config): array;

    /**
     * Seed realistic demo data for this driver.
     *
     * @param SeederInterface $seeder The seeder utility (command or service)
     * @param array $config
     * @return void
     */
    public function seedDemoData(SeederInterface $seeder, array $config = []): void;

    /**
     * Initialize driver-specific configurations in the host (e.g. Repository relations).
     */
    public function boot(): void;

    /**
     * Get the asset identification patterns for this driver.
     * Used to generate canonical IDs from URLs or hostnames.
     *
     * @return array
     */
    public function getAssetPatterns(): array;
}


