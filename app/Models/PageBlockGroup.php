<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

final class PageBlockGroup extends Model
{
    protected $fillable = [
        'title',
        'status',
        'remark',
    ];

    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class, 'block_group_id');
    }
}
