<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ScopeReference
{
    public function ScopeReference(Builder $query, $value = null): void
    {
        if (! empty($value)) {
            $query->where('reference', $value);
        }
    }
}
