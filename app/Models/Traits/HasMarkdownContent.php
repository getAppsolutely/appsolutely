<?php

namespace App\Models\Traits;

use App\Repositories\FileRepository;
use Str;

trait HasMarkdownContent
{
    /**
     * Boot the trait
     */
    protected static function bootHasMarkdownContent(): void
    {
        static::created(function ($model) {
            foreach ($model->getMarkdownFields() as $field) {
                $model->replaceWithAssessables($model, $field);
            }
        });

        static::updated(function ($model) {
            foreach ($model->getMarkdownFields() as $field) {
                if ($model->isDirty($field)) {
                    $model->replaceWithAssessables($model, $field);
                }
            }
        });
    }

    protected function replaceWithAssessables($model, $field): void
    {
        [$searches, $replaces] = $model->processMarkdownImages($field);
        $model->$field         = str_replace($searches, $replaces, $model->$field);
        $model->saveQuietly();
    }

    protected function processMarkdownImages(string $field): array
    {
        $content = $this->{$field};

        // Get all images from markdown content
        $images = parse_markdown_images($content);

        // Collect all file IDs with their details
        $syncData       = [];
        $fileRepository = app(FileRepository::class);
        $searches       = $replaces = [];

        foreach ($images as $image) {
            // Extract filename from url
            $filename = basename($image['url']);

            // Find the file record using repository
            $file = $fileRepository->findByFilename($filename);

            if ($file) {
                $filePath            = sprintf('%s/%s', Str::plural(Str::kebab(class_basename(($this)))), $file->getAttribute('full_path'));
                $searches[]          = $image['url'];
                $replaces[]          = asset_url($filePath);
                $syncData[$file->id] = [
                    'type'      => $field,
                    'file_path' => $filePath,
                ];
            }
        }

        // Sync the files with the model including additional details
        $this->filesOfType($field)->sync($syncData);

        return [$searches, $replaces];
    }

    protected function getMarkdownFields(): array
    {
        return [
            'content',
        ];
    }
}
