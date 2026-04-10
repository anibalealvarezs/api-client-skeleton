<?php

namespace Anibalealvarezs\ApiSkeleton\Interfaces;

interface DimensionManagerInterface
{
    /**
     * Resolve a dimension set from an array of dimensions.
     *
     * @param array $dimensions
     * @return object Returns the DimensionSet entity
     */
    public function resolveDimensionSet(array $dimensions): object;

    /**
     * Clear local caches (useful for bulk seeding).
     *
     * @return void
     */
    public function clearCaches(): void;
}
