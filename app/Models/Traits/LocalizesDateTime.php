<?php

namespace App\Models\Traits;

use Carbon\Carbon;

trait LocalizesDateTime
{
    /**
     * Cache for datetime fields per model class
     */
    protected static array $cachedDateTimeFields = [];

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
        $modelClass = static::class;

        // Return cached result if available
        if (isset(static::$cachedDateTimeFields[$modelClass])) {
            return static::$cachedDateTimeFields[$modelClass];
        }

        $datetimeFields = [];

        // Get datetime fields from $casts
        foreach ($this->getCasts() as $field => $cast) {
            if (in_array($cast, ['datetime', 'date', 'timestamp'])) {
                $datetimeFields[] = $field;
            }
        }

        // Get all table columns once and check for standard timestamp fields
        try {
            $availableFields = \Schema::getColumnListing($this->getTable());
        } catch (\Exception $e) {
            // Fallback: use model attributes if schema fails
            $availableFields = array_keys($this->attributes ?? []);
        }

        $existingTimestampFields = array_intersect($this->getStandardTimestampKeys(), $availableFields);
        $datetimeFields          = array_merge($datetimeFields, array_diff($existingTimestampFields, $datetimeFields));

        // Cache the result
        static::$cachedDateTimeFields[$modelClass] = $datetimeFields;

        return $datetimeFields;
    }

    protected static function getStandardTimestampKeys(): array
    {
        return ['created_at', 'updated_at', 'deleted_at'];
    }
}
