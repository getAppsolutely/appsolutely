<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasMissingIds;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PageBlockValue extends Model
{
    use HasMissingIds;

    protected $fillable = [
        'block_id',
        'template',
        'scripts',
        'stylesheets',
        'styles',
        'schema_values',
    ];

    protected $casts = [
        'styles'        => 'array',
        'schema_values' => 'array',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(PageBlock::class, 'block_id');
    }
}
