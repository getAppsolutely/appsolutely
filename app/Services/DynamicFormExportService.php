<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Form;
use App\Repositories\FormEntryRepository;
use App\Repositories\FormRepository;
use App\Services\Contracts\DynamicFormExportServiceInterface;

final class DynamicFormExportService implements DynamicFormExportServiceInterface
{
    public function __construct(
        protected FormRepository $formRepository,
        protected FormEntryRepository $entryRepository
    ) {}

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
     * Export form entries to CSV with custom format (for API usage)
     */
    public function exportFormEntriesForApi(?int $formId = null): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $entries  = $this->entryRepository->getValidEntriesForExport($formId);
        $filename = 'form-entries-' . date('Y-m-d-H-i-s') . '.csv';

        return response()->streamDownload(function () use ($entries) {
            $handle = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($handle, [
                'ID',
                'Form',
                'First Name',
                'Last Name',
                'Email',
                'Mobile',
                'User',
                'Form Data',
                'Submitted At',
                'IP Address',
            ]);

            // Add data rows
            foreach ($entries as $entry) {
                fputcsv($handle, [
                    $entry->id,
                    $entry->form->name,
                    $entry->first_name,
                    $entry->last_name,
                    $entry->email,
                    $entry->mobile,
                    $entry->user ? $entry->user->name : 'Guest',
                    json_encode($entry->data),
                    $entry->submitted_at->format('Y-m-d H:i:s'),
                    $entry->ip_address,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
