<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\ArticleCreated;
use App\Events\ArticleDeleted;
use App\Events\ArticleUpdated;
use App\Events\FormSubmitted;
use App\Events\PageCreated;
use App\Events\PageDeleted;
use App\Events\PageUpdated;
use App\Events\ProductCreated;
use App\Events\ProductDeleted;
use App\Events\ProductUpdated;
use App\Listeners\ClearSitemapCache;
use App\Listeners\TriggerFormNotifications;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Event Service Provider
 *
 * Registers event listeners for the application.
 * This enables event-driven architecture for decoupled side effects.
 */
final class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Page events
        PageCreated::class => [
            ClearSitemapCache::class,
        ],
        PageUpdated::class => [
            ClearSitemapCache::class,
        ],
        PageDeleted::class => [
            ClearSitemapCache::class,
        ],

        // Product events
        ProductCreated::class => [
            ClearSitemapCache::class,
        ],
        ProductUpdated::class => [
            ClearSitemapCache::class,
        ],
        ProductDeleted::class => [
            ClearSitemapCache::class,
        ],

        // Article events
        ArticleCreated::class => [
            ClearSitemapCache::class,
        ],
        ArticleUpdated::class => [
            ClearSitemapCache::class,
        ],
        ArticleDeleted::class => [
            ClearSitemapCache::class,
        ],

        // Form events
        FormSubmitted::class => [
            TriggerFormNotifications::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
