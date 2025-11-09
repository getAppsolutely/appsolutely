<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \App\Providers\EventServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            foreach (glob(base_path('routes/cache/*.php')) as $routeFile) {
                Route::middleware('web')->group($routeFile);
            }
            // Load fallback route last
            Route::middleware('web')->group(base_path('routes/fallback.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\TrustProxies::class);

        // Add the SetThemeMiddleware to the web middleware group
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\RestrictRoutePrefixes::class,
            \App\Http\Middleware\SetThemeMiddleware::class,
            \Spatie\ResponseCache\Middlewares\CacheResponse::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\RestrictRoutePrefixes::class,
        ]);

        // Apply rate limiting to API routes
        $middleware->throttleApi('api');

        $middleware->group('appsolutely_middleware', [
            \App\Http\Middleware\SetThemeMiddleware::class,
        ]);

        $middleware->alias([
            // Laravel Localization
            'localize'              => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect'  => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect'  => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath'        => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
            // Theme middleware
            'theme'              => \App\Http\Middleware\SetThemeMiddleware::class,
            'doNotCacheResponse' => \Spatie\ResponseCache\Middlewares\DoNotCacheResponse::class,
            // Rate limiting middleware
            'throttle.form' => \App\Http\Middleware\ThrottleFormSubmissions::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
