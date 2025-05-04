<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_sku_id',
        'reference',
        'summary',
        'original_price',
        'price',
        'quantity',
        'discounted_amount',
        'amount',
        'product_snapshot',
        'note',
        'remark',
        'status',
    ];

    protected $casts = [
        'original_price'    => 'integer',
        'price'             => 'integer',
        'quantity'          => 'integer',
        'discounted_amount' => 'integer',
        'amount'            => 'integer',
        'product_snapshot'  => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productSku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class);
    }
}
