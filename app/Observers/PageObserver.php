<?php

declare(strict_types=1);

namespace App\Observers;

use App\Events\PageCreated;
use App\Events\PageDeleted;
use App\Events\PageUpdated;
use App\Models\Page;

/**
 * Observer for Page model events
 *
 * Dispatches domain events when pages are created, updated, or deleted.
 * Listeners handle side effects like cache clearing.
 */
final class PageObserver
{
    /**
     * Handle the Page "created" event.
     */
    public function created(Page $page): void
    {
        event(new PageCreated($page));
    }

    /**
     * Handle the Page "updated" event.
     */
    public function updated(Page $page): void
    {
        event(new PageUpdated($page));
    }

    /**
     * Handle the Page "deleted" event.
     */
    public function deleted(Page $page): void
    {
        event(new PageDeleted($page));
    }
}
