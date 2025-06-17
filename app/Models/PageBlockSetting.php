<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PageBlockSetting extends Model
{
    protected $fillable = [
        'page_id',
        'block_id',
        'reference',
        'type',
        'remark',
        'template',
        'scripts',
        'stylesheets',
        'styles',
        'parameter_values',
        'sort',
        'status',
    ];

    protected $casts = [
        'styles'           => 'array',
        'parameter_values' => 'array',
        'status'           => Status::class,
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(PageBlock::class, 'block_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }
}
