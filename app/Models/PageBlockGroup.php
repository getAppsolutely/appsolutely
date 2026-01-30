<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Status;
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

    protected $casts = [
        'status' => Status::class,
    ];

    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class, 'block_group_id');
    }
}
