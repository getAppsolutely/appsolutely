<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Models\Model;
use App\Models\NotificationRule;
use App\Repositories\NotificationRuleRepository;
use App\Services\Contracts\NotificationRuleServiceInterface;
use Illuminate\Database\Eloquent\Collection;

final class NotificationRuleService implements NotificationRuleServiceInterface
{
    public function __construct(
        private readonly NotificationRuleRepository $ruleRepository
    ) {}

    /**
     * Find rules for specific trigger
     */
    public function findRulesForTrigger(string $triggerType, string $reference): Collection
    {
        return $this->ruleRepository->findByTrigger($triggerType, $reference);
    }

    /**
     * Evaluate if rule conditions are met
     */
    public function evaluateConditions(NotificationRule $rule, array $data): bool
    {
        return $rule->evaluateConditions($data);
    }

    /**
     * Get recipients for a rule
     */
    public function getRecipients(NotificationRule $rule, array $data): array
    {
        return match ($rule->recipient_type) {
            'admin'       => $this->getAdminEmails(),
            'user'        => $this->getUserEmails($data),
            'custom'      => $rule->recipient_emails_list,
            'conditional' => $this->getConditionalEmails($rule, $data),
            default       => []
        };
    }

    /**
     * Create new notification rule
     */
    public function createRule(array $data): Model
    {
        return NotificationRule::create($data);
    }

    /**
     * Update existing rule
     */
    public function updateRule(NotificationRule $rule, array $data): NotificationRule
    {
        $rule->update($data);

        return $rule->fresh();
    }

    /**
     * Test rule with sample data
     */
    public function testRule(NotificationRule $rule, array $sampleData): array
    {
        $result = [
            'conditions_met'   => $this->evaluateConditions($rule, $sampleData),
            'recipients'       => [],
            'template_preview' => null,
        ];

        if ($result['conditions_met']) {
            $result['recipients']       = $this->getRecipients($rule, $sampleData);
            $result['template_preview'] = $rule->template->render($sampleData);
        }

        return $result;
    }

    /**
     * Test rule by ID with sample data
     */
    public function testRuleById(int $id): array
    {
        $rule = $this->ruleRepository->find($id);

        if (! $rule) {
            throw new NotFoundException("Notification rule with ID {$id} not found");
        }

        // Generate sample data based on trigger type
        $sampleData = $this->generateSampleData($rule->trigger_type);

        return $this->testRule($rule, $sampleData);
    }

    /**
     * Generate sample data for testing
     */
    protected function generateSampleData(string $triggerType): array
    {
        return match ($triggerType) {
            'form_submission' => [
                'name'         => 'John Doe',
                'email'        => 'john@example.com',
                'phone'        => '+1234567890',
                'message'      => 'This is a test message',
                'form_name'    => 'Contact Form',
                'submitted_at' => now()->toDateTimeString(),
            ],
            'user_registration' => [
                'name'          => 'Jane Smith',
                'email'         => 'jane@example.com',
                'registered_at' => now()->toDateTimeString(),
                'ip_address'    => '192.168.1.1',
            ],
            'order_placed' => [
                'order_id'       => 'ORD-12345',
                'customer_name'  => 'John Customer',
                'customer_email' => 'customer@example.com',
                'total_amount'   => 99.99,
                'order_date'     => now()->toDateTimeString(),
            ],
            default => [
                'event_type' => $triggerType,
                'timestamp'  => now()->toDateTimeString(),
                'data'       => 'Sample data for ' . $triggerType,
            ]
        };
    }

    /**
     * Get available trigger types
     */
    public function getAvailableTriggerTypes(): array
    {
        return [
            'form_submission'        => 'Form Submission',
            'user_registration'      => 'User Registration',
            'user_login'             => 'User Login',
            'order_placed'           => 'Order Placed',
            'order_shipped'          => 'Order Shipped',
            'order_delivered'        => 'Order Delivered',
            'payment_received'       => 'Payment Received',
            'subscription_created'   => 'Subscription Created',
            'subscription_cancelled' => 'Subscription Cancelled',
            'system_error'           => 'System Error',
            'custom'                 => 'Custom Event',
        ];
    }

    /**
     * Get available condition operators
     */
    public function getConditionOperators(): array
    {
        return [
            'equals'       => 'Equals',
            'not_equals'   => 'Not Equals',
            'contains'     => 'Contains',
            'starts_with'  => 'Starts With',
            'ends_with'    => 'Ends With',
            'in'           => 'In List',
            'greater_than' => 'Greater Than',
            'less_than'    => 'Less Than',
        ];
    }

    /**
     * Get recipient type options
     */
    public function getRecipientTypes(): array
    {
        return [
            'admin'       => 'Admin Emails',
            'user'        => 'Form Submitter',
            'custom'      => 'Custom Email List',
            'conditional' => 'Conditional Recipients',
        ];
    }

    /**
     * Get admin emails from config or database
     */
    protected function getAdminEmails(): array
    {
        // Default admin emails - could be moved to config or settings table
        return [
            config('mail.from.address', 'admin@example.com'),
            // Add more admin emails as needed
        ];
    }

    /**
     * Extract user emails from data
     *
     * Searches for email addresses in common field names and validates them.
     * Returns unique list of valid email addresses found in the data.
     */
    protected function getUserEmails(array $data): array
    {
        $emails = [];

        // Try different possible email field names that might be used in various contexts
        // This handles different data structures (forms, orders, registrations, etc.)
        $emailFields = ['email', 'user_email', 'customer_email', 'contact_email'];

        foreach ($emailFields as $field) {
            // Only add valid email addresses to prevent invalid data
            if (isset($data[$field]) && filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                $emails[] = $data[$field];
            }
        }

        // Remove duplicates in case multiple fields contain the same email
        return array_unique($emails);
    }

    /**
     * Get conditional emails based on rule conditions
     *
     * Returns recipient emails only if the rule's conditions are satisfied by the provided data.
     * This allows dynamic recipient selection based on form/event data.
     */
    protected function getConditionalEmails(NotificationRule $rule, array $data): array
    {
        // Only return emails if rule conditions are met
        // This enables conditional notifications (e.g., only notify if order amount > $100)
        if ($rule->evaluateConditions($data)) {
            return $rule->recipient_emails_list;
        }

        // Return empty array if conditions are not met
        return [];
    }

    /**
     * Validate rule data
     */
    public function validateRule(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Rule name is required';
        }

        if (empty($data['trigger_type'])) {
            $errors[] = 'Trigger type is required';
        }

        if (empty($data['template_id'])) {
            $errors[] = 'Template is required';
        }

        if ($data['recipient_type'] === 'custom' && empty($data['recipient_emails'])) {
            $errors[] = 'Recipient emails are required for custom type';
        }

        if (! empty($data['conditions'])) {
            $conditions = $data['conditions'];
            if (empty($conditions['field']) || empty($conditions['operator'])) {
                $errors[] = 'Complete condition configuration is required';
            }
        }

        return $errors;
    }
}
