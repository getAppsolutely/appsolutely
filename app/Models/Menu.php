<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\ScopeReference;
use App\Models\Traits\ScopeStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Menu extends Model
{
    use ScopeReference;
    use ScopeStatus;

    protected $fillable = [
        'title',
        'reference',
        'remark',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        self::bootScopeReference();
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('left');
    }
}
