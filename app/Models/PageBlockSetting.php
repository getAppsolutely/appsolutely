<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeReference;
use App\Models\Traits\ScopeStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PageBlockSetting extends Model
{
    use ScopePublished, ScopeReference, ScopeStatus;

    protected $fillable = [
        'page_id',
        'block_id',
        'block_value_id',
        'reference',
        'type',
        'remark',
        'sort',
        'status',
        'published_at',
        'expired_at',
    ];

    protected $casts = [
        'styles'        => 'array',
        'schema_values' => 'array',
        'status'        => 'integer',
        'published_at'  => 'datetime',
        'expired_at'    => 'datetime',
    ];

    protected $appends = [
        'parameters',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(PageBlock::class, 'block_id');
    }

    public function blockValue(): BelongsTo
    {
        return $this->belongsTo(PageBlockValue::class, 'block_value_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    /**
     * Get the parameters for this block setting.
     * Returns schema_values if block scope is 'page', otherwise returns block's schema_values.
     */
    public function getParametersAttribute(): array
    {
        // Load the block relationship if not already loaded
        if (! $this->relationLoaded('block')) {
            $this->load('block');
        }

        // If block scope is 'page', return this setting's schema_values
        if ($this->block && $this->block->scope === 'page') {
            $schemaValues = $this->blockValue?->schema_values;
            if (is_string($schemaValues)) {
                return json_decode($schemaValues, true) ?? [];
            }

            return $schemaValues ?? [];
        }

        // Otherwise, return the block's schema_values (for global scope)
        $blockSchemaValues = $this->block?->schema_values;
        if (is_string($blockSchemaValues)) {
            return json_decode($blockSchemaValues, true) ?? [];
        }

        return $blockSchemaValues ?? [];
    }
}
