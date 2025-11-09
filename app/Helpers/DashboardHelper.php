<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class DashboardHelper
{
    public static function imageThumbnail($filePath, $maxWidth = 100, $maxHeight = 100): string
    {
        $url = upload_url($filePath);

        $extension = strtolower(File::extension($filePath));

        $html =  in_array($extension, FileHelper::IMAGE_EXTENSIONS) ?
            "<img src='{$url}' style='max-width:{$maxWidth}px;max-height:{$maxHeight}px;'>" :
            '<i class="fa fa-file-o"></i> ' . $extension;

        return "<a href='{$url}' target='_blank'>{$html}</a>";
    }
}
