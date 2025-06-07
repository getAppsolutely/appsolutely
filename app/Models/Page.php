<?php

namespace App\Models;

class Page extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'published_at',
        'expired_at',
        'status',
        'title',
        'description',
        'keywords',
        'content',
        'canonical_url',
        'meta_robots',
        'og_title',
        'og_description',
        'og_image',
        'structured_data',
        'hreflang',
        'language',
        'parent_id',
    ];

    protected $casts = [
        'published_at'    => 'datetime',
        'expired_at'      => 'datetime',
        'status'          => 'integer',
        'structured_data' => 'array',
    ];
}
