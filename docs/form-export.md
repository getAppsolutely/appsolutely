# Form Entries Export (CSV, Excel)

Complete guide for exporting form entries to CSV or Excel formats using the `forms:export` command.

## Table of Contents

- [Overview](#overview)
- [Quick Start](#quick-start)
- [Command Usage](#command-usage)
- [Examples](#examples)
- [Format Comparison](#format-comparison)
- [Export Structure](#export-structure)
- [Key Features](#key-features)
- [Architecture & Implementation](#architecture--implementation)
- [Use Cases](#use-cases)
- [Security](#security)
- [Performance](#performance)
- [Scheduling](#scheduling)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)
- [Advanced Features](#advanced-features)

---

## Overview

The form export system allows you to export form entries with their dynamic fields to structured files. Each form can have different fields, and the export automatically adapts to include all form-specific data.

**Supported Formats:**

- **CSV** (default): Comma-separated values for spreadsheet applications
- **Excel**: Tab-delimited format compatible with Microsoft Excel

**Key Capabilities:**

- Export by form ID or slug
- Batch export multiple forms
- Mix IDs and slugs in a single command
- Spam filtering (excluded by default)
- Dynamic field support
- Complete metadata export

---

## Quick Start

```bash
# Export all forms as CSV (default, excludes metadata)
php artisan forms:export

# Export specific form
php artisan forms:export contact

# Export as Excel
php artisan forms:export contact --format=excel

# Export multiple forms
php artisan forms:export 1 contact newsletter

# Include spam entries
php artisan forms:export contact --include-spam

# Include metadata columns (IP, User Agent, etc.)
php artisan forms:export contact --include-metadata
```

**Output Location:** `storage/app/export/`

**Default Behavior:** Metadata columns (IP Address, User Agent, Referer, User ID, User Name) are excluded by default for cleaner exports and privacy compliance.

---

## Command Usage

### Basic Syntax

```bash
php artisan forms:export [forms...] [--format=csv|excel] [--include-spam] [--include-metadata]
```

### Export All Forms

```bash
php artisan forms:export
```

When no form identifiers are provided, all forms are exported as CSV.

### Export Single Form

You can use either form ID or slug:

```bash
# By ID
php artisan forms:export 1

# By slug
php artisan forms:export contact
```

### Export Multiple Forms (Batch)

Mix IDs and slugs as needed:

```bash
php artisan forms:export 1 2 3
php artisan forms:export contact newsletter support
php artisan forms:export 1 contact 3 newsletter
```

### Format Selection

Use the `--format` option:

```bash
php artisan forms:export 1 --format=csv      # CSV (default)
php artisan forms:export contact --format=excel
```

**Available formats:**

- `csv` (default)
- `excel`

### Include Spam Entries

By default, spam entries are excluded. To include them:

```bash
php artisan forms:export 1 --include-spam
php artisan forms:export contact --format=csv --include-spam
php artisan forms:export --include-spam  # All forms with spam
```

### Include Metadata Columns

By default, metadata columns (IP Address, User Agent, Referer, User ID, User Name) are **excluded** for cleaner exports and privacy compliance. To include them:

```bash
php artisan forms:export 1 --include-metadata
php artisan forms:export contact --format=csv --include-metadata
php artisan forms:export --include-metadata  # All forms with metadata
php artisan forms:export contact --include-spam --include-metadata  # Combine options
```

**Benefits of Excluding Metadata (default):**

- Data privacy compliance (GDPR, CCPA, etc.)
- Cleaner exports for end users
- Reduced file size
- Focus on form data only

---

## Examples

### Example 1: Export Contact Form as CSV

```bash
php artisan forms:export contact
```

**Console Output:**

```
📤 Starting form entries CSV export...
ℹ️  Metadata columns excluded (use --include-metadata to include)

   ✓ Resolved slug 'contact': Contact Form (ID: 1)

📋 Exporting form ID: 1

✅ Export completed successfully!
   📁 File saved: storage/app/export/20251118/form-contact-20251118.csv
   📦 File size: 12.45 KB
   ⏱️  Duration: 45.23ms
```

### Example 2: Export All Forms

```bash
php artisan forms:export
```

**Console Output:**

```
📤 Starting form entries CSV export...
📋 Exporting all forms...
   Found 3 form(s) to export

   Exporting: Contact Form (ID: 1)...
   ✅ Contact Form: 15.23 KB
   Exporting: Newsletter Signup (ID: 2)...
   ✅ Newsletter Signup: 5.67 KB
   Exporting: Support Request (ID: 3)...
   ✅ Support Request: 22.89 KB

📊 Export Summary:
   ✅ Successful: 3
   📦 Total size: 43.79 KB
   ⏱️  Total duration: 198.45ms
```

### Example 3: Batch Export as Excel

```bash
php artisan forms:export 1 newsletter support --format=excel
```

**Console Output:**

```
📤 Starting form entries EXCEL export...

   ✓ Resolved ID 1: Contact Form
   ✓ Resolved slug 'newsletter': Newsletter Signup (ID: 2)
   ✓ Resolved slug 'support': Support Request (ID: 3)

📋 Exporting 3 specific form(s)...

   Exporting: Contact Form (ID: 1)...
   ✅ Contact Form: 12.45 KB
   [...]

📊 Export Summary:
   ✅ Successful: 3
   📦 Total size: 41.01 KB
   ⏱️  Total duration: 156.78ms
```

---

## Format Comparison

### CSV Format

**Best for:** Spreadsheet import, data analysis, quick viewing

**Features:**

- Universal spreadsheet compatibility
- Opens in Excel, Google Sheets, Numbers, etc.
- Flat structure with one row per entry
- Array values concatenated with commas
- Includes all standard and dynamic fields

**File Extension:** `.csv`

**Example:**

```csv
ID,Submitted At,First Name,Last Name,Email,Mobile,Is Spam,Subject,Message
1,2025-11-17 14:30:00,John,Doe,john@example.com,+123456789,No,General Inquiry,Hello!
```

### Excel Format

**Best for:** Direct opening in Microsoft Excel

**Features:**

- Tab-delimited format
- Opens directly in Excel
- Compatible with Excel 2007+
- Similar structure to CSV but Excel-optimized

**File Extension:** `.xlsx`

---

## Export Structure

### File Naming Convention

Exports are organized in date-based folders:

- Directory: `storage/app/export/{YYYYMMDD}/`
- CSV: `form-{slug}-{YYYYMMDD}.csv`
- Excel: `form-{slug}-{YYYYMMDD}.xlsx`

**Examples:**

- `storage/app/export/20251118/form-contact-20251118.csv`
- `storage/app/export/20251118/form-newsletter-20251118.xlsx`

**Benefits:**

- Clean organization by export date
- Human-readable form slugs instead of IDs
- Easy to find exports from specific dates
- Automatic cleanup by removing old date folders

**Note:** If you export the same form multiple times in one day, the later export will overwrite the earlier one. This ensures you always have the latest export and prevents accumulation of redundant files.

### Column Structure

The CSV and Excel exports include the following columns:

**Standard Columns:**

- ID
- Submitted At (YYYY-MM-DD HH:MM:SS format)
- First Name
- Last Name
- Email
- Mobile
- Is Spam (Yes/No)

**Dynamic Columns:**

- One column for each form field (field label as header)
- Order follows form field sorting
- Array values concatenated with commas
- **Note:** Form fields matching standard field names (first_name, last_name, email, mobile) are automatically merged to avoid duplicates

**Metadata Columns (excluded by default):**

- IP Address
- User Agent
- Referer
- User ID
- User Name

**Note:** Use `--include-metadata` flag to include these columns in the export.

**Data Priority:**
For standard fields, the export prioritizes data from the form's `data` JSON column first, then falls back to the dedicated column value if not found. This ensures no duplicate columns appear in the export.

**Example Row (without metadata - default):**

```csv
1,2025-11-17 14:30:00,John,Doe,john@example.com,+123456789,No,General Inquiry,Hello!,"Technology, Design"
```

**Example Row (with --include-metadata flag):**

```csv
1,2025-11-17 14:30:00,John,Doe,john@example.com,+123456789,No,General Inquiry,Hello!,"Technology, Design",192.168.1.1,Mozilla/5.0...,https://example.com/contact,5,John Doe
```

---

## Key Features

### 1. Dynamic Field Support

Each form can have different fields. The export automatically adapts to include all form-specific fields without hardcoding.

**Benefits:**

- No configuration needed
- Works with any form structure
- Automatically includes new fields
- Maintains field order
- Intelligent de-duplication of standard fields

**Standard Field Merging:**
The export system automatically detects and merges duplicate columns. If a form has fields named `first_name`, `last_name`, `email`, or `mobile` (which also exist as dedicated columns in the database), the export:

1. Shows only one column for each field
2. Prioritizes data from the form's `data` JSON column first
3. Falls back to the dedicated column value if not found in `data`
4. Ensures consistent, duplicate-free exports

### 2. Array Value Handling

Fields with multiple values (checkboxes, multiple select) are properly handled:

- CSV: Values concatenated with commas: `"Technology, Design, Marketing"`
- Excel: Same format, properly quoted

### 3. Smart Form Resolution

The system intelligently resolves form identifiers:

- Numeric values treated as IDs: `1`, `2`, `3`
- String values treated as slugs: `contact`, `newsletter`
- Automatic lookup and validation
- Clear feedback for not-found forms

### 4. Spam Filtering

**Default behavior:** Spam entries excluded

**Override:** Use `--include-spam` flag

**Benefits:**

- Cleaner exports by default
- Optional inclusion for audit purposes
- Configurable in `config/forms.php`

### 5. Batch Export

Export multiple forms in one command:

```bash
php artisan forms:export 1 2 3 --format=excel
```

**Features:**

- Progress indicators
- Individual error handling
- Summary statistics
- All exports in single operation

### 6. Privacy-First Defaults

**Metadata Excluded by Default:**

- IP Address, User Agent, Referer, User ID, User Name are **not** included by default
- Helps with GDPR, CCPA, and other privacy regulations
- Cleaner, more focused exports
- Use `--include-metadata` when audit trail is needed

```bash
# Default: No metadata
php artisan forms:export contact

# With metadata: Include audit trail
php artisan forms:export contact --include-metadata
```

---

## Architecture & Implementation

### Code Structure

```
Command (CLI Interface)
    ↓
Service (Business Logic)
    ↓
Repository (Data Access)
    ↓
Model (Entity)
```

### File Locations

| Component            | File Path                                               | Purpose                 |
| -------------------- | ------------------------------------------------------- | ----------------------- |
| **Service**          | `app/Services/FormExportService.php`                    | Export generation logic |
| **Interface**        | `app/Services/Contracts/FormExportServiceInterface.php` | Service contract        |
| **Command**          | `app/Console/Commands/ExportFormEntriesCommand.php`     | CLI interface           |
| **Provider**         | `app/Providers/AppServiceProvider.php`                  | Service registration    |
| **Export Directory** | `storage/app/export/`                                   | Output location         |
| **Documentation**    | `docs/form-export.md`                                   | This file               |
| **Quick Reference**  | `docs/quick-reference.md`                               | Command shortcuts       |

### Follows Project Conventions

✅ **Repository Pattern** - Data access abstraction  
✅ **Service Layer** - Business logic separation  
✅ **Interface Contracts** - Dependency injection  
✅ **Final, readonly classes** - Immutability  
✅ **Strict typing** - `declare(strict_types=1);`  
✅ **PSR-12 compliant** - Code standards  
✅ **Dependency injection** - Constructor injection  
✅ **Service provider registration** - Laravel DI container

### Dependencies

- **FormRepository**: Retrieves form data and field definitions
- **FormEntryRepository**: Fetches form entries with relationships
- **Storage Facade**: Handles file system operations
- **Laravel Collections**: Data manipulation

### Service Interface

```php
interface FormExportServiceInterface
{
    public function exportFormEntriesToCsv(int $formId, bool $includeSpam = false): string;
    public function exportFormEntriesToExcel(int $formId, bool $includeSpam = false): string;
}
```

---

## Use Cases

### 1. Data Backup

Regular exports for backup purposes

```php
$schedule->command('forms:export')->daily()->at('02:00');
```

### 2. Data Migration

Export data for migration to other systems

```bash
php artisan forms:export --format=csv
```

### 3. Compliance (GDPR)

Export user data for compliance requests

```bash
php artisan forms:export contact --include-spam
```

### 4. Data Analysis

Export for external analysis tools

```bash
php artisan forms:export --format=csv
# Open in Excel, Tableau, etc.
```

### 5. Reporting

Import into spreadsheet for reports

```bash
php artisan forms:export 1 2 3 --format=excel
```

### 6. Archival

Long-term storage in CSV format

```bash
php artisan forms:export --format=csv
```

---

## Security

### File Storage

- ✅ Files saved outside public directory (`storage/app/`)
- ✅ Not web-accessible by default
- ✅ Protected by Laravel's storage permissions

### Access Control

- ✅ Command-line only (not web accessible)
- ✅ Requires server access or artisan permissions
- ✅ Can be restricted via server user permissions

### Data Handling

- ✅ No sensitive authentication data exported
- ✅ Spam filtering enabled by default
- ✅ IP addresses and user agents included for audit
- ✅ Configurable data inclusion

### Best Practices

1. Regularly clean up old exports
2. Restrict file system permissions
3. Exclude exports from version control
4. Use secure file transfer methods
5. Encrypt sensitive exports if needed

---

## Performance

### Optimized Generation

- ✅ Memory-efficient CSV generation using `fopen('php://temp')`
- ✅ Streaming support for large datasets
- ✅ No full dataset loading into memory
- ✅ Progressive file writing

### Configurable Limits

```php
// config/forms.php
'export' => [
    'max_entries' => 10000,  // Limit per export
    'include_spam' => false,  // Default spam inclusion
],
```

### Progress Indicators

- Real-time console output
- Per-form progress in batch mode
- File size reporting
- Duration tracking
- Success/failure counts

### Large Dataset Handling

For forms with thousands of entries:

1. Exports process entries in batches
2. File I/O optimized for performance
3. Memory usage remains constant
4. No timeouts for large exports

---

## Scheduling

### Automated Exports

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    // Daily backup of all forms at 2 AM
    $schedule->command('forms:export')
        ->daily()
        ->at('02:00')
        ->appendOutputTo(storage_path('logs/exports.log'));

    // Weekly CSV export for reporting
    $schedule->command('forms:export contact --format=csv')
        ->weekly()
        ->sundays()
        ->at('03:00');

    // Daily Excel export for business users
    $schedule->command('forms:export 1 newsletter support --format=excel')
        ->dailyAt('04:00');

    // Monthly full export with spam
    $schedule->command('forms:export --include-spam')
        ->monthly()
        ->at('01:00');
}
```

### Email Notifications

Notify on completion:

```php
$schedule->command('forms:export')
    ->daily()
    ->emailOutputOnFailure('admin@example.com');
```

### Cleanup Old Exports

```php
$schedule->command('forms:cleanup-exports --older-than=30')
    ->weekly();
```

---

## Testing

### Manual Testing

1. **Verify command exists:**

    ```bash
    php artisan list forms:
    ```

2. **View command help:**

    ```bash
    php artisan forms:export --help
    ```

3. **Test CSV export:**

    ```bash
    php artisan forms:export 1 --format=csv
    ```

4. **Test Excel export:**

    ```bash
    php artisan forms:export 1 --format=excel
    ```

5. **Test single form by ID:**

    ```bash
    php artisan forms:export 1
    ```

6. **Test single form by slug:**

    ```bash
    php artisan forms:export contact
    ```

7. **Test batch export:**

    ```bash
    php artisan forms:export 1 newsletter support
    ```

8. **Verify output:**

    ```bash
    ls -lh storage/app/export/
    ```

9. **Open exports:**
    ```bash
    open storage/app/export/form-1-entries-*.csv
    open storage/app/export/form-1-entries-*.xlsx
    ```

### Automated Testing

```php
// tests/Feature/FormExportTest.php
public function test_can_export_form_as_csv()
{
    $this->artisan('forms:export', ['forms' => [1], '--format' => 'csv'])
         ->assertExitCode(0);

    $this->assertFileExists(storage_path('app/export/form-1-entries-*.csv'));
}
```

---

## Troubleshooting

### Command Not Found

**Problem:** Command doesn't appear in artisan list

**Solution:**

```bash
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear
```

### Permission Denied

**Problem:** Cannot write to export directory

**Solution:**

```bash
chmod -R 775 storage/app/export
chown -R www-data:www-data storage/app/export
```

### Empty Export Files

**Problem:** Export file created but contains no data

**Possible Causes:**

1. Form has no entries
2. All entries marked as spam
3. Database connection issue

**Solution:**

```bash
# Check if form has entries
php artisan tinker
>>> App\Models\Form::find(1)->entries()->count()

# Include spam to see all entries
php artisan forms:export 1 --include-spam
```

### Excel File Won't Open

**Problem:** Excel export file won't open properly

**Solution:**

1. Try CSV format instead: `--format=csv`
2. Install PhpSpreadsheet for native XLSX (see Advanced)
3. Open with text editor to verify content
4. Try different Excel version or alternative app

### Memory Issues

**Problem:** Export fails with memory error

**Solution:**

```php
// config/forms.php
'export' => [
    'max_entries' => 5000,  // Reduce from default 10000
],
```

Or increase PHP memory limit:

```bash
php -d memory_limit=512M artisan forms:export
```

### Form Not Found

**Problem:** "Form slug 'xyz' not found"

**Solution:**

1. Verify form exists: `php artisan tinker >>> App\Models\Form::all()`
2. Check slug spelling
3. Try using form ID instead

---

## Advanced Features

### Native XLSX Support with PhpSpreadsheet

For richer Excel exports with formatting, formulas, and multiple sheets:

**1. Install PhpSpreadsheet:**

```bash
composer require phpoffice/phpspreadsheet
```

**2. Update the service:**

Modify `generateExcel()` in `app/Services/FormExportService.php`:

```php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

protected function generateExcel(Form $form, $entries): string
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Add headers
    $headers = ['ID', 'Submitted At', 'First Name', ...];
    $sheet->fromArray($headers, null, 'A1');

    // Style headers
    $sheet->getStyle('A1:Z1')->getFont()->setBold(true);

    // Add data rows
    $row = 2;
    foreach ($entries as $entry) {
        $sheet->fromArray($rowData, null, "A{$row}");
        $row++;
    }

    // Auto-size columns
    foreach (range('A', 'Z') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Save to temp file
    $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFile);

    return file_get_contents($tempFile);
}
```

**Benefits:**

- Native XLSX format
- Cell formatting
- Multiple sheets
- Formulas and charts
- Better Excel compatibility

### Custom Export Formats

Extend the service to add new formats:

```php
// app/Services/FormExportService.php

public function exportFormEntriesToJson(int $formId, bool $includeSpam = false): string
{
    $form = $this->formRepository->find($formId);
    $entries = $this->entryRepository->getEntriesByForm($formId, $includeSpam);

    $json = json_encode([
        'form' => $form->only(['id', 'name', 'slug']),
        'entries' => $entries->toArray(),
        'exported_at' => now()->toIso8601String(),
    ], JSON_PRETTY_PRINT);

    $filename = $this->generateFilename($formId, 'json');
    $filepath = "export/{$filename}";

    Storage::put($filepath, $json);

    return Storage::path($filepath);
}
```

### Date Range Filtering

Add date filtering to exports:

```bash
php artisan forms:export contact --from=2025-01-01 --to=2025-12-31
```

### Compressed Exports

For large datasets, add ZIP compression:

```php
public function exportFormEntriesToCsvZipped(int $formId): string
{
    $csvPath = $this->exportFormEntriesToCsv($formId);
    $zipPath = str_replace('.csv', '.zip', $csvPath);

    $zip = new ZipArchive();
    $zip->open($zipPath, ZipArchive::CREATE);
    $zip->addFile($csvPath, basename($csvPath));
    $zip->close();

    unlink($csvPath); // Remove uncompressed file

    return $zipPath;
}
```

---

## Related Documentation

- [Quick Reference Guide](quick-reference.md) - Command shortcuts and common tasks
- [Form Management](../README.md) - Overview of form system

---

## Support

For issues or questions:

1. Check this documentation
2. Review troubleshooting section
3. Check Laravel logs: `storage/logs/laravel.log`
4. Run with verbose output: `php artisan forms:export -vvv`

---

**Last Updated:** 2025-11-17  
**Version:** 2.0 (CSV/Excel only)
