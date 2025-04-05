<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class DashboardHelper
{
    const DASHBOARD_ASSETS_PATH = 'assets/';

    const DASHBOARD_FILES_PATH = 'uploads/';

    public static function preview($filePath, $prefix = self::DASHBOARD_FILES_PATH, $maxWidth = 100, $maxHeight = 100): string
    {
        $url = url($prefix . $filePath);

        $extension = strtolower(File::extension($filePath));

        $html =  in_array($extension, FileHelper::IMAGE_EXTENSIONS) ?
            "<img src='{$url}' style='max-width:{$maxWidth}px;max-height:{$maxHeight}px;'>" :
            '<i class="fa fa-file-o"></i> ' . $extension;

        return "<a href='{$url}' target='_blank'>{$html}</a>";
    }
}
