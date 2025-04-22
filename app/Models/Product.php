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

    const TYPE_PHYSICAL_PRODUCT = 'PHYSICAL';

    const TYPE_AUTO_DELIVERABLE_VIRTUAL_PRODUCT = 'AUTO_VIRTUAL';

    const TYPE_MANUAL_DELIVERABLE_VIRTUAL_PRODUCT = 'MANUAL_VIRTUAL';

    const SHIPMENT_METHOD_PHYSICAL_PRODUCT = ['\App\Models\UserAddress'];

    const SHIPMENT_METHOD_AUTO_DELIVERABLE_VIRTUAL_PRODUCT = ['\App\Models\User'];

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
        'cover',
        'keywords',
        'description',
        'content',
        'setting',
        'payment_methods',
        'form_columns',
        'sort',
        'status',
        'published_at',
        'expired_at',
    ];

    protected $casts = [
        'shipment_methods' => 'json',
        'setting'          => 'json',
        'payment_methods'  => 'json',
        'form_columns'     => 'json',
        'published_at'     => 'datetime',
        'expired_at'       => 'datetime',
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
            self::TYPE_PHYSICAL_PRODUCT                   => __t('Physical Product'),
            self::TYPE_AUTO_DELIVERABLE_VIRTUAL_PRODUCT   => __t('Auto Deliverable Virtual Product'),
            self::TYPE_MANUAL_DELIVERABLE_VIRTUAL_PRODUCT => __t('Manual Deliverable Virtual Product'),
        ];
    }

    public static function getShipmentMethodForManualVirtualProduct(): array
    {
        return self::SHIPMENT_METHOD_MANUAL_DELIVERABLE_VIRTUAL_PRODUCT;
    }

    public static function getShipmentMethodForAutoVirtualProduct(): array
    {
        return self::SHIPMENT_METHOD_AUTO_DELIVERABLE_VIRTUAL_PRODUCT;
    }

    public static function getShipmentMethodForPhysicalProduct(): array
    {
        return self::SHIPMENT_METHOD_PHYSICAL_PRODUCT;
    }
}
