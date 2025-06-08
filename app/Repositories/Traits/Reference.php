<?php

namespace App\Repositories\Traits;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait Reference
{
    public function reference($reference): Builder
    {
        return $this->model->reference($reference);
    }

    public function findByReference($reference): Model
    {
        return $this->model->reference($reference)->firstOrFail();
    }

    public function getByReference($reference): Collection
    {
        return $this->model->reference($reference)->get();
    }
}
