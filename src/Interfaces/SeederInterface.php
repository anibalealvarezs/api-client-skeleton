<?php

namespace Anibalealvarezs\ApiSkeleton\Interfaces;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;

interface SeederInterface
{
    /**
     * Process a collection of metrics massively.
     *
     * @param Collection $metrics
     * @return void
     */
    public function processMetricsMassive(Collection $metrics): void;
    /**
     * Get the entity manager.
     *
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface;

    /**
     * Get the dimension manager.
     *
     * @return DimensionManagerInterface
     */
    public function getDimensionManager(): DimensionManagerInterface;

    /**
     * Get the absolute class name for an entity.
     *
     * @param string $shortName
     * @return string
     */
    public function getEntityClass(string $shortName): string;

    /**
     * Get the absolute class name for an enum.
     *
     * @param string $shortName
     * @return string
     */
    public function getEnumClass(string $shortName): string;

    /**
     * Get a list of dates for seeding.
     *
     * @param int $days
     * @return array
     */
    public function getDates(int $days = 180): array;

    /**
     * Queue a metric for bulk insertion.
     *
     * @param mixed $channel
     * @param string $name
     * @param string $date
     * @param float|int $value
     * @param mixed ...$params Additional parameters (setId, pageId, etc.)
     * @return void
     */
    public function queueMetric(
        mixed $channel,
        string $name,
        string $date,
        mixed $value,
        $setId = null,
        $pageId = null,
        $adId = null,
        $agId = null,
        $cpId = null,
        $caId = null,
        $gAccId = null,
        $gCpId = null,
        $postId = null,
        $queryId = null,
        $countryId = null,
        $deviceId = null,
        $productId = null,
        $customerId = null,
        $orderId = null,
        $creativeId = null,
        ?string $accName = null,
        ?string $caPId = null,
        ?string $gCpPId = null,
        ?string $cpPId = null,
        ?string $agPId = null,
        ?string $adPId = null,
        ?string $pageUrl = null,
        ?string $postPId = null,
        ?string $queryPId = null,
        ?string $countryPId = null,
        ?string $devicePId = null,
        ?string $productPId = null,
        ?string $customerPId = null,
        ?string $orderPId = null,
        ?string $creativePId = null,
        ?string $data = null,
        ?string $setHash = null,
        ...$extraParams
    ): void;
}
