<?php

namespace App\Models;

use App\Models\Traits\ScopeStatus;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Dcat\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ArticleCategory extends NestedSetModel
{
    use HasDateTimeFormatter;
    use ModelTree;
    use ScopeStatus;
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // If slug is empty, generate from title
            if (empty($model->slug) && ! empty($model->title)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_category_pivots',
            'article_category_id', 'article_id');
    }

    public function filesOfType(string $type)
    {
        return $this->morphToMany(File::class, 'assessable')
            ->wherePivot('type', $type)
            ->withTimestamps()
            ->withPivot('type');
    }
}
