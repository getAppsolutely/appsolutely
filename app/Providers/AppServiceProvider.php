<?php

namespace App\Providers;

use App\Repositories\TranslationRepository;
use App\Services\PageBlockService;
use App\Services\TranslationService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Translation Service and Repository
        $this->app->singleton(TranslationRepository::class);
        $this->app->singleton(TranslationService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Page Builder view namespace
        View::addNamespace('page-builder', resource_path('page-builder'));

        // Register Blade directive for translations
        Blade::directive('t', function ($expression) {
            return "<?php echo __t($expression); ?>";
        });

        // Register directive for variable translations
        Blade::directive('tv', function ($expression) {
            return "<?php echo __tv($expression); ?>";
        });

        // Register Blade directive for safe block rendering
        Blade::directive('renderBlock', function ($expression) {
            return "<?php echo app('" . PageBlockService::class . "')->renderBlockSafely($expression); ?>";
        });

        // Page meta
        Blade::directive('title', function ($expression) {
            return "<?php echo page_meta($expression, 'title'); ?>";
        });

        Blade::directive('keywords', function ($expression) {
            return "<?php echo page_meta($expression, 'keywords'); ?>";
        });

        Blade::directive('description', function ($expression) {
            return "<?php echo page_meta($expression, 'description'); ?>";
        });

        /** @var \Illuminate\Routing\Route $matched */
        $matched = collect(Route::getRoutes())->filter(function (\Illuminate\Routing\Route $route) {
            return $route->uri() === config('admin.route.prefix') . '/files';
        })->first();
        $matched?->uses('App\Admin\Controllers\Api\FileController@upload')->name('api.files.upload');

        Route::macro('localized', function (\Closure $callback) {
            if (config('app.localization', false)) {
                // Apply localization when enabled
                Route::prefix(\LaravelLocalization::setLocale())
                    ->middleware(['localeCookieRedirect', 'localizationRedirect', 'localeViewPath'])
                    ->group($callback);
            } else {
                // No localization when disabled
                Route::group([], $callback);
            }
        });
    }
}
