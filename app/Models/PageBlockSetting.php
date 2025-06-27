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

    /**
     * Check if block value's schema_values is dirty and create new block value if needed
     */
    public function checkAndCreateNewBlockValue(): void
    {
        // Only proceed if we have a block value and it's dirty
        if (! $this->blockValue || ! $this->blockValue->isDirty('schema_values')) {
            return;
        }

        $data = self::where('block_value_id', $this->block_value_id)->whereNot('id', $this->id)->first();
        if (! $data) {
            $this->blockValue->save();

            return;
        }

        // Create a new block value with the updated schema_values
        $newBlockValue = PageBlockValue::create([
            'id'            => PageBlockValue::getFirstMissingId(),
            'block_id'      => $this->block_id,
            'template'      => $this->blockValue->template,
            'scripts'       => $this->blockValue->scripts,
            'stylesheets'   => $this->blockValue->stylesheets,
            'styles'        => $this->blockValue->styles,
            'schema_values' => $this->blockValue->schema_values,
        ]);

        // Update this setting to use the new block value
        $this->block_value_id = $newBlockValue->id;
    }

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

        $schemaValues = $this->blockValue?->schema_values;
        if (is_string($schemaValues)) {
            return json_decode($schemaValues, true) ?? [];
        }

        // Otherwise, return the block's schema_values (for global scope)
        $blockSchemaValues = $this->block?->schema_values;
        if (is_string($blockSchemaValues)) {
            return json_decode($blockSchemaValues, true) ?? [];
        }

        return $blockSchemaValues ?? [];
    }
}
