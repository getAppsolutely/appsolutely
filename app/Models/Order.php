<?php

namespace App\Models;

use App\Models\Traits\HasMonetaryFields;
use App\Models\Traits\ScopeReference;
use App\Models\Traits\ScopeStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasMonetaryFields;
    use ScopeReference;
    use ScopeStatus;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'reference',
        'summary',
        'amount',
        'discounted_amount',
        'total_amount',
        'status',
        'delivery_info',
        'note',
        'remark',
        'ip',
        'request',
    ];

    protected $casts = [
        'delivery_info' => 'array',
        'request'       => 'array',
    ];

    protected $monetaryFields = [
        'amount',
        'discounted_amount',
        'total_amount',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(OrderShipment::class);
    }
}
