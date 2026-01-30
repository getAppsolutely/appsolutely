<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Builder;

trait ScopeStatus
{
    public function scopeStatus(Builder $query, $value = null, $operator = null): void
    {
        $value    = $value ?? Status::ACTIVE;
        $operator = $operator ?? '=';
        $query->where('status', $operator, $value);
    }
}
