<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NotificationTemplate;
use App\Repositories\NotificationTemplateRepository;

final class NotificationTemplateService
{
    public function __construct(
        private readonly NotificationTemplateRepository $templateRepository
    ) {}

    /**
     * Get available variables for a category
     */
    public function getAvailableVariables(string $category): array
    {
        return match ($category) {
            'form' => [
                'form_name'        => 'Name of the form',
                'form_description' => 'Form description',
                'user_name'        => 'User\'s full name',
                'user_email'       => 'User\'s email address',
                'user_phone'       => 'User\'s phone number',
                'submitted_at'     => 'Submission timestamp',
                'entry_id'         => 'Form entry ID',
                'form_data'        => 'All form data as JSON',
                'admin_link'       => 'Link to admin panel',
            ],
            'user' => [
                'username'          => 'User\'s username',
                'email'             => 'User\'s email address',
                'full_name'         => 'User\'s full name',
                'registration_date' => 'Account registration date',
                'verification_link' => 'Email verification link',
                'profile_link'      => 'User profile link',
            ],
            'order' => [
                'order_number'     => 'Order number',
                'order_total'      => 'Total order amount',
                'customer_name'    => 'Customer\'s name',
                'customer_email'   => 'Customer\'s email',
                'order_date'       => 'Order placement date',
                'items_count'      => 'Number of items',
                'items_list'       => 'List of ordered items',
                'shipping_address' => 'Shipping address',
                'billing_address'  => 'Billing address',
            ],
            'system' => [
                'site_name'    => 'Website name',
                'site_url'     => 'Website URL',
                'admin_email'  => 'Admin email address',
                'current_date' => 'Current date',
                'current_time' => 'Current time',
            ],
            default => []
        };
    }

    /**
     * Create a new template
     */
    public function createTemplate(array $data): NotificationTemplate
    {
        return $this->templateRepository->createWithUniqueSlug($data);
    }

    /**
     * Update existing template
     */
    public function updateTemplate(NotificationTemplate $template, array $data): NotificationTemplate
    {
        return $this->templateRepository->updateWithSlug($template->id, $data);
    }

    /**
     * Render template with variables and preview
     */
    public function renderPreview(NotificationTemplate $template, array $sampleData = []): array
    {
        $variables = $this->getSampleVariables($template->category);
        $variables = array_merge($variables, $sampleData);

        return $template->render($variables);
    }

    /**
     * Get sample data for template preview
     */
    public function getSampleVariables(string $category): array
    {
        return match ($category) {
            'form' => [
                'form_name'        => 'Test Drive Booking Form',
                'form_description' => 'Book your test drive today',
                'user_name'        => 'John Doe',
                'user_email'       => 'john.doe@example.com',
                'user_phone'       => '+1 234 567 8900',
                'submitted_at'     => now()->format('Y-m-d H:i:s'),
                'entry_id'         => '12345',
                'form_data'        => json_encode([
                    'preferred_date'   => '2024-01-15',
                    'vehicle_interest' => 'Sedan Model A',
                ]),
                'admin_link' => url('/admin/forms'),
            ],
            'user' => [
                'username'          => 'johndoe',
                'email'             => 'john.doe@example.com',
                'full_name'         => 'John Doe',
                'registration_date' => today()->format('Y-m-d'),
                'verification_link' => url('/verify-email/sample-token'),
                'profile_link'      => url('/profile'),
            ],
            'order' => [
                'order_number'     => 'ORD-2024-001',
                'order_total'      => '$299.99',
                'customer_name'    => 'John Doe',
                'customer_email'   => 'john.doe@example.com',
                'order_date'       => today()->format('Y-m-d'),
                'items_count'      => '3',
                'items_list'       => 'Product A, Product B, Product C',
                'shipping_address' => '123 Main St, City, State 12345',
                'billing_address'  => '123 Main St, City, State 12345',
            ],
            'system' => [
                'site_name'    => config('app.name', 'Laravel'),
                'site_url'     => config('app.url'),
                'admin_email'  => config('mail.from.address'),
                'current_date' => today()->format('Y-m-d'),
                'current_time' => now()->format('H:i:s'),
            ],
            default => []
        };
    }

    /**
     * Validate template syntax
     */
    public function validateTemplate(string $content, array $allowedVariables): array
    {
        $errors = [];

        // Find all variables in content
        preg_match_all('/\{(\w+)\}/', $content, $matches);
        $usedVariables = $matches[1] ?? [];

        // Check for undefined variables
        foreach ($usedVariables as $variable) {
            if (! in_array($variable, array_keys($allowedVariables))) {
                $errors[] = "Undefined variable: {{$variable}}";
            }
        }

        return $errors;
    }

    /**
     * Duplicate template
     */
    public function duplicateTemplate(NotificationTemplate $template): NotificationTemplate
    {
        return $this->templateRepository->duplicate($template->id);
    }
}
