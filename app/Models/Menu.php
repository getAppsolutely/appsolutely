<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MenuTarget;
use App\Enums\MenuType;
use App\Enums\Status;
use App\Models\Traits\ClearsResponseCache;
use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeReference;
use App\Models\Traits\ScopeStatus;
use Dcat\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

final class Menu extends NestedSetModel implements Sortable
{
    use ClearsResponseCache;
    use ModelTree;
    use ScopePublished;
    use ScopeReference;
    use ScopeStatus;
    // use SortableTrait;

    protected $fillable = [
        'parent_id',
        'title',
        'reference',
        'remark',
        'url',
        'type',
        'icon',
        'thumbnail',
        'setting',
        'permission_key',
        'target',
        'is_external',
        'published_at',
        'expired_at',
        'status',
    ];

    protected $casts = [
        'parent_id'    => 'integer',
        'is_external'  => 'boolean',
        'type'         => MenuType::class,
        'target'       => MenuTarget::class,
        'setting'      => 'array',
        'published_at' => 'datetime',
        'expired_at'   => 'datetime',
        'status'       => Status::class,
    ];

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('left', 'asc')->with('children');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }
}
