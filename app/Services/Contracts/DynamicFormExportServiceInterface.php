<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use Symfony\Component\HttpFoundation\StreamedResponse;

interface DynamicFormExportServiceInterface
{
    /**
     * Export form entries to CSV
     */
    public function exportFormEntries(int $formId): string;

    /**
     * Export form entries to CSV with custom format (for API usage)
     */
    public function exportFormEntriesForApi(?int $formId = null): StreamedResponse;
}
