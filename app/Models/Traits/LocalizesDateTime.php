<?php

namespace App\Models\Traits;

use Carbon\Carbon;

trait LocalizesDateTime
{
    /**
     * Boot the LocalizesTimestamps trait for a model.
     *
     * @return void
     */
    public static function bootLocalizesDateTime()
    {
        static::retrieved(function ($model) {
            foreach ($model->getLocalDateTimeFields() as $field) {
                // Only override if the attribute exists
                if (array_key_exists($field, $model->attributes ?? []) && $model->getOriginal($field) instanceof Carbon) {
                    $time = $model->getRawOriginal($field);
                    $model->setAttribute($field . '_local', to_local($time));
                }
            }
        });

        static::saving(function ($model) {
            foreach ($model->getLocalDateTimeFields() as $field) {
                $localKey = $field . '_local';
                if (! in_array($field, static::getExceptionalKeys())) {
                    $model->attributes[$field] = to_utc($model->attributes[$localKey]) ?? Carbon::now();
                }
                unset($model->attributes[$localKey]);
            }
        });
    }

    /**
     * Get the list of timestamp fields to convert to local time.
     * Override this property in your model as needed.
     */
    protected function getLocalDateTimeFields(): array
    {
        return property_exists($this, 'localDateTimeFields') ? $this->localDateTimeFields : [];
    }

    protected static function getExceptionalKeys(): array
    {
        return ['created_at', 'updated_at', 'deleted_at'];
    }
}
