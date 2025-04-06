<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimeHelper
{
    /**
     * Format timestamp to a readable format in 24-hour time
     */
    public static function format(?string $timestamp, string $format = 'Y-m-d H:i:s'): string
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
     */
    public static function formatWithTz(?string $timestamp): string
    {
        return self::format($timestamp, 'Y-m-d H:i:s T'); // Example: 2025-03-12 15:30:45 NZDT
    }
}
