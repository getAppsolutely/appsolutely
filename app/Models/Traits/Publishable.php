<?php

namespace App\Models\Traits;

use Carbon\Carbon;

trait Publishable
{
    protected static function bootPublishable(): void
    {
        static::creating(function ($model) {
            if (empty($model->published_at)) {
                $model->published_at = Carbon::now();
            }
        });
        static::updating(function ($model) {
            if (empty($model->published_at)) {
                $model->published_at = Carbon::now();
            } else {
                $dt                  = Carbon::parse($model->published_at, config('appsolutely.local_timezone'))
                    ->setTimezone(config('app.timezone'));
                $model->published_at = $dt;
            }
        });
    }
}
