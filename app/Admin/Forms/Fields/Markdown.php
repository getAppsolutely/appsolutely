<?php

namespace App\Admin\Forms\Fields;

class Markdown
{
    public static function script()
    {
        return <<<'JS'
    $(function () {
        if (typeof editormd !== "undefined") {
            editormd.toolbarHandlers["image-library"] = function() {
                // TODO: Image library
            };
        }
    });
JS;
    }

    public static function options()
    {
        return [
            'imageUpload'  => false,
            'toolbarIcons' => [
                'undo', 'redo', '|',
                'bold', 'del', 'italic', 'quote', 'ucwords', 'uppercase', 'lowercase', '|',
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6', '|',
                'list-ul', 'list-ol', 'hr', '|',
                'link', 'reference-link', 'image', 'image-library', 'code', 'preformatted-text', 'code-block', 'table', 'datetime', 'emoji', 'html-entities', 'pagebreak', '|',
                'goto-line', 'watch', 'preview', 'fullscreen', 'clear', 'search', '|', 'info',
            ],
            'toolbarIconsClass' => [
                'image-library' => 'fa-image',
            ],
        ];
    }
}
