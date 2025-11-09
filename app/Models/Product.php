<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasFilesOfType;
use App\Models\Traits\HasMarkdownContent;
use App\Models\Traits\HasMonetaryFields;
use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeStatus;
use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFilesOfType;
    use HasMarkdownContent;
    use hasMonetaryFields;
    use ScopePublished;
    use ScopeStatus;
    use Sluggable;
    use SoftDeletes;

    const TYPE_PHYSICAL_PRODUCT = 'PHYSICAL';

    const TYPE_AUTO_DELIVERABLE_VIRTUAL_PRODUCT = 'AUTO_VIRTUAL';

    const TYPE_MANUAL_DELIVERABLE_VIRTUAL_PRODUCT = 'MANUAL_VIRTUAL';

    const SHIPMENT_METHOD_PHYSICAL_PRODUCT = ['App\Models\UserAddress'];

    const SHIPMENT_METHOD_AUTO_DELIVERABLE_VIRTUAL_PRODUCT = ['App\Models\User'];

    const SHIPMENT_METHOD_MANUAL_DELIVERABLE_VIRTUAL_PRODUCT = [
        'Email',
        'Mobile',
        'Whatsapp',
        'Telegram',
        'WeChat',
        'Weixin',
    ];

    protected $fillable = [
        'type',
        'shipment_methods',
        'slug',
        'title',
        'subtitle',
        'cover',
        'keywords',
        'description',
        'content',
        'original_price',
        'price',
        'setting',
        'payment_methods',
        'additional_columns',
        'sort',
        'status',
        'published_at',
        'expired_at',
    ];

    protected $casts = [
        'shipment_methods'   => 'array',
        'setting'            => 'array',
        'payment_methods'    => 'array',
        'additional_columns' => 'array',
        'published_at'       => 'datetime',
        'expired_at'         => 'datetime',
    ];

    protected $monetaryFields = [
        'original_price',
        'price',
    ];

    // Boot method removed - sitemap cache clearing moved to ProductObserver

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class, 'product_category_pivot');
    }

    public function skus(): HasMany
    {
        return $this->hasMany(ProductSku::class);
    }

    // Business logic methods moved to ProductService for better separation of concerns
}
