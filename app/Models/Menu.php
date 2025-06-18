<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Menu extends NestedSetModel
{
    use ScopePublished;
    use ScopeStatus;

    protected $fillable = [
        'parent_id',
        'menu_group_id',
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
        'parent_id'     => 'integer',
        'menu_group_id' => 'integer',
        'is_external'   => 'boolean',
        'published_at'  => 'datetime',
        'expired_at'    => 'datetime',
        'status'        => 'integer',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('left');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function menuGroup(): BelongsTo
    {
        return $this->belongsTo(MenuGroup::class);
    }

    public function scopeByGroup($query, int $groupId)
    {
        return $query->where('menu_group_id', $groupId);
    }
}
