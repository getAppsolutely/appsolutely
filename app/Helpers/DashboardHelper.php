<?php

namespace App\Helpers;

class DashboardHelper
{
    public static function preview($filePath, $extension)
    {
        $url = admin_url('assets/' . $filePath);

        $extension = strtolower($extension);
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $html =  in_array($extension, $imageExtensions) ?
            "<img src='{$url}' style='max-width:100px;max-height:100px;'>" :
            '<i class="fa fa-file-o"></i> ' . $extension;

        return "<a href='{$url}' target='_blank'>{$html}</a>";
    }
}
