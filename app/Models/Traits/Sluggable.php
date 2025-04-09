<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
    protected static function bootSluggable(): void
    {
        static::saving(function ($model) {
            // If slug is empty, generate from title
            if (empty($model->slug) && ! empty($model->title)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }
}
