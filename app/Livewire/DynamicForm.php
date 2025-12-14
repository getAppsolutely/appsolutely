<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Form;
use App\Services\Contracts\DynamicFormServiceInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

final class DynamicForm extends BaseBlock
{
    /**
     * @var array<string, mixed>
     */
    public array $formData = [];

    public array $formFields = [];

    public bool $submitted = false;

    public string $successMessage = '';

    public ?Form $form = null;

    protected ?DynamicFormServiceInterface $formService = null;

    protected array $defaultQueryOptions = [
        'form_slug' => 'test-drive-booking', // Database form slug to load
        'email_to'  => 'sales@company.com',
    ];

    protected array $defaultDisplayOptions = [
        'title'                 => 'Test Drive Booking',
        'subtitle'              => 'Book your test drive today',
        'description'           => 'Fill out the form below and we\'ll get back to you as soon as possible.',
        'submit_text'           => 'Book Test Drive',
        'success_title'         => 'Thank You!',
        'success_message'       => '✅ Your test drive booking has been submitted. We\'ll contact you soon to confirm your appointment.',
        'layout'                => 'form', // form, modal
        'theme'                 => 'default',
        'columns'               => 1, // 1 or 2 columns
        'redirect_after_submit' => '', // URL to redirect after successful submission
    ];

    protected function initializeComponent(Container $container): void
    {
        // Resolve DynamicFormService from container (Livewire doesn't support constructor injection)
        $this->formService = $container->make(DynamicFormServiceInterface::class);

        $formSlug = $this->queryOptions['form_slug'] ?? 'test-drive-booking';

        try {
            $this->form = $this->formService->getFormBySlug($formSlug);

            if (! $this->form) {
                \Log::warning("Form not found for slug: {$formSlug}. Using legacy fallback.");
                $this->form       = null;
                $this->formFields = [];
            } else {
                $this->formFields = $this->formService->getFields($this->form);
                $this->initializeFormDataFromQuery();
            }
        } catch (\Exception $e) {
            \Log::error("Error loading form with slug {$formSlug}: " . $e->getMessage());
            $this->form       = null;
            $this->formFields = [];
        }
    }

    /**
     * Initialize form data from URL query parameters
     * Matches query parameter values to form field options
     * First tries exact match, then tries matching with hyphens replaced by spaces
     */
    protected function initializeFormDataFromQuery(): void
    {
        $queryParams = request()->query();

        foreach ($queryParams as $paramName => $paramValue) {
            // Skip if parameter doesn't match any form field
            if (! isset($this->formFields[$paramName])) {
                continue;
            }

            // Skip if value is not a string
            if (! is_string($paramValue)) {
                continue;
            }

            $fieldConfig = $this->formFields[$paramName];

            // Only process select and multiselect fields
            if (! in_array($fieldConfig['type'] ?? '', ['select', 'multiselect'])) {
                continue;
            }

            $options = $fieldConfig['options'] ?? [];
            if (empty($options)) {
                continue;
            }

            // Find matching option value
            $matchedValue = $this->findMatchingOption($paramValue, $options);

            if ($matchedValue !== null) {
                $this->formData[$paramName] = $matchedValue;
            }
        }
    }

    /**
     * Find matching option value from query parameter
     * First tries exact match, then tries matching with hyphens replaced by spaces
     *
     * @param  string  $queryValue  The value from URL query parameter
     * @param  array<string>  $options  Available form field options
     * @return string|null The matched option value or null if no match found
     */
    protected function findMatchingOption(string $queryValue, array $options): ?string
    {
        // First, try exact match
        if (in_array($queryValue, $options, true)) {
            return $queryValue;
        }

        // Then, try matching with hyphens replaced by spaces
        // e.g., "Product-1" should match "Product 1"
        $queryValueWithSpaces = str_replace('-', ' ', $queryValue);
        if (in_array($queryValueWithSpaces, $options, true)) {
            return $queryValueWithSpaces;
        }

        // Also try the reverse: if query has spaces, try with hyphens
        // e.g., "Product 1" should match "Product-1"
        $queryValueWithHyphens = str_replace(' ', '-', $queryValue);
        if (in_array($queryValueWithHyphens, $options, true)) {
            return $queryValueWithHyphens;
        }

        return null;
    }

    /**
     * Submit the form
     */
    public function submit(): void
    {
        // Apply rate limiting to prevent spam (5 submissions per minute per IP)
        $key = 'form-submission:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'form' => "Too many submission attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        try {
            $request = request();
            $request->merge([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer'    => $request->header('referer'),
            ]);
            // Ensure formService is resolved
            if (! $this->formService) {
                $this->formService = app(DynamicFormServiceInterface::class);
            }
            $this->formService->submitForm($this->form->slug, $this->formData, $request);

            // Hit rate limiter on successful submission (60 second decay)
            RateLimiter::hit($key, 60);

            // Set success state
            $this->submitted      = true;
            $this->successMessage = $this->displayOptions['success_message'];

            // Redirect if configured
            if (! empty($this->displayOptions['redirect_after_submit'])) {
                $this->redirect($this->displayOptions['redirect_after_submit']);
            }

        } catch (ValidationException $e) {
            // Re-throw validation exception to show errors
            throw $e;
        } catch (\Exception $e) {
            // Log error and show user-friendly message
            \Log::error('Form submission error: ' . $e->getMessage(), [
                'form_data' => $this->formData,
                'exception' => $e,
            ]);

            session()->flash('error', 'There was an error processing your request. Please try again.');
        }
    }

    /**
     * Reset the form
     */
    public function resetForm(): void
    {
        $this->submitted      = false;
        $this->successMessage = '';
        $this->initializeComponent(app());
        $this->resetValidation();
    }
}
