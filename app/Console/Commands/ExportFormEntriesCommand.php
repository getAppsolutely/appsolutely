<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\FormExportService;
use Illuminate\Console\Command;

/**
 * Command to export form entries to CSV or Excel formats
 *
 * Each form can have different dynamic fields, and this command generates
 * structured export files with all form entries and their dynamic data.
 *
 * Usage:
 *   php artisan forms:export                             # Export all forms as CSV (default)
 *   php artisan forms:export 1                           # Export single form by ID
 *   php artisan forms:export contact                     # Export single form by slug
 *   php artisan forms:export 1 2 3                       # Export multiple forms by ID
 *   php artisan forms:export contact newsletter          # Export multiple forms by slug
 *   php artisan forms:export 1 contact 3                 # Mix IDs and slugs
 *   php artisan forms:export 1 --format=csv              # Export as CSV (default)
 *   php artisan forms:export contact --format=excel      # Export as Excel
 *   php artisan forms:export 1 --include-spam            # Include spam entries
 */
final class ExportFormEntriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:export
                           {forms?* : Form ID(s) or slug(s) to export (leave empty to export all forms)}
                           {--format=csv : Export format: csv or excel}
                           {--include-spam : Include spam entries in the export}
                           {--include-metadata : Include metadata columns (IP, User Agent, etc.)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export form entries to CSV/Excel file(s) in storage/export directory (defaults to all forms as CSV, excludes metadata)';

    /**
     * Execute the console command.
     */
    public function handle(FormExportService $exportService): int
    {
        $formIdentifiers  = $this->argument('forms');
        $includeSpam      = $this->option('include-spam');
        $includeMetadata  = $this->option('include-metadata');
        $format           = strtolower($this->option('format'));

        // Validate format
        if (! in_array($format, ['csv', 'excel'])) {
            $this->error("❌ Invalid format '{$format}'. Supported formats: csv, excel");

            return Command::FAILURE;
        }

        $formatLabel = strtoupper($format);
        $this->info("📤 Starting form entries {$formatLabel} export...");

        if ($includeSpam) {
            $this->warn('⚠️  Including spam entries in export');
        }

        if (! $includeMetadata) {
            $this->line('ℹ️  Metadata columns excluded (use --include-metadata to include)');
        }

        $this->newLine();

        // If no form identifiers provided, export all forms
        if (empty($formIdentifiers)) {
            return $this->exportAllForms($exportService, $includeSpam, $includeMetadata, $format);
        }

        // Resolve form identifiers (IDs or slugs) to form IDs
        $formIds = $this->resolveFormIdentifiers($formIdentifiers);

        if (empty($formIds)) {
            $this->error('❌ No valid forms found');

            return Command::FAILURE;
        }

        // If single form provided
        if (count($formIds) === 1) {
            return $this->exportSingleForm($exportService, $formIds[0], $includeSpam, $includeMetadata, $format);
        }

        // If multiple forms provided, export as batch
        return $this->exportBatchForms($exportService, $formIds, $includeSpam, $includeMetadata, $format);
    }

    /**
     * Resolve form identifiers (IDs or slugs) to form IDs
     */
    protected function resolveFormIdentifiers(array $identifiers): array
    {
        $formIds = [];

        foreach ($identifiers as $identifier) {
            // Check if identifier is numeric (ID)
            if (is_numeric($identifier)) {
                $form = \App\Models\Form::find((int) $identifier);

                if ($form) {
                    $formIds[] = $form->id;
                    $this->line("   ✓ Resolved ID {$identifier}: {$form->name}");
                } else {
                    $this->warn("   ⚠️  Form ID {$identifier} not found - skipping");
                }
            } else {
                // Treat as slug
                $form = \App\Models\Form::where('slug', $identifier)->first();

                if ($form) {
                    $formIds[] = $form->id;
                    $this->line("   ✓ Resolved slug '{$identifier}': {$form->name} (ID: {$form->id})");
                } else {
                    $this->warn("   ⚠️  Form slug '{$identifier}' not found - skipping");
                }
            }
        }

        if (! empty($formIds)) {
            $this->newLine();
        }

        return $formIds;
    }

    /**
     * Export a single form
     */
    protected function exportSingleForm(FormExportService $exportService, int $formId, bool $includeSpam, bool $includeMetadata, string $format): int
    {
        try {
            $this->info("📋 Exporting form ID: {$formId}");

            $startTime = microtime(true);
            $filepath  = $this->exportFormByFormat($exportService, $formId, $includeSpam, $includeMetadata, $format);
            $endTime   = microtime(true);

            $duration = round(($endTime - $startTime) * 1000, 2);
            $filesize = filesize($filepath);

            $this->newLine();
            $this->info('✅ Export completed successfully!');
            $this->line("   📁 File saved: {$filepath}");
            $this->line('   📦 File size: ' . $this->formatBytes($filesize));
            $this->line("   ⏱️  Duration: {$duration}ms");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Export failed: ' . $e->getMessage());

            if ($this->getOutput()->isVerbose()) {
                $this->error($e->getTraceAsString());
            }

            return Command::FAILURE;
        }
    }

    /**
     * Export batch of specific forms
     */
    protected function exportBatchForms(FormExportService $exportService, array $formIds, bool $includeSpam, bool $includeMetadata, string $format): int
    {
        $this->info('📋 Exporting ' . count($formIds) . ' specific form(s)...');
        $this->newLine();

        $successCount = 0;
        $failCount    = 0;
        $totalSize    = 0;
        $startTime    = microtime(true);

        foreach ($formIds as $formId) {
            try {
                $form = \App\Models\Form::find($formId);

                if (! $form) {
                    $this->error("   ❌ Form ID {$formId}: Not found");
                    $failCount++;

                    continue;
                }

                $this->line("   Exporting: {$form->name} (ID: {$formId})...");

                $filepath  = $this->exportFormByFormat($exportService, $formId, $includeSpam, $includeMetadata, $format);
                $filesize  = filesize($filepath);
                $totalSize += $filesize;

                $this->info("   ✅ {$form->name}: " . $this->formatBytes($filesize));
                $successCount++;
            } catch (\Exception $e) {
                $this->error("   ❌ Form ID {$formId}: " . $e->getMessage());
                $failCount++;
            }
        }

        $endTime  = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);

        $this->newLine();
        $this->info('📊 Export Summary:');
        $this->line("   ✅ Successful: {$successCount}");

        if ($failCount > 0) {
            $this->line("   ❌ Failed: {$failCount}");
        }

        $this->line('   📦 Total size: ' . $this->formatBytes($totalSize));
        $this->line("   ⏱️  Total duration: {$duration}ms");

        return $failCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Export all forms
     */
    protected function exportAllForms(FormExportService $exportService, bool $includeSpam, bool $includeMetadata, string $format): int
    {
        $this->info('📋 Exporting all forms...');

        // Get all form IDs
        $formIds = \App\Models\Form::pluck('id', 'name')->toArray();

        if (empty($formIds)) {
            $this->warn('⚠️  No forms found in the database');

            return Command::SUCCESS;
        }

        $this->line('   Found ' . count($formIds) . ' form(s) to export');
        $this->newLine();

        $successCount = 0;
        $failCount    = 0;
        $totalSize    = 0;
        $startTime    = microtime(true);

        foreach ($formIds as $formName => $formId) {
            try {
                $this->line("   Exporting: {$formName} (ID: {$formId})...");

                $filepath  = $this->exportFormByFormat($exportService, $formId, $includeSpam, $includeMetadata, $format);
                $filesize  = filesize($filepath);
                $totalSize += $filesize;

                $this->info("   ✅ {$formName}: " . $this->formatBytes($filesize));
                $successCount++;
            } catch (\Exception $e) {
                $this->error("   ❌ {$formName}: " . $e->getMessage());
                $failCount++;
            }
        }

        $endTime  = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);

        $this->newLine();
        $this->info('📊 Export Summary:');
        $this->line("   ✅ Successful: {$successCount}");

        if ($failCount > 0) {
            $this->line("   ❌ Failed: {$failCount}");
        }

        $this->line('   📦 Total size: ' . $this->formatBytes($totalSize));
        $this->line("   ⏱️  Total duration: {$duration}ms");

        return $failCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Export form by specified format
     */
    protected function exportFormByFormat(FormExportService $exportService, int $formId, bool $includeSpam, bool $includeMetadata, string $format): string
    {
        return match ($format) {
            'csv'   => $exportService->exportFormEntriesToCsv($formId, $includeSpam, $includeMetadata),
            'excel' => $exportService->exportFormEntriesToExcel($formId, $includeSpam, $includeMetadata),
            default => throw new \InvalidArgumentException("Unsupported format: {$format}"),
        };
    }

    /**
     * Format bytes to human-readable format
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i     = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
