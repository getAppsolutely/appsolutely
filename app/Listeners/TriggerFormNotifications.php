<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\FormSubmitted;
use App\Services\Contracts\NotificationServiceInterface;

/**
 * Listener to trigger notifications when a form is submitted
 *
 * Handles notification triggering for form submissions by delegating
 * to the NotificationService.
 *
 * Note: This listener is synchronous for immediate execution.
 * If you need queued execution, implement ShouldQueue interface.
 */
final class TriggerFormNotifications
{
    public function __construct(
        private readonly NotificationServiceInterface $notificationService
    ) {}

    /**
     * Handle the form submitted event
     */
    public function handle(FormSubmitted $event): void
    {
        // Use atomic lock to prevent duplicate processing across instances
        $lockKey = 'form_notification_processing:' . $event->entry->id . '_' . $event->form->id;

        $lock = \Cache::lock($lockKey, 10);

        if (! $lock->get()) {
            \Log::warning('Form notification: duplicate event skipped', [
                'entry_id'  => $event->entry->id,
                'form_slug' => $event->form->slug,
            ]);

            return;
        }

        try {

            // Prepare notification data matching the format expected by NotificationService
            $notificationData = [
                'form_name'        => $event->form->name,
                'form_description' => $event->form->description,
                'user_name'        => trim(($event->entry->first_name ?? '') . ' ' . ($event->entry->last_name ?? '')),
                'user_email'       => $event->entry->email,
                'user_phone'       => $event->entry->mobile,
                'submitted_at'     => $event->entry->created_at->format('Y-m-d H:i:s'),
                'entry_id'         => $event->entry->id,
                'form_data'        => json_encode($event->data),
                'admin_link'       => url('/admin/dynamic-forms?tab=form-entries&form_id=' . $event->form->id),
            ];

            // Trigger notifications using 'form_submission' (matching the trigger type used in rules)
            // Use form slug as reference, or '*' for wildcard rules
            $this->notificationService->trigger(
                'form_submission',
                $event->form->slug,
                $notificationData
            );
        } finally {
            // Release lock after processing (or on exception)
            $lock->release();
        }
    }
}
