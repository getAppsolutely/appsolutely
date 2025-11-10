<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class FormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'label',
        'name',
        'type',
        'placeholder',
        'required',
        'options',
        'sort',
        'setting',
    ];

    protected $casts = [
        'required' => 'boolean',
        'options'  => 'array',
        'setting'  => 'array',
        'sort'     => 'integer',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Get default value from settings
     */
    public function getDefaultValueAttribute(): mixed
    {
        return $this->setting['default'] ?? null;
    }

    /**
     * Check if field is readonly
     */
    public function getIsReadonlyAttribute(): bool
    {
        return $this->setting['readonly'] ?? false;
    }

    /**
     * Get validation rules for this field
     */
    public function getValidationRulesAttribute(): array
    {
        $rules = [];

        if ($this->required) {
            $rules[] = 'required';
        }

        // Add type-specific validation
        switch ($this->type) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'number':
                $rules[] = 'numeric';
                if (isset($this->setting['min'])) {
                    $rules[] = 'min:' . $this->setting['min'];
                }
                if (isset($this->setting['max'])) {
                    $rules[] = 'max:' . $this->setting['max'];
                }
                break;
            case 'text':
            case 'textarea':
                if (isset($this->setting['min'])) {
                    $rules[] = 'min:' . $this->setting['min'];
                }
                if (isset($this->setting['max'])) {
                    $rules[] = 'max:' . $this->setting['max'];
                }
                if (isset($this->setting['pattern'])) {
                    $rules[] = 'regex:' . $this->setting['pattern'];
                }
                break;
            case 'select':
            case 'radio':
                // Validate that the value is one of the available options
                $options = $this->field_options;
                if (! empty($options)) {
                    $rules[] = 'in:' . implode(',', $options);
                }
                break;
            case 'checkbox':
                $options = $this->field_options;
                if (empty($options) || count($options) === 1) {
                    // Single checkbox (e.g., "I agree", "I have a valid license")
                    // Treat as boolean or acceptance field
                    if ($this->required) {
                        $rules[] = 'accepted'; // Must be checked if required
                    } else {
                        $rules[] = 'boolean'; // Can be true/false
                    }
                } else {
                    // Multiple checkbox options (checkbox group)
                    $rules[] = 'array';
                    $rules[] = 'in:' . implode(',', $options);
                }
                break;
            case 'multiple_select':
                // For multiple select, validate that each value is in the options
                $rules[] = 'array';
                $options = $this->field_options;
                if (! empty($options)) {
                    $rules[] = 'in:' . implode(',', $options);
                }
                break;
            case 'file':
                $rules[] = 'file';
                if (isset($this->setting['mimes'])) {
                    $rules[] = 'mimes:' . implode(',', $this->setting['mimes']);
                }
                if (isset($this->setting['max_size'])) {
                    $rules[] = 'max:' . $this->setting['max_size'];
                }
                break;
            case 'date':
                $rules[] = 'date';
                if (isset($this->setting['after'])) {
                    $rules[] = 'after:' . $this->setting['after'];
                }
                if (isset($this->setting['before'])) {
                    $rules[] = 'before:' . $this->setting['before'];
                }
                break;
            case 'boolean':
                $rules[] = 'boolean';
                break;
        }

        return $rules;
    }

    /**
     * Check if field type supports multiple values
     */
    public function getSupportsMultipleValuesAttribute(): bool
    {
        return in_array($this->type, ['checkbox', 'multiple_select']);
    }

    /**
     * Get field options for select/radio/checkbox fields
     */
    public function getFieldOptionsAttribute(): array
    {
        if (! in_array($this->type, ['select', 'radio', 'checkbox', 'multiple_select'])) {
            return [];
        }

        return $this->options ?? [];
    }
}
