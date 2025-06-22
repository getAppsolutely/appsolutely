<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

final class DynamicForm extends Component
{
    /**
     * @var array<string, mixed>
     */
    public array $formConfig = [];

    /**
     * @var array<string, mixed>
     */
    public array $formData = [];

    public bool $submitted = false;

    public string $successMessage = '';

    /**
     * Mount the component with form configuration.
     *
     * @param  array<string, mixed>  $formConfig
     */
    public function mount(array $formConfig = []): void
    {
        $this->formConfig = array_merge($this->defaultConfig(), $formConfig);

        // Initialize form data with empty values
        $this->initializeFormData();
    }

    /**
     * Get default form configuration.
     *
     * @return array<string, mixed>
     */
    private function defaultConfig(): array
    {
        return [
            'title'           => 'Test Drive Booking',
            'subtitle'        => 'Book your test drive today',
            'description'     => 'Fill out the form below and we\'ll get back to you within 24 hours.',
            'submit_text'     => 'Book Test Drive',
            'success_title'   => 'Thank You!',
            'success_message' => 'Your test drive booking has been submitted. We\'ll contact you soon to confirm your appointment.',
            'fields'          => [
                'name' => [
                    'type'        => 'text',
                    'label'       => 'Full Name',
                    'placeholder' => 'Enter your full name',
                    'required'    => true,
                    'validation'  => 'required|string|max:255',
                ],
                'email' => [
                    'type'        => 'email',
                    'label'       => 'Email Address',
                    'placeholder' => 'Enter your email address',
                    'required'    => true,
                    'validation'  => 'required|email|max:255',
                ],
                'phone' => [
                    'type'        => 'tel',
                    'label'       => 'Phone Number',
                    'placeholder' => 'Enter your phone number',
                    'required'    => true,
                    'validation'  => 'required|string|max:20',
                ],
                'preferred_date' => [
                    'type'        => 'date',
                    'label'       => 'Preferred Date',
                    'placeholder' => '',
                    'required'    => true,
                    'validation'  => 'required|date|after:today',
                ],
                'preferred_time' => [
                    'type'        => 'select',
                    'label'       => 'Preferred Time',
                    'placeholder' => 'Select preferred time',
                    'required'    => true,
                    'validation'  => 'required|string',
                    'options'     => [
                        '9:00 AM - 10:00 AM',
                        '10:00 AM - 11:00 AM',
                        '11:00 AM - 12:00 PM',
                        '12:00 PM - 1:00 PM',
                        '1:00 PM - 2:00 PM',
                        '2:00 PM - 3:00 PM',
                        '3:00 PM - 4:00 PM',
                        '4:00 PM - 5:00 PM',
                    ],
                ],
                'vehicle_interest' => [
                    'type'        => 'select',
                    'label'       => 'Vehicle of Interest',
                    'placeholder' => 'Select a vehicle',
                    'required'    => false,
                    'validation'  => 'nullable|string',
                    'options'     => [
                        'Sedan Model A',
                        'SUV Model B',
                        'Hatchback Model C',
                        'Electric Model D',
                        'Hybrid Model E',
                        'Not sure yet',
                    ],
                ],
                'license_valid' => [
                    'type'       => 'checkbox',
                    'label'      => 'I have a valid driver\'s license',
                    'required'   => true,
                    'validation' => 'accepted',
                ],
                'message' => [
                    'type'        => 'textarea',
                    'label'       => 'Additional Comments',
                    'placeholder' => 'Any specific requirements or questions?',
                    'required'    => false,
                    'validation'  => 'nullable|string|max:1000',
                    'rows'        => 4,
                ],
            ],
            'layout'                => 'form', // form, modal
            'theme'                 => 'default', // default, card, minimal
            'columns'               => 1, // 1 or 2 columns
            'save_to_db'            => true, // Whether to save submissions to database
            'send_email'            => true, // Whether to send email notifications
            'email_to'              => 'sales@company.com',
            'redirect_after_submit' => '', // URL to redirect after successful submission
        ];
    }

