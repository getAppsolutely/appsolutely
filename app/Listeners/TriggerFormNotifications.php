<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\FormSubmitted;
use App\Services\Contracts\NotificationServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Listener to trigger notifications when a form is submitted
 *
 * Handles notification triggering for form submissions by delegating
 * to the NotificationService.
 */
final class TriggerFormNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private readonly NotificationServiceInterface $notificationService
    ) {}

    /**
     * Handle the form submitted event
     */
    public function handle(FormSubmitted $event): void
    {
        // Trigger notifications using the form slug as the trigger type
        $this->notificationService->trigger(
            'form_submitted',
            $event->form->slug,
            [
                'form'  => $event->form->toArray(),
                'entry' => $event->entry->toArray(),
                'data'  => $event->data,
            ]
        );
    }
}
