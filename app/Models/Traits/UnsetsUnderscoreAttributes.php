<?php

declare(strict_types=1);

namespace App\Models\Traits;

trait UnsetsUnderscoreAttributes
{
    protected static function bootUnsetsUnderscoreAttributes()
    {
        static::saving(function ($model) {
            foreach ($model->getAttributes() as $key => $value) {
                if (str_starts_with($key, '_')) {
                    unset($model->$key);
                }
            }
        });
    }
}
