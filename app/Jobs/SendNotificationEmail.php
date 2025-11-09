<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Job to send a single notification email asynchronously
 *
 * This job should be dispatched for individual email sends to avoid
 * blocking the main request thread and improve user experience.
 */
final class SendNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $email,
        private readonly string $subject,
        private readonly string $bodyHtml,
        private readonly ?string $bodyText = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::send([], [], function ($message) {
                $message->to($this->email)
                    ->subject($this->subject)
                    ->html($this->bodyHtml);

                if ($this->bodyText) {
                    $message->text($this->bodyText);
                }
            });

            Log::info('Notification email sent successfully', [
                'email'   => $this->email,
                'subject' => $this->subject,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send notification email', [
                'email'   => $this->email,
                'subject' => $this->subject,
                'error'   => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('SendNotificationEmail job failed', [
            'email'   => $this->email,
            'subject' => $this->subject,
            'error'   => $exception->getMessage(),
        ]);
    }
}
