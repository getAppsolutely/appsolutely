<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'original_filename',
        'filename',
        'extension',
        'mime_type',
        'path',
        'size',
        'hash',
    ];

    protected $casts = [
        'size'       => 'integer',
        'deleted_at' => 'datetime',
    ];

    function getFullPathAttribute()
    {
        return $this->path . '/' . $this->filename;
    }
}
