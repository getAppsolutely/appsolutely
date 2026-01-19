<?php

declare(strict_types=1);

use App\Jobs\ProcessMissingTranslations;
use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

// ============================================================================
// Console Commands
// ============================================================================

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================================
// Scheduled Tasks
// ============================================================================

// Run the translation job every minute
// withoutOverlapping ensures that if one execution is still running, a new one won't start
Schedule::job(new ProcessMissingTranslations(null, 10))
    ->everyMinute()
    ->withoutOverlapping()
    ->onFailure(function () {
        Log::error('Translation job failed to schedule');
    });

// Regenerate sitemap every hour
Schedule::command('sitemap:generate --force')
    ->hourly()
    ->withoutOverlapping()
    ->onFailure(function () {
        Log::error('Sitemap generation failed');
    });

// Process notification queue every minute
// This reads from notification_queue table and dispatches jobs to Laravel queue
Schedule::command('notifications:process-queue --once')
    ->everyMinute()
    ->withoutOverlapping()
    ->onFailure(function () {
        Log::error('Notification queue processing failed');
    });
