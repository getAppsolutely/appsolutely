<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Models\Assessable;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class FileRepository extends BaseRepository
{
    public function model(): string
    {
        return File::class;
    }

    public function findByAssessable($filePath): ?Assessable
    {
        return Assessable::query()->with(['file'])->whereFilePath($filePath)->first();
    }

    public function getLibrary(Request $request): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Filter by file extensions (images only)
        $query->whereIn('extension', FileHelper::IMAGE_EXTENSIONS);

        // Filter by name if provided
        if ($request->has('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('original_filename', 'like', "%{$request->name}%")
                    ->orWhere('filename', 'like', "%{$request->name}%");
            });
        }

        // Filter by path if provided
        if ($request->has('path')) {
            $query->where('path', 'like', "%{$request->path}%");
        }

        // Filter by extension if provided
        if ($request->has('extension')) {
            $query->whereIn('extension', explode(',', $request->extension));
        }

        // Apply sorting
        $sortField     = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['created_at', 'original_filename', 'size', 'path'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        // Apply pagination
        $page     = max(1, intval($request->get('page', 1)));
        $pageSize = max(1, min(100, intval($request->get('page_size', 20)))); // Between 1 and 100

        $paginator = $query->paginate($pageSize, ['*'], 'page', $page);

        // Transform the items to include full_path with app URL
        $paginator->through(function ($file) {
            $file->full_path = $file->getAttribute('full_path');
            $file->url       = app_url('uploads/' . $file->full_path);

            return $file;
        });

        return $paginator;
    }
}
