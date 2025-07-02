<?php

declare(strict_types=1);

namespace App\Admin\Controllers\Api;

use App\Repositories\FormEntryRepository;

final class DynamicFormApiController extends AdminBaseApiController
{
    public function __construct(
        protected FormEntryRepository $entryRepository,
    ) {}

    /**
     * Mark entry as spam
     */
    public function markAsSpam(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->entryRepository->markSingleAsSpam($id);

            return $this->success(null, 'Entry marked as spam successfully');
        } catch (\Exception $e) {
            return $this->failServer('Failed to mark entry as spam');
        }
    }

    /**
     * Mark entry as not spam
     */
    public function markAsNotSpam(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->entryRepository->markSingleAsNotSpam($id);

            return $this->success(null, 'Entry marked as valid successfully');
        } catch (\Exception $e) {
            return $this->failServer('Failed to mark entry as valid');
        }
    }

    /**
     * Export form entries as CSV
     */
    public function exportCsv(?int $formId = null): \Symfony\Component\HttpFoundation\StreamedResponse
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
