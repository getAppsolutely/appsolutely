<?php

namespace Tests\Unit;

use App\Models\File;
use App\Repositories\FileRepository;
use App\Services\StorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class StorageServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->fileRepository = Mockery::mock(FileRepository::class);
        $this->storageService = new StorageService($this->fileRepository);
    }

    public function testStoreFileSuccessfully()
    {
        Storage::fake('s3');

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $this->fileRepository->shouldReceive('create')->once()->andReturn(new File());

        $storedFile = $this->storageService->store($file);

        Storage::disk('s3')->assertExists($storedFile->path . '/' . $storedFile->filename);
    }

    public function testStoreFileUploadFails()
    {
        Storage::shouldReceive('disk->putFileAs')->andReturn(false);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to upload file to S3');

        $this->storageService->store($file);
    }

    public function testStoreFileNotFoundAfterUpload()
    {
        Storage::shouldReceive('disk->putFileAs')->andReturn(true);
        Storage::shouldReceive('disk->exists')->andReturn(false);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File not found after upload');

        $this->storageService->store($file);
    }

    public function testDeleteFileSuccessfully()
    {
        $file = new File(['path' => '2023/03', 'filename' => 'document.pdf']);

        Storage::shouldReceive('disk->delete')->andReturn(true);
        $this->fileRepository->shouldReceive('delete')->once()->andReturn(true);

        $result = $this->storageService->delete($file);

        $this->assertTrue($result);
    }

    public function testDeleteFileFails()
    {
        $file = new File(['path' => '2023/03', 'filename' => 'document.pdf']);

        Storage::shouldReceive('disk->delete')->andReturn(false);

        $result = $this->storageService->delete($file);

        $this->assertFalse($result);
    }

    public function testGetSignedUrl()
    {
        $file = new File(['path' => '2023/03', 'filename' => 'document.pdf']);

        Storage::shouldReceive('disk->temporaryUrl')->andReturn('http://example.com/signed-url');

        $url = $this->storageService->getSignedUrl($file);

        $this->assertEquals('http://example.com/signed-url', $url);
    }
}
