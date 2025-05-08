<?php

namespace App\Models;

use App\Models\Traits\HasFilesOfType;
use App\Models\Traits\HasMarkdownContent;
use App\Models\Traits\HasMonetaryFields;
use App\Models\Traits\Sluggable;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class ProductSku extends Model implements Sortable
{
    use HasDateTimeFormatter;
    use HasFilesOfType;
    use HasMarkdownContent;
    use hasMonetaryFields;
    use Sluggable;
    use SoftDeletes;
    use SortableTrait;

    public array $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    protected $fillable = [
        'product_id',
        'attributes',
        'slug',
        'title',
        'cover',
        'keywords',
        'description',
        'content',
        'stock',
        'original_price',
        'price',
        'sort',
        'status',
        'published_at',
        'expired_at',
    ];

    protected $casts = [
        'attributes'   => 'json',
        'stock'        => 'integer',
        'sort'         => 'integer',
        'status'       => 'integer',
        'published_at' => 'datetime',
        'expired_at'   => 'datetime',
    ];

    protected $monetaryFields = [
        'original_price',
        'price',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getTitle(): string
    {
        return $this->title ?: $this->product->title;
    }

    public function getCover(): ?string
    {
        return $this->cover ?: $this->product->cover;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords ?: $this->product->keywords;
    }

    public function getDescription(): ?string
    {
        return $this->description ?: $this->product->description;
    }

    public function getContent(): ?string
    {
        return $this->content ?: $this->product->content;
    }

    public function getOriginalPrice(): ?string
    {
        return $this->original_price ?: $this->product->original_price;
    }

    protected function getSlugConfig(): array
    {
        return [
            'source_field' => 'title',
            'slug_field'   => 'slug',
            'parent_field' => 'product_id',
        ];
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_sku_attribute_value');
    }
}
