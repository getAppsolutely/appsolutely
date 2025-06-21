<?php

return [
    'prefix'         => env('APPSOLUTELY_APP_PREFIX', 'appsolutely'),
    'url'            => env('APPSOLUTELY_URL', config('app.url')),
    'asset'          => env('APPSOLUTELY_ASSET_SERVER', config('app.url')),
    'local_timezone' => env('LOCAL_TIMEZONE', 'UTC'),
    'time_format'    => env('TIME_FORMAT', 'Y-m-d H:i:s T'),
    'theme'          => [
        'name'   => env('APPSOLUTELY_THEME', 'default'),
        'styles' => [
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css',
        ],
        'scripts' => [],
    ],
    'general' => [
        'logo'             => env('APPSOLUTELY_LOGO', ''),
        'site_name'        => env('APPSOLUTELY_SITE_NAME', config('app.name')),
        'site_description' => env('APPSOLUTELY_SITE_DESCRIPTION', ''),
    ],
    'currency' => [
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
