<?php

declare(strict_types=1);

namespace App\Repositories\Traits;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait Status
{
    public function status($reference): Builder
    {
        return $this->model->status($reference);
    }

    public function findByStatus($reference): Model
    {
        return $this->model->status($reference)->firstOrFail();
    }

    public function getByStatus($reference): Collection
    {
        return $this->model->status($reference)->get();
    }
}
