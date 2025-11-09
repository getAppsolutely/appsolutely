<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository as Repository;

class BaseRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * Child repositories must implement this method to return the model class name.
     */
    public function model(): string
    {
        throw new \RuntimeException(
            'BaseRepository::model() must be implemented by child repository classes. ' .
            'Return the fully qualified model class name (e.g., return Page::class;).'
        );
    }
}
