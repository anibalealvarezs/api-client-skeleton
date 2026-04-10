<?php
declare(strict_types=1);

namespace Anibalealvarezs\ApiSkeleton\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Splits a date range into smaller chunks.
     *
     * @param string $startDate Start date (Y-m-d)
     * @param string $endDate End date (Y-m-d)
     * @param string $interval Interval to add to each chunk (e.g. '1 week')
     * @return array Array of chunks with 'start' and 'end' keys
     */
    public static function getDateChunks(string $startDate, string $endDate, string $interval = '1 week'): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($start->isAfter($end)) {
            return [];
        }

        $chunks = [];
        $currentStart = $start->copy();

        while ($currentStart->isBefore($end) || $currentStart->isSameDay($end)) {
            $currentEnd = $currentStart->copy()->add($interval)->subDay();
            if ($currentEnd->isAfter($end)) {
                $currentEnd = $end->copy();
            }

            $chunks[] = [
                'start' => $currentStart->format('Y-m-d'),
                'end' => $currentEnd->format('Y-m-d')
            ];

            $currentStart = $currentEnd->copy()->addDay();
        }

        return $chunks;
    }
}
