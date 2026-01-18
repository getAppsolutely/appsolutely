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
                'user_name'        => trim(($event->entry->first_name ?? '') . ' ' . ($event->entry->last_name ?? '')),
                'user_email'       => $event->entry->email,
                'user_phone'       => $event->entry->mobile,
                'form_fields_html' => $this->formatFieldsAsHtml($event->data),
                'form_fields_text' => $this->formatFieldsAsText($event->data),
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

    /**
     * Format form fields as HTML table rows
     */
    private function formatFieldsAsHtml(array $data): string
    {
        // Skip common fields that are already displayed
        $skipFields = ['name', 'first_name', 'last_name', 'email', 'mobile', 'phone'];

        $html = '';
        foreach ($data as $key => $value) {
            // Skip if it's already shown in contact info
            if (in_array(strtolower($key), $skipFields)) {
                continue;
            }

            // Format the field name (convert snake_case to Title Case)
            $label = ucwords(str_replace(['_', '-'], ' ', $key));

            // Format the value
            $formattedValue = $this->formatValue($value);

            // Add table row
            $html .= '<tr>';
            $html .= '<td style="padding: 8px 0; font-weight: bold; width: 150px;">' . htmlspecialchars($label) . ':</td>';
            $html .= '<td style="padding: 8px 0;">' . $formattedValue . '</td>';
            $html .= '</tr>' . "\n";
        }

        return $html;
    }

    /**
     * Format form fields as plain text
     */
    private function formatFieldsAsText(array $data): string
    {
        // Skip common fields that are already displayed
        $skipFields = ['name', 'first_name', 'last_name', 'email', 'mobile', 'phone'];

        $text = '';
        foreach ($data as $key => $value) {
            // Skip if it's already shown in contact info
            if (in_array(strtolower($key), $skipFields)) {
                continue;
            }

            // Format the field name
            $label = ucwords(str_replace(['_', '-'], ' ', $key));

            // Format the value
            $formattedValue = $this->formatValueText($value);

            // Add line
            $text .= $label . ': ' . $formattedValue . "\n";
        }

        return $text ? "\n" . $text : '';
    }

    /**
     * Format a value for HTML display
     */
    private function formatValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '<span style="color: #27ae60;">✓ Yes</span>' : '<span style="color: #e74c3c;">✗ No</span>';
        }

        if (is_array($value)) {
            return htmlspecialchars(implode(', ', $value));
        }

        if (is_null($value)) {
            return '<span style="color: #999;">—</span>';
        }

        // Check if it's a URL
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return '<a href="' . htmlspecialchars($value) . '" style="color: #3498db; text-decoration: none;">' . htmlspecialchars($value) . '</a>';
        }

        return htmlspecialchars((string) $value);
    }

    /**
     * Format a value for plain text display
     */
    private function formatValueText(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return implode(', ', $value);
        }

        if (is_null($value)) {
            return '—';
        }

        return (string) $value;
    }
}
