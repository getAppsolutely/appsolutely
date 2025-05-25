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
                if (! in_array($field, static::getStandardTimestampKeys())) {
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
        $datetimeFields = [];

        // Get datetime fields from $casts
        foreach ($this->getCasts() as $field => $cast) {
            if (in_array($cast, ['datetime', 'date', 'timestamp'])) {
                $datetimeFields[] = $field;
            }
        }

        foreach ($this->getStandardTimestampKeys() as $field) {
            if ($this->hasColumn($field) && ! in_array($field, $datetimeFields)) {
                $datetimeFields[] = $field;
            }
        }

        return $datetimeFields;
    }

    /**
     * Check if the model has a specific column
     */
    protected function hasColumn(string $column): bool
    {
        try {
            return \Schema::hasColumn($this->getTable(), $column);
        } catch (\Exception $e) {
            // Fallback: check if the attribute exists in the model
            return array_key_exists($column, $this->attributes ?? []);
        }
    }

    protected static function getStandardTimestampKeys(): array
    {
        return ['created_at', 'updated_at', 'deleted_at'];
    }
}
