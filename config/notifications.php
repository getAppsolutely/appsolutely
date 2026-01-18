<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Notification System Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the notification system including retry limits,
    | admin emails, email field mappings, and cleanup settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Maximum number of retry attempts for failed notifications before
    | marking them as permanently failed.
    |
    */
    'max_retry_attempts' => env('NOTIFICATION_MAX_RETRY_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Admin Emails
    |--------------------------------------------------------------------------
    |
    | Default admin email addresses for notifications. These can be
    | overridden via environment variables or extended in the service.
    |
    */
    'admin_emails' => array_filter(explode(',', env('NOTIFICATION_ADMIN_EMAILS', ''))),

    /*
    |--------------------------------------------------------------------------
    | Email Field Mapping
    |--------------------------------------------------------------------------
    |
    | Common field names that might contain email addresses in different
    | contexts (forms, orders, registrations, etc.). Used for extracting
    | user emails from event data.
    |
    */
    'email_field_names' => [
        'email',
        'user_email',
        'customer_email',
        'contact_email',
        'recipient_email',
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Cleanup
    |--------------------------------------------------------------------------
    |
    | Number of days to keep sent notifications before cleaning them up.
    | Set to 0 to disable automatic cleanup.
    |
    */
    'cleanup_sent_after_days' => env('NOTIFICATION_CLEANUP_SENT_AFTER_DAYS', 90),

    /*
    |--------------------------------------------------------------------------
    | Default Pagination
    |--------------------------------------------------------------------------
    |
    | Default number of items per page for notification queue listings.
    |
    */
    'default_per_page' => env('NOTIFICATION_DEFAULT_PER_PAGE', 20),

    /*
    |--------------------------------------------------------------------------
    | Internal Email Domains
    |--------------------------------------------------------------------------
    |
    | Email domains that are considered internal (staff/admin).
    | Used for auto-detecting sender category.
    |
    */
    'internal_domains' => array_filter(explode(',', env('NOTIFICATION_INTERNAL_DOMAINS', '@company.com,@internal.company.com'))),
];
