<?php

namespace App\Models\Traits;

use App\Constants\BasicConstant;
use Illuminate\Database\Eloquent\Builder;

trait ScopeStatus
{
    public function scopeStatus(Builder $query, $value = null, $operator = null): void
    {
        $value    = $value ?? BasicConstant::ON;
        $operator = $operator ?? '=';
        $query->where('status', $operator, $value);
    }
}
