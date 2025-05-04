<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'title',
        'display',
        'vendor',
        'handler',
        'device',
        'merchant_id',
        'merchant_key',
        'merchant_secret',
        'setting',
        'instruction',
        'remark',
        'sort',
        'status',
    ];

    protected $casts = [
        'setting' => 'array',
        'sort'    => 'integer',
        'status'  => 'integer',
    ];
}
