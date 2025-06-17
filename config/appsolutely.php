<?php

return [
    'prefix'         => env('APPSOLUTELY_APP_PREFIX', 'appsolutely'),
    'url'            => env('APPSOLUTELY_URL', config('app.url')),
    'theme'          => env('APPSOLUTELY_THEME', 'default'),
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
    'theme_assets' => [
        'styles' => [
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css',
        ],
    ],
];
