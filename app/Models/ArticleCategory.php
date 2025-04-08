<?php

namespace App\Models;

use App\Models\Traits\HasFilesOfType;
use App\Models\Traits\ScopeStatus;
use App\Models\Traits\Sluggable;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Dcat\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleCategory extends NestedSetModel
{
    use HasDateTimeFormatter;
    use HasFilesOfType;
    use ModelTree;
    use ScopeStatus;
    use Sluggable;
    use SoftDeletes;

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_category_pivots',
            'article_category_id', 'article_id');
    }
}
