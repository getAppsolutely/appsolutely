<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\FormEntry;
use Illuminate\Http\Request;

interface DynamicFormSubmissionServiceInterface
{
    /**
     * Submit form entry
     */
    public function submitForm(string $slug, array $data, ?Request $request = null): FormEntry;
}
