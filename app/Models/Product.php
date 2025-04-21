<?php

namespace App\Models;

use App\Models\Traits\HasFilesOfType;
use App\Models\Traits\HasMarkdownContent;
use App\Models\Traits\Publishable;
use App\Models\Traits\Sluggable;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasDateTimeFormatter;
    use HasFilesOfType;
    use HasMarkdownContent;
    use Publishable;
    use Sluggable;
    use SoftDeletes;

    const TYPE_PHYSICAL_PRODUCT = 'physical';

    const TYPE_AUTO_DELIVERABLE_VIRTUAL_PRODUCT = 'auto_virtual';

    const TYPE_MANUAL_DELIVERABLE_VIRTUAL_PRODUCT = 'manual_virtual';

    protected $fillable = [
        'type',
        'type_config',
        'slug',
        'title',
        'cover',
        'keywords',
        'description',
        'content',
        'setting',
        'payments',
        'form_columns',
        'sort',
        'status',
        'published_at',
        'expired_at',
    ];

    protected $casts = [
        'type_config'  => 'json',
        'setting'      => 'json',
        'payments'     => 'json',
        'form_columns' => 'json',
        'published_at' => 'datetime',
        'expired_at'   => 'datetime',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class, 'product_category_pivots');
    }

    public function skus(): HasMany
    {
        return $this->hasMany(ProductSku::class);
    }

    public static function getProductTypes(): array
    {
        return [
            self::TYPE_PHYSICAL_PRODUCT                   => 'Physical Product',
            self::TYPE_AUTO_DELIVERABLE_VIRTUAL_PRODUCT   => 'Auto Deliverable Virtual Product',
            self::TYPE_MANUAL_DELIVERABLE_VIRTUAL_PRODUCT => 'Manual Deliverable Virtual Product',
        ];
    }
}
