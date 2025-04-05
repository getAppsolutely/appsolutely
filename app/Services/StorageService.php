<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\File;
use App\Repositories\AdminSettingRepository;
use App\Repositories\FileRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StorageService
{

    public function __construct(protected AdminSettingRepository $adminSettingRepository,
                                protected FileRepository $fileRepository){}

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
            log_error('S3 Upload failed: ' . $e->getMessage(), [
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
    public function findFile(int $id): \Illuminate\Database\Eloquent\Model
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
        if (request()->route()->getName() == 'file.assets') {
            $assessable = $this->fileRepository->findByAssessable($filePath);
            if (empty($assessable->file->full_path)) {
                abort(404);
            }
            $filePath = $assessable->file->full_path;
        }
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
            if (empty($s3Contents)) {
                return null;
            }

            // Store the file locally
            Storage::disk('public')->put($localFilePath, $s3Contents);

            return [
                $s3Contents,
                Storage::disk('public')->mimeType($localFilePath),
            ];
        } catch (\Exception $e) {
            log_error('Failed to retrieve file from S3', [
                'filePath' => $filePath,
                'error'    => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function assessable(File $file, $uploader)
    {
        $filePath = false;
        $type = $uploader->upload_column ?? null;
        if (in_array($type, array_keys(AdminSetting::PATH_PATTERNS)) && $pattern = config(AdminSetting::PATH_PATTERNS[$type])) {
            $adminSetting = $this->adminSettingRepository->find('ghost::admin_config');
            $filePath = sprintf($pattern, $file->extension);
            $sync = [$file->id => ['type' => $type, 'file_path' => $filePath]];
            $adminSetting->filesOfType($type)->sync($sync);
        }

        return $filePath;
    }
}
