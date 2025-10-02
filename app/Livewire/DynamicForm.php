<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Form;
use App\Services\DynamicFormService;
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

    protected function initializeComponent(): void
    {
        $formSlug = $this->queryOptions['form_slug'] ?? 'test-drive-booking';

        try {
            $formService = app(DynamicFormService::class);
            $this->form  = $formService->getFormBySlug($formSlug);

            if (! $this->form) {
                \Log::warning("Form not found for slug: {$formSlug}. Using legacy fallback.");
                $this->form = null;
            }

            $this->formFields = $formService->getFields($this->form);
        } catch (\Exception $e) {
            \Log::error("Error loading form with slug {$formSlug}: " . $e->getMessage());
            $this->form = null;
        }
    }

    /**
     * Submit the form
     */
    public function submit(): void
    {
        try {
            $request = request();
            $request->merge([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer'    => $request->header('referer'),
            ]);
            $formService = app(DynamicFormService::class);
            $formService->submitForm($this->form->slug, $this->formData, $request);

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
        $this->initializeComponent();
        $this->resetValidation();
    }
}
