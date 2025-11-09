<?php

return [
    'prefix'           => env('APPSOLUTELY_APP_PREFIX', 'appsolutely'),
    'url'              => env('APPSOLUTELY_URL', config('app.url')),
    'local_timezone'   => env('APPSOLUTELY_LOCAL_TIMEZONE', 'UTC'),
    'time_format'      => env('APPSOLUTELY_TIME_FORMAT', 'Y-m-d H:i:s T'),
    'multiple_locales' => env('APPSOLUTELY_MULTIPLE_LOCALES', false),
    'asset_url'        => env('APPSOLUTELY_ASSET_URL', config('app.asset_url') ?? config('app.url')),
    'theme'            => [
        'name'   => env('APPSOLUTELY_THEME', 'default'),
        'styles' => [
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css',
        ],
        'scripts' => [],
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
        'enabled'  => env('APPSOLUTELY_ENABLED_FEATURES', ''), // Will check enabled first
        'disabled' => env('APPSOLUTELY_DISABLED_FEATURES', ''),
    ],
    'blocks' => [
        App\Livewire\ArticleList::class => [
            App\Repositories\ArticleRepository::class,
            App\Repositories\ProductRepository::class,
        ],
    ],
    'seo' => [
        'title_separator' => ' | ',
    ],
    'security' => [
        // Content Security Policy
        // Set to null to use default, or provide custom CSP string
        // Example: "default-src 'self'; script-src 'self' 'unsafe-inline';"
        'csp' => env('APPSOLUTELY_SECURITY_CSP', null),
    ],
];
