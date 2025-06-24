<?php

declare(strict_types=1);

namespace App\Livewire;

final class TextDocument extends BaseBlock
{
    protected function defaultConfig(): array
    {
        return [
            'title'          => 'Document Title',
            'subtitle'       => '',
            'content'        => '<p>Document content goes here...</p>',
            'published_date' => null,
            'author'         => '',
            'show_meta'      => true,
        ];
    }
}
