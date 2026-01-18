<?php

use App\Jobs\ProcessMissingTranslations;
use Illuminate\Support\Facades\Schedule;

return function (Schedule $schedule) {
    // Run the translation job every minute
    // withoutOverlapping ensures that if one execution is still running, a new one won't start
    $schedule->job(new ProcessMissingTranslations(null, 10))
        ->everyMinute()
        ->withoutOverlapping()
        ->onFailure(function () {
            // Log failure if needed
            \Illuminate\Support\Facades\Log::error('Translation job failed to schedule');
        });

    // Regenerate sitemap every hour
    $schedule->command('sitemap:generate --force')
        ->hourly()
        ->withoutOverlapping()
        ->onFailure(function () {
            \Illuminate\Support\Facades\Log::error('Sitemap generation failed');
        });

    // Process notification queue every minute
    // This reads from notification_queue table and dispatches jobs to Laravel queue
    $schedule->command('notifications:process-queue --once')
        ->everyMinute()
        ->withoutOverlapping()
        ->onFailure(function () {
            \Illuminate\Support\Facades\Log::error('Notification queue processing failed');
        });
};
