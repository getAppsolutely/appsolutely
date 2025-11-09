<?php

declare(strict_types=1);

namespace App\Models\Traits;

trait HasMonetaryFields
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootHasMonetaryFields()
    {
        static::retrieved(function ($model) {
            $model->convertMonetaryFieldsToDecimal();
        });

        static::saving(function ($model) {
            $model->convertMonetaryFieldsToInteger();
        });
    }

    /**
     * Convert monetary fields to decimal format when retrieving from database.
     *
     * @return void
     */
    protected function convertMonetaryFieldsToDecimal()
    {
        foreach ($this->getMonetaryFields() as $field) {
            if (isset($this->attributes[$field])) {
                $this->attributes[$field] = $this->attributes[$field] / 100;
            }
        }
    }

    /**
     * Convert monetary fields to integer format before saving to database.
     *
     * @return void
     */
    protected function convertMonetaryFieldsToInteger()
    {
        foreach ($this->getMonetaryFields() as $field) {
            if (isset($this->attributes[$field])) {
                $this->attributes[$field] = (int) round($this->attributes[$field] * 100);
            }
        }
    }

    /**
     * Get the monetary fields that should be converted.
     *
     * @return array
     */
    protected function getMonetaryFields()
    {
        return $this->monetaryFields ?? [];
    }
}
