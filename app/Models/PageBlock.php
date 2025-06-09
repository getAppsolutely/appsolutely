<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\ScopeStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Str;

final class PageBlock extends Model
{
    use ScopeStatus;

    protected $fillable = [
        'block_group_id',
        'reference',
        'title',
        'class',
        'remark',
        'description',
        'template',
        'instruction',
        'parameters',
        'setting',
        'sort',
        'status',
    ];

    protected $casts = [
        'parameters' => 'array',
        'setting'    => 'array',
        'sort'       => 'integer',
        'status'     => 'integer',
    ];

    protected static function booted()
    {
        self::creating(function ($model) {
            $model->reference = Str::lower($model->class);
        });
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(PageBlockGroup::class, 'block_group_id');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(PageBlockSetting::class, 'block_id');
    }
}
