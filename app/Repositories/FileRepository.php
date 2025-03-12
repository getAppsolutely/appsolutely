<?php

namespace App\Repositories;

use App\Models\File;

class FileRepository
{
    public function create(array $data): File
    {
        return File::create($data);
    }

    public function delete(File $file): bool
    {
        return $file->delete();
    }

    public function find(int $id): ?File
    {
        return File::find($id);
    }
}
