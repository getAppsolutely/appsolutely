<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'file_id',
        'assetable_id',
        'assetable_type',
        'type',
        'file_path',
        'title',
        'keyword',
        'description',
        'content',
        'status',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function assetable()
    {
        return $this->morphTo();
    }

    // Scope for specific asset types
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
