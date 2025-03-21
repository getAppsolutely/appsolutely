<?php

declare(strict_types=1);

use App\Settings\Settings;
use Illuminate\Support\Facades\Log;

if (!function_exists('appsolutely')) {
    /**
     * Get the Appsolutely prefix for database or cache keys.
     *
     * @param string|null $prefix The suffix to append to the base prefix
     * @return string The formatted prefix
     */
    function appsolutely(?string $prefix = null): string
    {
        $result = config('appsolutely.prefix');

        if ($prefix !== null) {
            $result = "app_{$prefix}";
            config('appsolutely.prefix', $result);
        }

        Log::info('Application for ', [
            'prefix_param' => $prefix,
            'result' => $result
        ]);

        return $result;
    }
}

