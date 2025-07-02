<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Form;
use App\Models\FormEntry;
use App\Models\FormField;
use App\Repositories\FormEntryRepository;
use App\Repositories\FormFieldRepository;
use App\Repositories\FormRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class DynamicFormService
{
    public function __construct(
        protected FormRepository $formRepository,
        protected FormFieldRepository $fieldRepository,
        protected FormEntryRepository $entryRepository
    ) {}

    /**
     * Get form by slug with fields
     */
    public function getFormBySlug(string $slug): ?Form
    {
        return $this->formRepository->findBySlug($slug);
    }

    /**
     * Create a new form with fields
     */
    public function createForm(array $formData, array $fieldsData = []): Form
    {
        if (empty($formData['slug'])) {
            $formData['slug'] = Str::slug($formData['name']);
        }

        $formData['slug'] = $this->ensureUniqueSlug($formData['slug']);

        return $this->formRepository->createWithFields($formData, $fieldsData);
    }

    /**
     * Submit form entry
     */
    public function submitForm(string $slug, array $data, ?Request $request = null): FormEntry
    {
        $form = $this->getFormBySlug($slug);

        if (! $form) {
            throw new \Exception('Form not found');
        }

        // Validate the submission
        $validatedData = $this->validateFormSubmission($form, $data);

        // Prepare entry data
        $entryData = [
            'form_id'    => $form->id,
            'first_name' => $data['first_name'] ?? null,
            'last_name'  => $data['last_name'] ?? null,
            'email'      => $data['email'] ?? null,
            'mobile'     => $data['mobile'] ?? null,
            'data'       => $this->prepareFormData($form, $validatedData),
        ];

        // Add request metadata if available
        if ($request) {
            $entryData['referer']    = $request->header('referer');
            $entryData['ip_address'] = $request->ip();
            $entryData['user_agent'] = $request->header('user-agent');

            if ($request->user()) {
                $entryData['user_id'] = $request->user()->id;
            }
        }

        return $this->entryRepository->createEntryWithSpamCheck($entryData);
    }

    /**
     * Validate form submission
     */
    public function validateFormSubmission(Form $form, array $data): array
    {
        $rules      = $this->getValidationRules($form);
        $messages   = $this->getValidationMessages($form);
        $attributes = $this->getValidationAttributes($form);

        $validator = Validator::make($data, $rules, $messages, $attributes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get validation rules for form
     */
    public function getValidationRules(Form $form): array
    {
        $rules = [];

        foreach ($form->fields as $field) {
            $fieldRules = $field->validation_rules;

            if (! empty($fieldRules)) {
                $rules[$field->name] = $fieldRules;
            }
        }

        return $rules;
    }

    /**
     * Get validation messages for form
     */
    public function getValidationMessages(Form $form): array
    {
        $messages = [];

        foreach ($form->fields as $field) {
            if ($field->required) {
                $messages["{$field->name}.required"] = "The {$field->label} field is required.";
            }
        }

        return $messages;
    }

    /**
     * Get validation attributes for form
     */
    public function getValidationAttributes(Form $form): array
    {
        $attributes = [];

        foreach ($form->fields as $field) {
            $attributes[$field->name] = $field->label;
        }

        return $attributes;
    }

    /**
     * Prepare form data for storage
     */
    protected function prepareFormData(Form $form, array $data): array
    {
        $preparedData = [];

        foreach ($form->fields as $field) {
            $value = $data[$field->name] ?? null;

            // Handle file uploads
            if ($field->type === 'file' && $value) {
                $preparedData[$field->name] = $this->handleFileUpload($value, $field);
            } else {
                $preparedData[$field->name] = $value;
            }
        }

        return $preparedData;
    }

    /**
     * Handle file upload for form field
     */
    protected function handleFileUpload($file, FormField $field): array
    {
        if (is_array($file)) {
            $uploadedFiles = [];
            foreach ($file as $singleFile) {
                $uploadedFiles[] = [
                    'name' => $singleFile->getClientOriginalName(),
                    'path' => $singleFile->store('form-uploads'),
                    'size' => $singleFile->getSize(),
                    'mime' => $singleFile->getMimeType(),
                ];
            }

            return $uploadedFiles;
        }

        return [
            'name' => $file->getClientOriginalName(),
            'path' => $file->store('form-uploads'),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
        ];
    }

    /**
     * Generate HTML for form rendering
     */
    public function renderForm(Form $form, array $values = [], array $errors = []): string
    {
        $html = "<form method='POST' action='' enctype='multipart/form-data'>";
        $html .= csrf_field();

        foreach ($form->fields as $field) {
            $html .= $this->renderField($field, $values[$field->name] ?? null, $errors[$field->name] ?? null);
        }

        $html .= "<button type='submit' class='btn btn-primary'>Submit</button>";
        $html .= '</form>';

        return $html;
    }

    /**
     * Render individual form field
     */
    protected function renderField(FormField $field, $value = null, $error = null): string
    {
        $value    = $value ?? $field->default_value;
        $required = $field->required ? 'required' : '';
        $readonly = $field->is_readonly ? 'readonly' : '';

        $html = "<div class='form-group mb-3'>";
        $html .= "<label for='{$field->name}' class='form-label'>{$field->label}";

        if ($field->required) {
            $html .= " <span class='text-danger'>*</span>";
        }

        $html .= '</label>';

        switch ($field->type) {
            case 'text':
            case 'email':
            case 'number':
                $html .= "<input type='{$field->type}' id='{$field->name}' name='{$field->name}' class='form-control' value='{$value}' placeholder='{$field->placeholder}' {$required} {$readonly}>";
                break;

            case 'textarea':
                $html .= "<textarea id='{$field->name}' name='{$field->name}' class='form-control' placeholder='{$field->placeholder}' {$required} {$readonly}>{$value}</textarea>";
                break;

            case 'select':
                $html .= "<select id='{$field->name}' name='{$field->name}' class='form-control' {$required}>";
                $html .= "<option value=''>Choose...</option>";
                foreach ($field->field_options as $option) {
                    $selected = $value === $option ? 'selected' : '';
                    $html .= "<option value='{$option}' {$selected}>{$option}</option>";
                }
                $html .= '</select>';
                break;

            case 'radio':
                foreach ($field->field_options as $option) {
                    $checked = $value === $option ? 'checked' : '';
                    $html .= "<div class='form-check'>";
                    $html .= "<input type='radio' id='{$field->name}_{$option}' name='{$field->name}' value='{$option}' class='form-check-input' {$checked} {$required}>";
                    $html .= "<label for='{$field->name}_{$option}' class='form-check-label'>{$option}</label>";
                    $html .= '</div>';
                }
                break;

            case 'checkbox':
                foreach ($field->field_options as $option) {
                    $checked = is_array($value) && in_array($option, $value) ? 'checked' : '';
                    $html .= "<div class='form-check'>";
                    $html .= "<input type='checkbox' id='{$field->name}_{$option}' name='{$field->name}[]' value='{$option}' class='form-check-input' {$checked}>";
                    $html .= "<label for='{$field->name}_{$option}' class='form-check-label'>{$option}</label>";
                    $html .= '</div>';
                }
                break;

            case 'file':
                $multiple = $field->supports_multiple_values ? 'multiple' : '';
                $html .= "<input type='file' id='{$field->name}' name='{$field->name}" . ($multiple ? '[]' : '') . "' class='form-control' {$required} {$multiple}>";
                break;
        }

        if ($error) {
            $html .= "<div class='text-danger mt-1'>{$error}</div>";
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Get form statistics
     */
    public function getFormStatistics(int $formId): array
    {
        return $this->entryRepository->getFormStats($formId);
    }

    /**
     * Mark entries as spam
     */
    public function markEntriesAsSpam(array $entryIds): int
    {
        return $this->entryRepository->markAsSpam($entryIds);
    }

    /**
     * Export form entries to CSV
     */
    public function exportFormEntries(int $formId): string
    {
        $form    = $this->formRepository->find($formId);
        $entries = $this->entryRepository->getEntriesByForm($formId, false);

        $output = fopen('php://temp', 'r+');

        // Headers
        $headers = ['ID', 'Submitted At', 'First Name', 'Last Name', 'Email', 'Mobile'];
        foreach ($form->fields as $field) {
            $headers[] = $field->label;
        }
        fputcsv($output, $headers);

        // Data rows
        foreach ($entries as $entry) {
            $row = [
                $entry->id,
                $entry->submitted_at->format('Y-m-d H:i:s'),
                $entry->first_name,
                $entry->last_name,
                $entry->email,
                $entry->mobile,
            ];

            foreach ($form->fields as $field) {
                $value = $entry->getFieldValue($field->name);
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $row[] = $value;
            }

            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Ensure unique slug
     */
    protected function ensureUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $originalSlug = $slug;
        $counter      = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    protected function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Form::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
