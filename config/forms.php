<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Form System Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for form submission handling including spam detection,
    | validation settings, and export options.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Spam Detection
    |--------------------------------------------------------------------------
    |
    | Configuration for basic spam detection. Can be enhanced with more
    | sophisticated detection methods (e.g., machine learning, third-party services).
    |
    */
    'spam_detection' => [
        /*
         * Keywords that trigger spam detection when found in form submissions
         */
        'keywords' => array_filter(explode(',', env('FORM_SPAM_KEYWORDS', 'viagra,casino,lottery,prize,winner'))),

        /*
         * Enable email validation as spam indicator
         * Invalid email addresses are often a sign of spam
         */
        'validate_email' => env('FORM_SPAM_VALIDATE_EMAIL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Pagination
    |--------------------------------------------------------------------------
    |
    | Default number of entries per page for form entry listings.
    |
    */
    'default_per_page' => env('FORM_DEFAULT_PER_PAGE', 15),

    /*
    |--------------------------------------------------------------------------
    | Export Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for form entry exports.
    |
    */
    'export' => [
        /*
         * Include spam entries in exports by default
         */
        'include_spam' => env('FORM_EXPORT_INCLUDE_SPAM', false),

        /*
         * Maximum number of entries to export in a single request
         */
        'max_entries' => env('FORM_EXPORT_MAX_ENTRIES', 10000),
    ],
];
