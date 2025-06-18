<?php

namespace App\Models;

use App\Models\Traits\ScopeStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderShipment extends Model
{
    use ScopeStatus;
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_type',
        'email',
        'name',
        'mobile',
        'address',
        'address_extra',
        'town',
        'city',
        'province',
        'postcode',
        'country',
        'delivery_vendor',
        'delivery_reference',
        'remark',
        'status',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
