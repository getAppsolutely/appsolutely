<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProductType;
use App\Models\Traits\HasFilesOfType;
use App\Models\Traits\HasMarkdownContent;
use App\Models\Traits\HasMonetaryFields;
use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeStatus;
use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use HasFilesOfType;
    use HasMarkdownContent;
    use hasMonetaryFields;
    use ScopePublished;
    use ScopeStatus;
    use Sluggable;
    use SoftDeletes;

    const array SHIPMENT_METHOD_PHYSICAL_PRODUCT = ['App\Models\UserAddress'];

    const array SHIPMENT_METHOD_AUTO_DELIVERABLE_VIRTUAL_PRODUCT = ['App\Models\User'];

    const array SHIPMENT_METHOD_MANUAL_DELIVERABLE_VIRTUAL_PRODUCT = [
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
        'type'               => ProductType::class,
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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class, 'product_category_pivot');
    }

    public function skus(): HasMany
    {
        return $this->hasMany(ProductSku::class);
    }
}
