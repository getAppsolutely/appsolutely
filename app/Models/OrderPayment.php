<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\ScopeReference;
use App\Models\Traits\ScopeStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPayment extends Model
{
    use ScopeReference;
    use ScopeStatus;
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
    ];

    protected $monetaryFields = [
        'payment_amount',
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
