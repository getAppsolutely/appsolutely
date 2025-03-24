<?php

namespace App\Helpers;

class DashboardHelper
{
    const DASHBOARD_ASSETS_PATH = 'assets/';
    const DASHBOARD_FILES_PATH = 'uploads/';

    public static function preview($filePath, $extension, $prefix = self::DASHBOARD_FILES_PATH, $maxWidth = 100, $maxHeight = 100)
    {
        $url = url($prefix . $filePath);

        $extension = strtolower($extension);

        $html =  in_array($extension, FileHelper::IMAGE_EXTENSIONS) ?
            "<img src='{$url}' style='max-width:{$maxWidth}px;max-height:{$maxHeight}px;'>" :
            '<i class="fa fa-file-o"></i> ' . $extension;

        return "<a href='{$url}' target='_blank'>{$html}</a>";
    }

}
