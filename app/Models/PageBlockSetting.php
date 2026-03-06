<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BlockScope;
use App\Enums\Status;
use App\Models\Traits\ClearsResponseCache;
use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeReference;
use App\Models\Traits\ScopeStatus;
use App\Services\Contracts\ManifestServiceInterface;
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
        'status'       => Status::class,
        'published_at' => 'datetime',
        'expired_at'   => 'datetime',
    ];

    protected $appends = ['block_display_label', 'display_options_title', 'view_style'];

    /**
     * Check if block value's display_options, query_options, theme, anchor_label, or style is dirty and create new block value if needed
     */
    public function checkAndCreateNewBlockValue(): void
    {
        if (! $this->blockValue || ! $this->blockValue->isDirty(['display_options', 'query_options', 'theme', 'anchor_label', 'view_style'])) {
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
            'theme'           => $this->blockValue->theme,
            'view'            => (string) ($this->blockValue->view ?? ''),
            'view_style'      => $this->blockValue->view_style ?? 'default',
            'anchor_label'    => $this->blockValue->anchor_label,
            'query_options'   => $this->blockValue->query_options,
            'display_options' => $this->blockValue->display_options,
            'scripts'         => $this->blockValue->scripts,
            'styles'          => $this->blockValue->styles,
            'template'        => $this->blockValue->template,
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
            $options = is_array($displayOptions)
                ? $displayOptions
                : (is_string($displayOptions) ? json_decode($displayOptions, true) : []);
            if (is_array($options)) {
                unset($options['anchor_label'], $options['style']);
            }

            return $options ?? [];
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

    /**
     * Get block display label from manifest (blockValue.view → manifest template label),
     * fallback to block.reference or block.title.
     */
    public function getBlockDisplayLabelAttribute(): string
    {
        $view  = $this->blockValue?->view;
        $theme = $this->blockValue?->theme;
        if (! empty($view)) {
            $config = \app(ManifestServiceInterface::class)->getTemplateConfig($view, $theme);
            if (! empty($config['label'])) {
                return $config['label'];
            }
        }

        return $this->block?->reference ?: $this->block?->title ?? '';
    }

    /**
     * Get title from display_options. Returns empty string if not set.
     */
    public function getDisplayOptionsTitleAttribute(): string
    {
        $options = $this->display_options_value;
        $title   = $options['title'] ?? null;

        return $title !== null && $title !== '' ? (string) $title : '';
    }

    /**
     * Get view style from block value column (page_block_values.view_style).
     * Returns 'default' if not set.
     */
    public function getViewStyleAttribute(): string
    {
        $columnStyle = $this->blockValue?->view_style;

        return $columnStyle !== null && $columnStyle !== '' ? (string) $columnStyle : 'default';
    }
}
