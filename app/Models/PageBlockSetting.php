<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Status;
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
        'published_at',
        'expired_at',
    ];

    protected $casts = [
        'styles'           => 'array',
        'parameter_values' => 'array',
        'status'           => Status::class,
        'published_at'     => 'datetime',
        'expired_at'       => 'datetime',
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
