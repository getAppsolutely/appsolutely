<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\ScopeStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class MenuGroup extends Model
{
    use ScopeStatus;

    protected $fillable = [
        'title',
        'remark',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class)->orderBy('left');
    }
}
