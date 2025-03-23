<?php

namespace App\Services;

use App\Models\File;
use App\Repositories\FileRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageService
{
    protected FileRepository $fileRepository;
    // protected mixed $prefixPath;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
        // $this->prefixPath = config('filesystems.disks.s3.prefix');
    }

    private function getFilePath(File $file): string
    {
        return $file->path . '/' . $file->filename;
    }

    public function store(UploadedFile $file): File
    {
        $originalFilename = $file->getClientOriginalName();
        $extension        = $file->getClientOriginalExtension();
        $mimeType         = $file->getMimeType();
        $size             = $file->getSize();
        $hash             = hash_file('sha256', $file->getRealPath());

        // Generate path based on current year and month (YYYY/MM)
        $path = now()->format('Y/m');

        // Generate unique filename
        $filename = Str::uuid() . '.' . $extension;

        try {
            // Store file to S3
            $filePath = $path . '/' . $filename;
            $result   = Storage::disk('s3')->putFileAs(
                $path,
                $file,
                $filename,
                ['visibility' => 'public']
            );

            if (! $result) {
                throw new \Exception('Failed to upload file to S3');
            }

            // Verify file exists
            if (! Storage::disk('s3')->exists($filePath)) {
                throw new \Exception('File not found after upload');
            }

            // Create database record using FileRepository
            return $this->fileRepository->create([
                'original_filename' => $originalFilename,
                'filename'          => $filename,
                'extension'         => $extension,
                'mime_type'         => $mimeType,
                'path'              => $path,
                'size'              => $size,
                'hash'              => $hash,
            ]);
        } catch (\Exception $e) {
            Log::error('S3 Upload failed: ' . $e->getMessage(), [
                'file'  => $originalFilename,
                'path'  => $path,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function delete(File $file): bool
    {
        // Delete from S3
        if (Storage::disk('s3')->delete($this->getFilePath($file))) {
            return $this->fileRepository->delete($file);
        }

        return false;
    }

    public function getSignedUrl(File $file, int $expiresInMinutes = 5): string
    {
        return Storage::disk('s3')->temporaryUrl(
            $this->getFilePath($file),
            now()->addMinutes($expiresInMinutes)
        );
    }

    /**
     * Find a file by its ID.
     */
    public function findFile(int $id): ?File
    {
        return $this->fileRepository->find($id);
    }

    /**
     * Retrieve a file from storage, pulling from S3 if not found locally
     *
     * @param  string  $filePath  Full file path including filename
     * @return array|null Returns [file contents, mime type] if found, null if not found
     */
    public function retrieve(string $filePath): ?array
    {
        // Check if file exists in local storage
        $localFilePath = appsolutely() . '/' . $filePath;
        if (Storage::disk('public')->exists($localFilePath)) {
            return [
                Storage::disk('public')->get($localFilePath),
                Storage::disk('public')->mimeType($localFilePath),
            ];
        }

        try {
            // If not in local storage, attempt to get from S3
            $s3Contents = Storage::disk('s3')->get($filePath);

            // Store the file locally
            Storage::disk('public')->put($localFilePath, $s3Contents);

            return [
                $s3Contents,
                Storage::disk('public')->mimeType($localFilePath),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to retrieve file from S3', [
                'filePath' => $filePath,
                'error'    => $e->getMessage(),
            ]);

            return null;
        }
    }
}
