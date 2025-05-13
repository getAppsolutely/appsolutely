<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'published_at',
        'expired_at',
        'status',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expired_at'   => 'datetime',
        'status'       => 'integer',
    ];

    public function containers(): HasMany
    {
        return $this->hasMany(PageContainer::class)->orderBy('sort');
    }
}
