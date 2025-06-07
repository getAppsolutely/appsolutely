<?php

namespace App\Providers;

use App\Http\Livewire\HeaderLivewire;
use App\Repositories\TranslationRepository;
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

        \Livewire::component('header-livewire', HeaderLivewire::class);
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

        /** @var \Illuminate\Routing\Route $matched */
        $matched = collect(Route::getRoutes())->filter(function (\Illuminate\Routing\Route $route) {
            return $route->uri() === config('admin.route.prefix') . '/files';
        })->first();
        $matched?->uses('App\Admin\Controllers\Api\FileController@upload')->name('api.files.upload');

    }
}
