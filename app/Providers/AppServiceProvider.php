<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Article;
use App\Models\Page;
use App\Models\Product;
use App\Observers\ArticleObserver;
use App\Observers\PageObserver;
use App\Observers\ProductObserver;
use App\Repositories\TranslationRepository;
use App\Services\ArticleService;
use App\Services\BlockRendererService;
use App\Services\Contracts\ArticleServiceInterface;
use App\Services\Contracts\BlockRendererServiceInterface;
use App\Services\Contracts\DynamicFormExportServiceInterface;
use App\Services\Contracts\DynamicFormRenderServiceInterface;
use App\Services\Contracts\DynamicFormServiceInterface;
use App\Services\Contracts\DynamicFormSubmissionServiceInterface;
use App\Services\Contracts\DynamicFormValidationServiceInterface;
use App\Services\Contracts\GeneralPageServiceInterface;
use App\Services\Contracts\MenuServiceInterface;
use App\Services\Contracts\NotificationQueueServiceInterface;
use App\Services\Contracts\NotificationRuleServiceInterface;
use App\Services\Contracts\NotificationServiceInterface;
use App\Services\Contracts\NotificationTemplateServiceInterface;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\OrderShipmentServiceInterface;
use App\Services\Contracts\PageBlockSchemaServiceInterface;
use App\Services\Contracts\PageBlockServiceInterface;
use App\Services\Contracts\PageServiceInterface;
use App\Services\Contracts\PaymentServiceInterface;
use App\Services\Contracts\ProductAttributeServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\Contracts\ReleaseServiceInterface;
use App\Services\Contracts\RouteRestrictionServiceInterface;
use App\Services\Contracts\SitemapServiceInterface;
use App\Services\Contracts\StorageServiceInterface;
use App\Services\Contracts\ThemeServiceInterface;
use App\Services\Contracts\TranslationServiceInterface;
use App\Services\Contracts\UserAddressServiceInterface;
use App\Services\DynamicFormExportService;
use App\Services\DynamicFormRenderService;
use App\Services\DynamicFormService;
use App\Services\DynamicFormSubmissionService;
use App\Services\DynamicFormValidationService;
use App\Services\GeneralPageService;
use App\Services\MenuService;
use App\Services\NotificationQueueService;
use App\Services\NotificationRuleService;
use App\Services\NotificationService;
use App\Services\NotificationTemplateService;
use App\Services\OrderService;
use App\Services\OrderShipmentService;
use App\Services\PageBlockSchemaService;
use App\Services\PageBlockService;
use App\Services\PageService;
use App\Services\PaymentService;
use App\Services\ProductAttributeService;
use App\Services\ProductService;
use App\Services\ReleaseService;
use App\Services\RouteRestrictionService;
use App\Services\SitemapService;
use App\Services\StorageService;
use App\Services\ThemeService;
use App\Services\TranslationService;
use App\Services\UserAddressService;
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

        // Bind service interfaces to their implementations
        // Organized alphabetically for maintainability
        $this->app->bind(ArticleServiceInterface::class, ArticleService::class);
        $this->app->bind(BlockRendererServiceInterface::class, BlockRendererService::class);
        $this->app->bind(DynamicFormExportServiceInterface::class, DynamicFormExportService::class);
        $this->app->bind(DynamicFormRenderServiceInterface::class, DynamicFormRenderService::class);
        $this->app->bind(DynamicFormServiceInterface::class, DynamicFormService::class);
        $this->app->bind(DynamicFormSubmissionServiceInterface::class, DynamicFormSubmissionService::class);
        $this->app->bind(DynamicFormValidationServiceInterface::class, DynamicFormValidationService::class);
        $this->app->bind(GeneralPageServiceInterface::class, GeneralPageService::class);
        $this->app->bind(MenuServiceInterface::class, MenuService::class);
        $this->app->bind(NotificationQueueServiceInterface::class, NotificationQueueService::class);
        $this->app->bind(NotificationRuleServiceInterface::class, NotificationRuleService::class);
        $this->app->bind(NotificationServiceInterface::class, NotificationService::class);
        $this->app->bind(NotificationTemplateServiceInterface::class, NotificationTemplateService::class);
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        $this->app->bind(OrderShipmentServiceInterface::class, OrderShipmentService::class);
        $this->app->bind(PageBlockSchemaServiceInterface::class, PageBlockSchemaService::class);
        $this->app->bind(PageBlockServiceInterface::class, PageBlockService::class);
        $this->app->bind(PageServiceInterface::class, PageService::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
        $this->app->bind(ProductAttributeServiceInterface::class, ProductAttributeService::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(ReleaseServiceInterface::class, ReleaseService::class);
        $this->app->bind(RouteRestrictionServiceInterface::class, RouteRestrictionService::class);
        $this->app->bind(SitemapServiceInterface::class, SitemapService::class);
        $this->app->bind(StorageServiceInterface::class, StorageService::class);
        $this->app->bind(ThemeServiceInterface::class, ThemeService::class);
        $this->app->bind(TranslationServiceInterface::class, TranslationService::class);
        $this->app->bind(UserAddressServiceInterface::class, UserAddressService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers
        Page::observe(PageObserver::class);
        Product::observe(ProductObserver::class);
        Article::observe(ArticleObserver::class);

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
            return "<?php echo page_meta($expression, 'meta_title'); ?>";
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
