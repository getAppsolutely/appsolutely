<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NotificationQueue;
use App\Models\NotificationRule;
use App\Repositories\NotificationQueueRepository;
use App\Repositories\NotificationRuleRepository;
use App\Repositories\NotificationTemplateRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class NotificationService
{
    public function __construct(
        private readonly NotificationTemplateRepository $templateRepository,
        private readonly NotificationRuleRepository $ruleRepository,
        private readonly NotificationQueueRepository $queueRepository,
        protected NotificationTemplateService $templateService,
        protected NotificationRuleService $ruleService
    ) {}

    /**
     * Trigger notifications based on event
     */
    public function trigger(string $triggerType, string $reference, array $data): void
    {
        try {
            $rules = $this->ruleRepository->findByTrigger($triggerType, $reference);

            foreach ($rules as $rule) {
                if ($this->ruleService->evaluateConditions($rule, $data)) {
                    $this->processRule($rule, $data);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to trigger notifications', [
                'trigger_type' => $triggerType,
                'reference'    => $reference,
                'error'        => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notification immediately
     */
    public function sendImmediate(string $templateSlug, string $email, array $data): bool
    {
        try {
            $template = $this->templateRepository->findBySlug($templateSlug);

            if (! $template) {
                Log::warning("Template not found: {$templateSlug}");

                return false;
            }

            $rendered = $template->render($data);

            return $this->sendEmail($email, $rendered['subject'], $rendered['body_html'], $rendered['body_text']);
        } catch (\Exception $e) {
            Log::error('Failed to send immediate notification', [
                'template_slug' => $templateSlug,
                'email'         => $email,
                'error'         => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Schedule notification for later
     */
    public function schedule(string $templateSlug, string $email, array $data, \Carbon\Carbon $when): NotificationQueue
    {
        $template = $this->templateRepository->findBySlug($templateSlug);
        if (! $template) {
            throw new \InvalidArgumentException("Template not found: {$templateSlug}");
        }

        $rendered = $template->render($data);

        return $this->queueRepository->createQueueItem([
            'template_id'     => $template->id,
            'recipient_email' => $email,
            'subject'         => $rendered['subject'],
            'body_html'       => $rendered['body_html'],
            'body_text'       => $rendered['body_text'],
            'variables'       => $data,
            'scheduled_at'    => $when,
            'status'          => 'pending',
        ]);
    }

    /**
     * Process pending notifications
     */
    public function processPendingNotifications(): int
    {
        $processed     = 0;
        $notifications = $this->queueRepository->getPendingToSend()->take(100);

        foreach ($notifications as $notification) {
            try {
                $success = $this->sendEmail(
                    $notification->recipient_email,
                    $notification->subject,
                    $notification->body_html,
                    $notification->body_text
                );

                if ($success) {
                    $this->queueRepository->updateStatus($notification->id, 'sent');
                    $processed++;
                } else {
                    $this->queueRepository->updateStatus($notification->id, 'failed', 'Email sending failed');
                }
            } catch (\Exception $e) {
                $this->queueRepository->updateStatus($notification->id, 'failed', $e->getMessage());
                Log::error('Failed to send queued notification', [
                    'notification_id' => $notification->id,
                    'error'           => $e->getMessage(),
                ]);
            }
        }

        return $processed;
    }

    /**
     * Process queue (alias for processPendingNotifications)
     */
    public function processQueue(): int
    {
        return $this->processPendingNotifications();
    }

    /**
     * Get notification statistics
     */
    public function getStatistics(): array
    {
        $queueStats = $this->queueRepository->getStatistics();

        return [
            'templates_count'       => $this->templateRepository->getActive()->count(),
            'rules_count'           => $this->ruleRepository->getActiveWithTemplates()->count(),
            'pending_notifications' => $queueStats['pending'],
            'sent_today'            => $queueStats['today'],
            'failed_today'          => $queueStats['failed'],
        ];
    }

    /**
     * Process a specific rule
     */
    protected function processRule(NotificationRule $rule, array $data): void
    {
        $recipients  = $this->ruleService->getRecipients($rule, $data);
        $scheduledAt = $rule->getScheduledAt();

        foreach ($recipients as $email) {
            $rendered = $rule->template->render($data);

            $this->queueRepository->createQueueItem([
                'rule_id'         => $rule->id,
                'template_id'     => $rule->template_id,
                'recipient_email' => $email,
                'subject'         => $rendered['subject'],
                'body_html'       => $rendered['body_html'],
                'body_text'       => $rendered['body_text'],
                'variables'       => $data,
                'scheduled_at'    => $scheduledAt,
                'status'          => 'pending',
            ]);
        }
    }

    /**
     * Send email using Laravel's mail system
     */
    protected function sendEmail(string $email, string $subject, string $bodyHtml, ?string $bodyText = null): bool
    {
        try {
            Mail::send([], [], function ($message) use ($email, $subject, $bodyHtml, $bodyText) {
                $message->to($email)
                    ->subject($subject)
                    ->html($bodyHtml);

                if ($bodyText) {
                    $message->text($bodyText);
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Email sending failed', [
                'email'   => $email,
                'subject' => $subject,
                'error'   => $e->getMessage(),
            ]);

            return false;
        }
    }
}
