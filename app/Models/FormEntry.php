<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class FormEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'form_id',
        'submitted_at',
        'user_id',
        'name',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'data',
        'is_spam',
        'referer',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'data'         => 'array',
        'is_spam'      => 'boolean',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->getUserName();
    }

    /**
     * Get user name - uses name column if available, otherwise falls back to first_name + last_name
     */
    public function getUserName(): string
    {
        if (! empty($this->name)) {
            return trim($this->name);
        }

        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    /**
     * Get a specific field value from data
     */
    public function getFieldValue(string $fieldName): mixed
    {
        return $this->data[$fieldName] ?? null;
    }

    /**
     * Set a specific field value in data
     */
    public function setFieldValue(string $fieldName, mixed $value): void
    {
        $data             = $this->data ?? [];
        $data[$fieldName] = $value;
        $this->data       = $data;
    }

    /**
     * Mark as spam
     */
    public function markAsSpam(): void
    {
        $this->update(['is_spam' => true]);
    }

    /**
     * Mark as not spam
     */
    public function markAsNotSpam(): void
    {
        $this->update(['is_spam' => false]);
    }

    /**
     * Check if entry is valid (not spam)
     */
    public function getIsValidAttribute(): bool
    {
        return ! $this->is_spam;
    }

    /**
     * Get formatted data for display
     */
    public function getFormattedDataAttribute(): array
    {
        $formatted  = [];
        $formFields = $this->form->fields;

        foreach ($formFields as $field) {
            $value = $this->getFieldValue($field->name);

            if ($value !== null) {
                $formatted[$field->label] = $this->formatFieldValue($field, $value);
            }
        }

        return $formatted;
    }

    /**
     * Format field value based on field type
     */
    private function formatFieldValue(FormField $field, mixed $value): string
    {
        switch ($field->type) {
            case 'checkbox':
            case 'multiple_select':
                if (is_array($value)) {
                    return implode(', ', $value);
                }

                return (string) $value;
            case 'file':
                if (is_array($value)) {
                    return implode(', ', array_map(fn ($file) => $file['name'] ?? $file, $value));
                }

                return is_string($value) ? $value : '';
            default:
                return (string) $value;
        }
    }
}
