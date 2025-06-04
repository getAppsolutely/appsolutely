<?php

return [
    'prefix'         => env('APPSOLUTELY_APP_PREFIX', 'appsolutely'),
    'url'            => env('APPSOLUTELY_URL', config('app.url')),
    'local_timezone' => env('LOCAL_TIMEZONE', 'UTC'),
    'time_format'    => env('TIME_FORMAT', 'Y-m-d H:i:s T'),
    'currency'       => [
        'symbol' => 'USD',
    ],
    'storage' => [
        'dash_files' => 'uploads/',
        'assets'     => 'assets/',
        'public'     => 'storage/',
    ],
    'features' => [
        'disabled' => env('DISABLED_FEATURES', ''),
    ],
];
