<?php

declare(strict_types=1);

namespace Anibalealvarezs\ApiSkeleton\Classes;

class RepositoryRegistry
{
    private static array $relationMap = [];
    private static array $formulas = [];

    /**
     * Register a custom relation mapping.
     */
    public static function registerRelation(string $key, array $mapping): void
    {
        self::$relationMap[$key] = $mapping;
    }

    /**
     * Register multiple relation mappings.
     */
    public static function registerRelations(array $mappings): void
    {
        self::$relationMap = array_merge(self::$relationMap, $mappings);
    }

    /**
     * Get all registered relations.
     */
    public static function getRelations(): array
    {
        return self::$relationMap;
    }

    /**
     * Register a custom formula.
     */
    public static function registerFormula(string $key, $formula): void
    {
        self::$formulas[$key] = $formula;
    }

    /**
     * Register multiple formulas.
     */
    public static function registerFormulas(array $formulas): void
    {
        self::$formulas = array_merge(self::$formulas, $formulas);
    }

    /**
     * Get all registered formulas.
     */
    public static function getFormulas(): array
    {
        return self::$formulas;
    }
}
