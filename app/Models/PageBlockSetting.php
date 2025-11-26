<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BlockScope;
use App\Models\Traits\ClearsResponseCache;
use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeReference;
use App\Models\Traits\ScopeStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PageBlockSetting extends Model
{
    use ClearsResponseCache;
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
        'status'       => 'integer',
        'published_at' => 'datetime',
        'expired_at'   => 'datetime',
    ];

    protected $appends = [];

    /**
     * Check if block value's display_options and query_options is dirty and create new block value if needed
     */
    public function checkAndCreateNewBlockValue(): void
    {
        // Only proceed if we have a block value and it's dirty
        if (! $this->blockValue || ! $this->blockValue->isDirty(['display_options', 'query_options'])) {
            return;
        }

        $data = self::where('block_value_id', $this->block_value_id)->whereNot('id', $this->id)->first();
        if (empty($data) || $this->block->scope == BlockScope::Global->value) {
            $this->blockValue->save();

            return;
        }

        // Create a new block value with the updated display_options and query_options
        $newBlockValue = PageBlockValue::create([
            'id'              => PageBlockValue::getFirstMissingId(),
            'block_id'        => $this->block_id,
            'template'        => $this->blockValue->template,
            'scripts'         => $this->blockValue->scripts,
            'stylesheets'     => $this->blockValue->stylesheets,
            'styles'          => $this->blockValue->styles,
            'display_options' => $this->blockValue->display_options,
            'query_options'   => $this->blockValue->query_options,
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
     * Returns display_options if block scope is 'page', otherwise returns block's display_options.
     */
    public function getDisplayOptionsValueAttribute(): array
    {
        // Load the block relationship if not already loaded
        if (! $this->relationLoaded('block')) {
            $this->load('block');
        }

        $displayOptions = $this->blockValue?->display_options;
        if (! empty($displayOptions)) {
            return is_array($displayOptions)
                ? $displayOptions
                : (is_string($displayOptions) ? json_decode($displayOptions, true) : []);
        }

        // Otherwise, return the block's schema_values (for global scope)
        $blockSchemaValues = $this->block?->schema_values;
        if (is_string($blockSchemaValues)) {
            return json_decode($blockSchemaValues, true) ?? [];
        }

        return $blockSchemaValues ?? [];
    }

    public function getQueryOptionsValueAttribute(): array
    {
        // Load the block relationship if not already loaded
        if (! $this->relationLoaded('block')) {
            $this->load('block');
        }

        $queryOptions = $this->blockValue?->query_options;
        if (empty($queryOptions) || (! is_array($queryOptions) && ! is_string($queryOptions))) {
            return [];
        }

        return is_string($queryOptions) ? json_decode($queryOptions, true) : $queryOptions;

    }
}
