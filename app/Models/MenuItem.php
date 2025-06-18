<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MenuTarget;
use App\Enums\MenuType;
use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeStatus;
use Dcat\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class MenuItem extends NestedSetModel
{
    use ModelTree;
    use ScopePublished;
    use ScopeStatus;

    protected $fillable = [
        'parent_id',
        'menu_id',
        'title',
        'remark',
        'route',
        'type',
        'icon',
        'permission_key',
        'target',
        'is_external',
        'published_at',
        'expired_at',
        'status',
    ];

    protected $casts = [
        'parent_id'    => 'integer',
        'menu_id'      => 'integer',
        'is_external'  => 'boolean',
        'type'         => MenuType::class,
        'target'       => MenuTarget::class,
        'published_at' => 'datetime',
        'expired_at'   => 'datetime',
        'status'       => 'integer',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('left');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function scopeByMenu($query, int $menuId)
    {
        return $query->where('menu_id', $menuId);
    }
}
