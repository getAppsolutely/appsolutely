<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Form;
use App\Models\FormField;

interface DynamicFormRenderServiceInterface
{
    /**
     * Generate HTML for form rendering
     */
    public function renderForm(Form $form, array $values = [], array $errors = []): string;

    /**
     * Render individual form field
     */
    public function renderField(FormField $field, $value = null, $error = null): string;

    /**
     * Get form fields as array for frontend rendering
     */
    public function getFields(Form $form): array;
}
