<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\FormFieldType;
use App\Models\Form;
use App\Services\Contracts\DynamicFormServiceInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DynamicForm extends GeneralBlock
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
        'form_slug' => 'test-drive',
    ];

    protected function initializeComponent(Container $container): void
    {
        // Resolve DynamicFormService from container (Livewire doesn't support constructor injection)
        $this->formService = $container->make(DynamicFormServiceInterface::class);

        $formSlug = $this->queryOptions['form_slug'] ?? '';

        try {
            $this->form = $this->formService->getFormBySlug($formSlug);

            if (! $this->form) {
                \Log::warning("Form not found for slug: {$formSlug}.");
                abort(404);
            }

            $this->formFields = $this->formService->getFields($this->form);
            $this->initializeFormDataFromQuery();
        } catch (NotFoundHttpException $e) {
            // Re-throw 404 exceptions so they're properly handled
            throw $e;
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
            $fieldType = $fieldConfig['type'] ?? null;
            if (! $fieldType instanceof FormFieldType || ! $fieldType->supportsOptions()) {
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
     * Tries exact match, hyphen/space variations, and case-insensitive matching
     *
     * @param  string  $queryValue  The value from URL query parameter
     * @param  array<string>  $options  Available form field options
     * @return string|null The matched option value or null if no match found
     */
    protected function findMatchingOption(string $queryValue, array $options): ?string
    {
        $normalize = static fn (string $v): string => strtolower(trim($v));

        // First, try exact match
        if (in_array($queryValue, $options, true)) {
            return $queryValue;
        }

        // Try hyphen/space variations (case-sensitive)
        $queryValueWithSpaces = str_replace('-', ' ', $queryValue);
        if (in_array($queryValueWithSpaces, $options, true)) {
            return $queryValueWithSpaces;
        }

        $queryValueWithHyphens = str_replace(' ', '-', $queryValue);
        if (in_array($queryValueWithHyphens, $options, true)) {
            return $queryValueWithHyphens;
        }

        // Case-insensitive match (e.g. "aion-v" matches "AION V")
        $queryNormalized        = $normalize($queryValue);
        $queryNormalizedSpaces  = $normalize(str_replace('-', ' ', $queryValue));
        $queryNormalizedHyphens = $normalize(str_replace(' ', '-', $queryValue));

        foreach ($options as $option) {
            $optNormalized = $normalize($option);
            if ($optNormalized    === $queryNormalized
                || $optNormalized === $queryNormalizedSpaces
                || $optNormalized === $queryNormalizedHyphens) {
                return $option;
            }
        }

        return null;
    }

    /**
     * Submit the form
     */
    public function submit(): void
    {
        // Apply rate limiting to prevent spam (5 submissions per minute per IP)
        $key = 'form-submission:' . client_ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'form' => "Too many submission attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        try {
            $request = request();
            $request->merge([
                'ip_address' => client_ip($request),
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
        $this->formData       = [];
        $this->resetValidation();
        $this->initializeComponent(app());
    }
}
