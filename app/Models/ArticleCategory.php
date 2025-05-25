<?php

namespace App\Models;

use App\Models\Traits\HasFilesOfType;
use App\Models\Traits\ScopeStatus;
use App\Models\Traits\Sluggable;
use Dcat\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleCategory extends NestedSetModel
{
    use HasFilesOfType;
    use ModelTree;
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
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_category_pivot');
    }
}
