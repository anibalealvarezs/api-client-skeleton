<?php

namespace Anibalealvarezs\ApiSkeleton\Traits;

trait HasUpdatableCredentials
{
    /**
     * Get the list of environment variables that are updatable for this driver.
     *
     * @return array
     */
    public function getUpdatableCredentials(): array
    {
        return property_exists($this, 'updatableCredentials') ? $this->updatableCredentials : [];
    }
}
