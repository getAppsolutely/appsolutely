<?php

namespace App\Models\Traits;

trait Publishable
{
    protected static function bootPublishable(): void
    {
        static::creating(function ($model) {
            if (empty($model->published_at)) {
                $model->published_at = now();
            }
        });
    }
}
