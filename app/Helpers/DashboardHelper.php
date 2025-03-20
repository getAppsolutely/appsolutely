<?php

namespace App\Helpers;

class DashboardHelper
{
    public static function preview($filePath, $extension, $maxWidth = 100, $maxHeight = 100)
    {
        $url = admin_url('assets/' . $filePath);

        $extension = strtolower($extension);

        $html =  in_array($extension, FileHelper::IMAGE_EXTENSIONS) ?
            "<img src='{$url}' style='max-width:{$maxWidth}px;max-height:{$maxHeight}px;'>" :
            '<i class="fa fa-file-o"></i> ' . $extension;

        return "<a href='{$url}' target='_blank'>{$html}</a>";
    }
}
