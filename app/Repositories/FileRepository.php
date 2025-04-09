<?php

namespace App\Repositories;

use App\Models\Assessable;
use App\Models\File;

class FileRepository extends BaseRepository
{
    public function __construct(File $model)
    {
        $this->model = $model;
    }

    public function findByAssessable($filePath): ?Assessable
    {
        return Assessable::query()->with(['file'])->whereFilePath($filePath)->first();
    }
}