    /**
     * Initialize form data with empty values
     */
    private function initializeFormData(): void
    {
        foreach ($this->formConfig['fields'] as $fieldName => $fieldConfig) {
            $this->formData[$fieldName] = match ($fieldConfig['type']) {
                'checkbox'    => false,
                'multiselect' => [],
                default       => '',
            };
        }
    }

    /**
     * Get validation rules from form configuration
     */
    private function getValidationRules(): array
    {
        $rules = [];

        foreach ($this->formConfig['fields'] as $fieldName => $fieldConfig) {
            if (isset($fieldConfig['validation'])) {
                $rules["formData.{$fieldName}"] = $fieldConfig['validation'];
            }
        }

        return $rules;
    }

    /**
     * Get custom validation messages
     */
    private function getValidationMessages(): array
    {
        $messages = [];

        foreach ($this->formConfig['fields'] as $fieldName => $fieldConfig) {
            $label = $fieldConfig['label'] ?? ucfirst(str_replace('_', ' ', $fieldName));

            $messages["formData.{$fieldName}.required"] = "The {$label} field is required.";
            $messages["formData.{$fieldName}.email"]    = "The {$label} must be a valid email address.";
            $messages["formData.{$fieldName}.date"]     = "The {$label} must be a valid date.";
            $messages["formData.{$fieldName}.after"]    = "The {$label} must be a date after today.";
            $messages["formData.{$fieldName}.max"]      = "The {$label} may not be greater than :max characters.";
            $messages["formData.{$fieldName}.accepted"] = "The {$label} must be accepted.";
        }

        return $messages;
    }

    /**
     * Submit the form
     */
    public function submit(): void
    {
        try {
            // Validate form data
            $rules    = $this->getValidationRules();
            $messages = $this->getValidationMessages();

            $validator = Validator::make(['formData' => $this->formData], $rules, $messages);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validatedData = $validator->validated()['formData'];

            // Save to database if configured
            if ($this->formConfig['save_to_db']) {
                $this->saveToDatabase($validatedData);
            }

            // Send email if configured
            if ($this->formConfig['send_email']) {
                $this->sendEmailNotification($validatedData);
            }

            // Set success state
            $this->submitted      = true;
            $this->successMessage = $this->formConfig['success_message'];

            // Redirect if configured
            if (! empty($this->formConfig['redirect_after_submit'])) {
                $this->redirect($this->formConfig['redirect_after_submit']);
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
     * Save form submission to database
     */
    private function saveToDatabase(array $data): void
    {
        // For now, we'll save to a generic form_submissions table
        // You can create a specific model and table for test drive bookings if needed
        \DB::table('form_submissions')->insert([
            'form_type'    => 'test_drive_booking',
            'data'         => json_encode($data),
            'submitted_at' => now(),
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification(array $data): void
    {
        // Basic email notification
        // You can enhance this with proper email templates and queued jobs
        $subject = 'New Test Drive Booking Request';
        $message = "New test drive booking request:\n\n";

        foreach ($data as $field => $value) {
            $fieldConfig = $this->formConfig['fields'][$field] ?? [];
            $label       = $fieldConfig['label'] ?? ucfirst(str_replace('_', ' ', $field));

            if (is_bool($value)) {
                $value = $value ? 'Yes' : 'No';
            } elseif (is_array($value)) {
                $value = implode(', ', $value);
            }

            $message .= "{$label}: {$value}\n";
        }

        $message .= "\nSubmitted at: " . now()->format('Y-m-d H:i:s');
        $message .= "\nIP Address: " . request()->ip();

        // Send email (you can implement proper mail class here)
        try {
            \Mail::raw($message, function ($mail) use ($subject, $data) {
                $mail->to($this->formConfig['email_to'])
                    ->subject($subject)
                    ->replyTo($data['email'] ?? 'noreply@company.com', $data['name'] ?? 'Test Drive Inquiry');
            });
        } catch (\Exception $e) {
            \Log::warning('Failed to send email notification: ' . $e->getMessage());
        }
    }

    /**
     * Reset the form
     */
    public function resetForm(): void
    {
        $this->submitted      = false;
        $this->successMessage = '';
        $this->initializeFormData();
        $this->resetValidation();
    }

    public function render(): object
    {
        return themed_view('livewire.dynamic-form');
    }
}
