<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface StorageServiceInterface
{
    /**
     * Store an uploaded file
     */
    public function store(UploadedFile $file): File;

    /**
     * Delete a file
     */
    public function delete(File $file): bool;

    /**
     * Get a signed URL for a file
     */
    public function getSignedUrl(File $file, int $expiresInMinutes = 5): string;

    /**
     * Find a file by its ID
     */
    public function findFile(int $id): \Illuminate\Database\Eloquent\Model;

    /**
     * Retrieve a file from storage
     *
     * @param  string  $filePath  Full file path including filename
     * @return array|null Returns [file contents, mime type] if found, null if not found
     */
    public function retrieve(string $filePath): ?array;

    /**
     * Associate file with an assessable model
     */
    public function assessable(File $file): false|string|null;

    /**
     * Get file library with pagination
     */
    public function getLibrary(Request $request): LengthAwarePaginator;

    /**
     * Get file response
     */
    public function response(Request $request, ?string $filePath = null): Response|JsonResponse;
}
