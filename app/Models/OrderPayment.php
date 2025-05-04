<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'order_id',
        'payment_id',
        'vendor',
        'vendor_reference',
        'vendor_extra_info',
        'payment_amount',
        'status',
    ];

    protected $casts = [
        'vendor_extra_info' => 'array',
        'payment_amount'    => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
