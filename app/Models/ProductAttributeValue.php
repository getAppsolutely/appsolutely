<?php

namespace App\Models;

use App\Models\Traits\ScopeStatus;
use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAttributeValue extends Model
{
    use HasFactory;
    use ScopeStatus;
    use Sluggable;
    use SoftDeletes;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attribute_id',
        'value',
        'slug',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the attribute that owns the value.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    /**
     * Get the product SKUs for the attribute value.
     */
    public function productSkus(): BelongsToMany
    {
        return $this->belongsToMany(ProductSku::class, 'product_sku_attribute_value');
    }

    protected function getSlugConfig(): array
    {
        return [
            'source_field' => 'value',
        ];
    }
}
