<?php

declare(strict_types=1);

namespace Anibalealvarezs\ApiSkeleton\Classes;

use DateTime;
use Anibalealvarezs\ApiSkeleton\Enums\Channel;
use Anibalealvarezs\ApiSkeleton\Enums\Period;
use Anibalealvarezs\ApiSkeleton\Enums\Country as CountryEnum;
use Anibalealvarezs\ApiSkeleton\Enums\Device as DeviceEnum;
use InvalidArgumentException;

/**
 * KeyGenerator
 * 
 * Standardized key generation for metrics and entities.
 * Refactored to be entity-agnostic for the base skeleton.
 */
class KeyGenerator
{
    public static function generateQueryKey(object|string $query): string
    {
        if (is_object($query) && method_exists($query, 'getQuery')) {
            return md5($query->getQuery());
        }
        return md5((string) $query);
    }

    /**
     * @param Channel|int|string $channel
     * @param string $name
     * @param Period|string $period
     * @param object|string|null $account
     * @param object|string|null $channeledAccount
     * @param object|string|null $campaign
     * @param object|string|null $channeledCampaign
     * @param object|string|null $channeledAdGroup
     * @param object|string|null $channeledAd
     * @param string|null $creative
     * @param object|string|null $page
     * @param object|string|null $query
     * @param object|string|null $post
     * @param object|string|null $product
     * @param object|string|null $customer
     * @param object|string|null $order
     * @param object|CountryEnum|string|null $country
     * @param object|DeviceEnum|string|null $device
     * @param object|int|string|null $dimensionSet
     * @return string
     */
    public static function generateMetricConfigKey(
        mixed $channel,
        string $name,
        mixed $period,
        object|string|null $account = null,
        object|string|null $channeledAccount = null,
        object|string|null $campaign = null,
        object|string|null $channeledCampaign = null,
        object|string|null $channeledAdGroup = null,
        object|string|null $channeledAd = null,
        ?string $creative = null,
        object|string|null $page = null,
        object|string|null $query = null,
        object|string|null $post = null,
        object|string|null $product = null,
        object|string|null $customer = null,
        object|string|null $order = null,
        mixed $country = null,
        mixed $device = null,
        object|int|string|null $dimensionSet = null
    ): string {
        $emptyHash = self::generateDimensionsHash([]);
        if ($dimensionSet === $emptyHash) { $dimensionSet = null; }

        $channelVal = $channel instanceof \BackedEnum ? $channel->value : $channel;
        $channelEnum = is_numeric($channelVal) 
            ? Channel::from((int) $channelVal) 
            : Channel::tryFromName((string) $channelVal);

        if (!$channelEnum) {
            throw new \InvalidArgumentException("Invalid channel identifier: " . (is_array($channelVal) ? json_encode($channelVal) : (string)$channelVal));
        }

        $params = [
            'channel' => $channelEnum->getName(),
            'name' => $name,
            'period' => $period instanceof \BackedEnum ? $period->value : $period,
            'account' => self::extractString($account, 'getName'),
            'channeledAccount' => (string) self::extractString($channeledAccount, 'getPlatformId'),
            'campaign' => (string) self::extractString($campaign, 'getCampaignId'),
            'channeledCampaign' => (string) self::extractString($channeledCampaign, 'getPlatformId'),
            'channeledAdGroup' => (string) self::extractString($channeledAdGroup, 'getPlatformId'),
            'channeledAd' => (string) self::extractString($channeledAd, 'getPlatformId'),
            'creative' => $creative,
            'page' => self::extractString($page, 'getUrl'),
            'query' => self::extractString($query, 'getQuery'),
            'post' => (string) self::extractString($post, 'getPostId'),
            'product' => (string) self::extractString($product, 'getProductId'),
            'customer' => self::extractString($customer, 'getEmail'),
            'order' => (string) self::extractString($order, 'getOrderId'),
            'country' => ($country instanceof \BackedEnum) ? $country->value : self::extractString($country, 'getCode'),
            'device' => ($device instanceof \BackedEnum) ? $device->value : self::extractString($device, 'getType'),
            'dimensionSet' => self::extractString($dimensionSet, 'getHash')
        ];

        return md5(json_encode($params, JSON_UNESCAPED_UNICODE));
    }

    private static function extractString(mixed $val, string $method): ?string
    {
        if (is_null($val)) return null;
        if (is_object($val) && method_exists($val, $method)) {
            return (string) $val->$method();
        }
        return (string) $val;
    }

