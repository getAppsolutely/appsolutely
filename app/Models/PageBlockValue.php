<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\ClearsResponseCache;
use App\Models\Traits\HasMissingIds;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PageBlockValue extends Model
{
    use ClearsResponseCache;
    use HasMissingIds;

    protected $fillable = [
        'block_id',
        'template',
        'scripts',
        'stylesheets',
        'styles',
        'query_options',
        'display_options',
    ];

    protected $casts = [
        'styles'          => 'array',
        'query_options'   => 'array',
        'display_options' => 'array',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(PageBlock::class, 'block_id');
    }
}
