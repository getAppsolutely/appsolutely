<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\FormSubmitted;
use App\Models\Form;
use App\Models\FormEntry;
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
            // Load form fields relationship if not already loaded
            if (! $event->form->relationLoaded('fields')) {
                $event->form->load('fields');
            }

            // Prepare notification data matching the format expected by NotificationService
            $notificationData = [
                'form_name'        => $event->form->name,
                'user_name'        => $event->entry->getUserName(),
                'form_fields_html' => $this->formatFieldsAsHtml($event->form, $event->entry),
                'form_fields_text' => $this->formatFieldsAsText($event->form, $event->entry),
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
     * Format form fields as HTML table rows based on database form fields
     */
    private function formatFieldsAsHtml(Form $form, FormEntry $entry): string
    {
        $html = '<table id="form-fields-table">' . "\n";

        // Loop through all form fields from database
        foreach ($form->fields as $field) {
            // Get value from entry (check direct properties first, then data JSON)
            $value = $this->getFieldValue($entry, $field->name);

            // Skip if value is null or empty
            if ($value === null || $value === '') {
                continue;
            }

            // Format the value based on field type
            $formattedValue = $this->formatValue($value, $field->type);

            // Add table row using field label from database
            $html .= '    <tr>' . "\n";
            $html .= '        <td class="form-field-label">' . htmlspecialchars($field->label) . ':</td>' . "\n";
            $html .= '        <td class="form-field-value">' . $formattedValue . '</td>' . "\n";
            $html .= '    </tr>' . "\n";
        }

        $html .= '</table>';

        return $html;
    }

    /**
     * Format form fields as plain text based on database form fields
     */
    private function formatFieldsAsText(Form $form, FormEntry $entry): string
    {
        $text = '';

        // Loop through all form fields from database
        foreach ($form->fields as $field) {
            // Get value from entry (check direct properties first, then data JSON)
            $value = $this->getFieldValue($entry, $field->name);

            // Skip if value is null or empty
            if ($value === null || $value === '') {
                continue;
            }

            // Format the value based on field type
            $formattedValue = $this->formatValueText($value, $field->type);

            // Add line using field label from database
            $text .= $field->label . ': ' . $formattedValue . "\n";
        }

        return $text ? "\n" . $text : '';
    }

    /**
     * Get field value from entry (checks direct properties first, then data JSON)
     */
    private function getFieldValue(FormEntry $entry, string $fieldName): mixed
    {
        // Check direct properties on entry (name, email, mobile, first_name, last_name)
        $directProperties = ['name', 'email', 'mobile', 'first_name', 'last_name'];

        if (in_array($fieldName, $directProperties)) {
            $value = $entry->{$fieldName};
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        // Fall back to data JSON column
        return $entry->getFieldValue($fieldName);
    }

    /**
     * Format a value for HTML display based on field type
     */
    private function formatValue(mixed $value, string $fieldType): string
    {
        // Handle file uploads
        if ($fieldType === 'file') {
            if (is_array($value)) {
                $files = [];
                foreach ($value as $file) {
                    if (is_array($file) && isset($file['name'])) {
                        $files[] = htmlspecialchars($file['name']);
                    } else {
                        $files[] = htmlspecialchars((string) $file);
                    }
                }

                return implode(', ', $files);
            }

            return htmlspecialchars((string) $value);
        }

        // Handle checkboxes and multiple selects
        if (in_array($fieldType, ['checkbox', 'multiple_select']) || is_array($value)) {
            if (is_array($value)) {
                return htmlspecialchars(implode(', ', $value));
            }
        }

        if (is_bool($value)) {
            return $value ? '<span style="color: #27ae60;">✓ Yes</span>' : '<span style="color: #e74c3c;">✗ No</span>';
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
     * Format a value for plain text display based on field type
     */
    private function formatValueText(mixed $value, string $fieldType): string
    {
        // Handle file uploads
        if ($fieldType === 'file') {
            if (is_array($value)) {
                $files = [];
                foreach ($value as $file) {
                    if (is_array($file) && isset($file['name'])) {
                        $files[] = $file['name'];
                    } else {
                        $files[] = (string) $file;
                    }
                }

                return implode(', ', $files);
            }

            return (string) $value;
        }

        // Handle checkboxes and multiple selects
        if (in_array($fieldType, ['checkbox', 'multiple_select']) || is_array($value)) {
            if (is_array($value)) {
                return implode(', ', $value);
            }
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_null($value)) {
            return '—';
        }

        return (string) $value;
    }
}
