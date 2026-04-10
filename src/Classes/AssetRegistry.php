<?php

namespace Anibalealvarezs\ApiSkeleton\Classes;

class AssetRegistry
{
    private static array $patterns = [];

    public static function register(string $type, array $config): void
    {
        self::$patterns[$type] = $config;
    }

    public static function getAll(): array
    {
        return self::$patterns;
    }

    public static function findByHostname(string $hostname): ?array
    {
        foreach (self::$patterns as $type => $config) {
            foreach ($config['hostnames'] ?? [] as $pattern) {
                if (str_contains($hostname, $pattern)) {
                    return array_merge(['type' => $type], $config);
                }
            }
        }
        return null;
    }

    public static function findByType(string $type): ?array
    {
        return self::$patterns[$type] ?? null;
    }
}
