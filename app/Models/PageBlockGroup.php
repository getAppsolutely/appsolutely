<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\ScopeStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class PageBlockGroup extends Model
{
    use ScopeStatus;

    protected $fillable = [
        'title',
        'remark',
        'sort',
        'status',
    ];

    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class, 'block_group_id');
    }
}
