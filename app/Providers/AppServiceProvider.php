<?php

namespace App\Providers;

use App\Repositories\TranslationRepository;
use App\Services\TranslationService;
use Illuminate\Support\Facades\Blade;
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
        // Register Blade directive for translations
        Blade::directive('t', function ($expression) {
            return "<?php echo __t($expression); ?>";
        });

        // Register directive for variable translations
        Blade::directive('tv', function ($expression) {
            return "<?php echo __tv($expression); ?>";
        });
    }
}
