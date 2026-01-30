<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Status;
use App\Models\Traits\HasFilesOfType;
use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeStatus;
use App\Models\Traits\Sluggable;
use Dcat\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends NestedSetModel
{
    use HasFactory;
    use HasFilesOfType;
    use ModelTree;
    use ScopePublished;
    use ScopeStatus;
    use Sluggable;
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'title',
        'keywords',
        'description',
        'slug',
        'cover',
        'status',
        'published_at',
        'expired_at',
        'setting',
    ];

    protected $casts = [
        'setting'      => 'array',
        'published_at' => 'datetime',
        'expired_at'   => 'datetime',
        'status'       => Status::class,
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_category_pivot');
    }
}