    public static function generateMetricKey(
        mixed $channel = null,
        ?string $name = null,
        mixed $period = null,
        DateTime|string|null $metricDate = null,
        object|int|null $account = null,
        object|int|null $channeledAccount = null,
        object|int|null $campaign = null,
        object|int|null $channeledCampaign = null,
        object|int|null $channeledAdGroup = null,
        object|int|null $channeledAd = null,
        ?string $creative = null,
        object|string|null $page = null,
        object|string|null $query = null,
        object|string|null $post = null,
        object|int|null $product = null,
        object|int|null $customer = null,
        object|int|null $order = null,
        mixed $country = null,
        mixed $device = null,
        array $dimensions = [],
        ?string $dimensionsHash = null,
        ?string $metricConfigKey = null,
    ): string {
        if (is_null($metricConfigKey)) {
            if (is_null($channel) || is_null($name) || is_null($period) || is_null($metricDate)) {
                throw new InvalidArgumentException('Channel, name, period and metricDate are required to generate a metric key.');
            }
            $metricConfigKey = self::generateMetricConfigKey(
                channel: $channel,
                name: $name,
                period: $period,
                account: $account,
                channeledAccount: $channeledAccount,
                campaign: $campaign,
                channeledCampaign: $channeledCampaign,
                channeledAdGroup: $channeledAdGroup,
                channeledAd: $channeledAd,
                creative: $creative,
                page: $page,
                query: $query,
                post: $post,
                product: $product,
                customer: $customer,
                order: $order,
                country: $country,
                device: $device,
                dimensionSet: $dimensionsHash
            );
        }
        if (is_null($dimensionsHash)) {
            self::sortDimensions($dimensions);
            $dimensionsHash = self::generateDimensionsHash($dimensions);
        }
        return md5(json_encode([
            'metricConfig' => $metricConfigKey,
            'dimensionsHash' => $dimensionsHash,
            'metricDate' => $metricDate instanceof DateTime ? $metricDate->format('Y-m-d') : $metricDate,
        ], JSON_UNESCAPED_UNICODE));
    }

    public static function sortDimensions(array &$dimensions): void
    {
        usort($dimensions, function ($a, $b) {
            return strcmp($a['dimensionKey'], $b['dimensionKey']);
        });
    }

    public static function generateDimensionsHash(array $dimensions): string
    {
        return md5(json_encode($dimensions, JSON_UNESCAPED_UNICODE));
    }

    public static function generateChanneledMetricKey(
        Channel|int|string $channel,
        string $platformId,
        object|int $metric,
        DateTime|string $platformCreatedAt
    ): string {
        return md5(json_encode([
            'channel' => $channel instanceof Channel ? $channel->getName() : (is_numeric($channel) ? Channel::from((int)$channel)->getName() : $channel),
            'platformId' => $platformId,
            'metric_id' => is_object($metric) ? $metric->getId() : $metric,
            'platformCreatedAt' => $platformCreatedAt instanceof DateTime ? $platformCreatedAt->format('Y-m-d') : $platformCreatedAt
        ], JSON_UNESCAPED_UNICODE));
    }

    public static function generateCustomerKey(string $email): string
    {
        return md5(strtolower(trim($email)));
    }

    public static function generateChanneledCustomerKey(string $channel, string $platformId): string
    {
        return md5($channel . '_' . $platformId);
    }

    public static function generateProductKey(string $productId): string
    {
        return md5((string)$productId);
    }

    public static function generateChanneledProductKey(string $channel, string $platformId): string
    {
        return md5($channel . '_' . $platformId);
    }

    public static function generateVendorKey(string $name): string
    {
        return md5(strtolower(trim($name)));
    }

    public static function generateChanneledVendorKey(string $channel, string $name): string
    {
        return md5($channel . '_' . strtolower(trim($name)));
    }

    public static function generateProductVariantKey(string $productVariantId): string
    {
        return md5((string)$productVariantId);
    }

    public static function generateChanneledProductVariantKey(string $channel, string $platformId): string
    {
        return md5($channel . '_' . $platformId);
    }

    public static function generateProductCategoryKey(string $productCategoryId): string
    {
        return md5((string)$productCategoryId);
    }

    public static function generateChanneledProductCategoryKey(string $channel, string $platformId): string
    {
        return md5($channel . '_' . $platformId);
    }

    public static function generateOrderKey(string $orderId): string
    {
        return md5((string)$orderId);
    }

    public static function generateChanneledOrderKey(string $channel, string $platformId): string
    {
        return md5($channel . '_' . $platformId);
    }

    public static function generateDiscountKey(string $code): string
    {
        return md5((string)$code);
    }

    public static function generateChanneledDiscountKey(string $channel, string $code): string
    {
        return md5($channel . '_' . $code);
    }

    public static function generatePriceRuleKey(string $priceRuleId): string
    {
        return md5((string)$priceRuleId);
    }

    public static function generateChanneledPriceRuleKey(string $channel, string $platformId): string
    {
        return md5($channel . '_' . $platformId);
    }
}
