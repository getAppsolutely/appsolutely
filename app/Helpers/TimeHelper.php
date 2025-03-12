<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimeHelper
{
    /**
     * Format timestamp to a readable format in 24-hour time
     *
     * @param string|null $timestamp
     * @param string $format Default format is 'Y-m-d H:i:s' (24-hour)
     * @return string
     */
    public static function format($timestamp, $format = 'Y-m-d H:i:s')
    {
        if (empty($timestamp)) {
            return '';
        }

        // Parse the timestamp and set to application timezone
        return Carbon::parse($timestamp)
            ->setTimezone(config('appsolutely.timezone'))
            ->format($format);
    }

    /**
     * Format timestamp with timezone
     *
     * @param string|null $timestamp
     * @return string
     */
    public static function formatWithTz($timestamp)
    {
        return self::format($timestamp, 'Y-m-d H:i:s T'); // Example: 2025-03-12 15:30:45 NZDT
    }
}
