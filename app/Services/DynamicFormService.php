<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Models\Form;
use App\Models\FormEntry;
use App\Models\FormField;
use App\Repositories\FormEntryRepository;
use App\Repositories\FormFieldRepository;
use App\Repositories\FormRepository;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Psr\Log\LoggerInterface;

final class DynamicFormService
{
    const FORM_WRAPPER = 'formData';

    public function __construct(
        protected FormRepository $formRepository,
        protected FormFieldRepository $fieldRepository,
        protected FormEntryRepository $entryRepository,
        protected ConnectionInterface $db,
        protected LoggerInterface $logger
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
            throw new NotFoundException("Form with slug '{$slug}' not found");
        }

        // Validate the submission
        $validatedData = $this->validateFormSubmission($form, [self::FORM_WRAPPER => $data])[self::FORM_WRAPPER] ?? null;

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

        // Create the form entry
        $formEntry = $this->entryRepository->createEntryWithSpamCheck($entryData);

        // Insert into target table if specified
        if ($form->target_table) {
            $this->insertIntoTargetTable($form, $validatedData, $formEntry);
        }

        // Trigger notifications
        $this->triggerNotifications($form, $formEntry, $validatedData);

        return $formEntry;
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
                $rules[self::FORM_WRAPPER . ".{$field->name}"] = $fieldRules;
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
            $fieldKey = self::FORM_WRAPPER . ".{$field->name}";
            $label    = $field->label;

            // Get validation rules for this field
            $validationRules = $field->validation_rules ?? [];
            if (empty($validationRules)) {
                continue;
            }

            // Use Laravel's built-in validation message generation
            // Create a temporary validator to get default messages
            $validator = \Illuminate\Support\Facades\Validator::make(
                [$field->name => null], // dummy data
                [$field->name => $validationRules], // rules
                [], // no custom messages
                [$field->name => $label] // attributes
            );

            // Extract the default messages and remap to our field key format
            $defaultMessages = $validator->getMessageBag()->getMessages();

            foreach ($validationRules as $rule) {
                $ruleName = is_string($rule) ? explode(':', $rule)[0] : $rule;

                // Generate the Laravel message key and our custom key
                $laravelKey = "{$field->name}.{$ruleName}";
                $ourKey     = "{$fieldKey}.{$ruleName}";

                // Try to get Laravel's default message for this rule
                $defaultMessage = trans("validation.{$ruleName}", [
                    'attribute' => $label,
                    'value'     => '', // placeholder
                ]);

                // If Laravel has a default message and it's not the translation key, use it
                if ($defaultMessage !== "validation.{$ruleName}") {
                    $messages[$ourKey] = $defaultMessage;
                } else {
                    // Fallback to custom message for field-specific rules
                    $messages[$ourKey] = "The {$label} field is invalid.";
                }
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

    public function getFields(Form $form): array
    {
        $fields = [];
        foreach ($form->fields->sortBy('sort') as $field) {
            $fields[$field->name] = [
                'type'        => $field->type,
                'label'       => $field->label,
                'placeholder' => $field->placeholder,
                'required'    => $field->required,
                'validation'  => $field->validation_rules,
                'options'     => $field->options ?? [],
                'default'     => $field->default_value,
                'rows'        => $field->setting['rows'] ?? 4,
            ];
        }

        return $fields;
    }

    /**
     * Insert form data into target table
     */
    protected function insertIntoTargetTable(Form $form, array $validatedData, FormEntry $formEntry): void
    {
        if (empty($form->target_table)) {
            return;
        }

        // Get table columns to filter data
        $tableColumns = $this->getTableColumns($form->target_table);

        if (empty($tableColumns)) {
            $this->logger->warning("Target table '{$form->target_table}' does not exist or has no columns");

            return;
        }

        // Prepare data for target table
        $targetData = $this->prepareTargetTableData($validatedData, $tableColumns, $formEntry);

        if (empty($targetData)) {
            $this->logger->warning("No matching columns found for target table '{$form->target_table}'");

            return;
        }

        try {
            // Insert into target table using injected connection
            $this->db->table($form->target_table)->insert($targetData);

            $this->logger->info("Successfully inserted form data into target table '{$form->target_table}'", [
                'form_id'       => $form->id,
                'form_entry_id' => $formEntry->id,
                'target_table'  => $form->target_table,
            ]);
        } catch (\Exception $e) {
            $this->logger->error("Failed to insert into target table '{$form->target_table}': " . $e->getMessage(), [
                'form_id'       => $form->id,
                'form_entry_id' => $formEntry->id,
                'error'         => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get table columns for the target table
     */
    protected function getTableColumns(string $tableName): array
    {
        try {
            $columns = $this->db->getSchemaBuilder()->getColumnListing($tableName);

            return $columns;
        } catch (\Exception $e) {
            $this->logger->error("Failed to get columns for table '{$tableName}': " . $e->getMessage());

            return [];
        }
    }

    /**
     * Prepare data for target table by filtering only existing columns
     */
    protected function prepareTargetTableData(array $validatedData, array $tableColumns, FormEntry $formEntry): array
    {
        $targetData = [];

        // Add form entry reference if column exists
        if (in_array('form_entry_id', $tableColumns)) {
            $targetData['form_entry_id'] = $formEntry->id;
        }

        // Add common contact fields if they exist in target table
        $contactFields = [
            'first_name' => $formEntry->first_name,
            'last_name'  => $formEntry->last_name,
            'email'      => $formEntry->email,
            'mobile'     => $formEntry->mobile,
        ];

        foreach ($contactFields as $field => $value) {
            if (in_array($field, $tableColumns) && ! is_null($value)) {
                $targetData[$field] = $value;
            }
        }

        // Add form data fields that match table columns
        foreach ($validatedData as $fieldName => $value) {
            // Remove 'formData.' prefix if present
            $cleanFieldName = str_replace('formData.', '', $fieldName);

            if (in_array($cleanFieldName, $tableColumns)) {
                // Handle different data types
                if (is_array($value)) {
                    // Convert arrays to JSON or comma-separated string based on column type
                    $targetData[$cleanFieldName] = json_encode($value);
                } else {
                    $targetData[$cleanFieldName] = $value;
                }
            }
        }

        // Add timestamps if columns exist
        $now = now();
        if (in_array('created_at', $tableColumns)) {
            $targetData['created_at'] = $now;
        }
        if (in_array('updated_at', $tableColumns)) {
            $targetData['updated_at'] = $now;
        }

        return $targetData;
    }

    /**
     * Trigger notifications for form submission
     */
    protected function triggerNotifications(Form $form, FormEntry $formEntry, array $validatedData): void
    {
        try {
            $notificationService = app(\App\Services\NotificationService::class);

            $notificationData = [
                'form_name'        => $form->name,
                'form_description' => $form->description,
                'user_name'        => trim(($formEntry->first_name ?? '') . ' ' . ($formEntry->last_name ?? '')),
                'user_email'       => $formEntry->email,
                'user_phone'       => $formEntry->mobile,
                'submitted_at'     => $formEntry->created_at->format('Y-m-d H:i:s'),
                'entry_id'         => $formEntry->id,
                'form_data'        => json_encode($validatedData),
                'admin_link'       => url('/admin/dynamic-forms?tab=form-entries&form_id=' . $form->id),
            ];

            $notificationService->trigger('form_submission', $form->slug, $notificationData);
        } catch (\Exception $e) {
            $this->logger->error('Failed to trigger form submission notifications', [
                'form_id'  => $form->id,
                'entry_id' => $formEntry->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
