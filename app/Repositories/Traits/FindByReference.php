<?php

namespace App\Repositories\Traits;

trait FindByReference
{
    public function findOneByReference($reference)
    {
        return $this->model->where('reference', $reference)->first();
    }
}
