<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Jobs\SendNotificationEmail;
use App\Models\NotificationQueue;
use App\Models\NotificationRule;
use App\Repositories\NotificationQueueRepository;
use App\Repositories\NotificationRuleRepository;
use App\Repositories\NotificationTemplateRepository;
use App\Services\Contracts\NotificationRuleServiceInterface;
use App\Services\Contracts\NotificationServiceInterface;
use App\Services\Contracts\NotificationTemplateServiceInterface;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\MailException;
use Illuminate\Queue\MaxAttemptsExceededException;
use Psr\Log\LoggerInterface;

final class NotificationService implements NotificationServiceInterface
{
    public function __construct(
        private readonly NotificationTemplateRepository $templateRepository,
        private readonly NotificationRuleRepository $ruleRepository,
        private readonly NotificationQueueRepository $queueRepository,
        protected NotificationTemplateServiceInterface $templateService,
        protected NotificationRuleServiceInterface $ruleService,
        protected LoggerInterface $logger,
        protected Mailer $mailer
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
        } catch (NotFoundException $e) {
            $this->logger->warning('Failed to trigger notifications: resource not found', [
                'trigger_type' => $triggerType,
                'reference'    => $reference,
                'error'        => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to trigger notifications: unexpected error', [
                'trigger_type' => $triggerType,
                'reference'    => $reference,
                'error'        => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notification immediately (dispatches to queue for async processing)
     */
    public function sendImmediate(string $templateSlug, string $email, array $data): bool
    {
        try {
            $template = $this->templateRepository->findBySlug($templateSlug);

            if (! $template) {
                $this->logger->warning("Template not found: {$templateSlug}");

                return false;
            }

            $rendered = $template->render($data);

            // Dispatch to queue for async processing
            dispatch(new SendNotificationEmail(
                $email,
                $rendered['subject'],
                $rendered['body_html'],
                $rendered['body_text']
            ));

            return true;
        } catch (NotFoundException $e) {
            $this->logger->warning('Failed to dispatch notification email: template not found', [
                'template_slug' => $templateSlug,
                'email'         => $email,
                'error'         => $e->getMessage(),
            ]);

            return false;
        } catch (MaxAttemptsExceededException $e) {
            $this->logger->error('Failed to dispatch notification email: queue max attempts exceeded', [
                'template_slug' => $templateSlug,
                'email'         => $email,
                'error'         => $e->getMessage(),
            ]);

            return false;
        } catch (\Exception $e) {
            $this->logger->error('Failed to dispatch notification email: unexpected error', [
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
     * Process pending notifications (delegates to NotificationQueueService)
     */
    public function processPendingNotifications(): int
    {
        $queueService = app(\App\Services\NotificationQueueService::class);

        return $queueService->processPending(100);
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
            $this->mailer->send([], [], function ($message) use ($email, $subject, $bodyHtml, $bodyText) {
                $message->to($email)
                    ->subject($subject)
                    ->html($bodyHtml);

                if ($bodyText) {
                    $message->text($bodyText);
                }
            });

            return true;
        } catch (MailException $e) {
            $this->logger->error('Email sending failed: mail error', [
                'email'   => $email,
                'subject' => $subject,
                'error'   => $e->getMessage(),
            ]);

            return false;
        } catch (\Exception $e) {
            $this->logger->error('Email sending failed: unexpected error', [
                'email'   => $email,
                'subject' => $subject,
                'error'   => $e->getMessage(),
            ]);

            return false;
        }
    }
}
