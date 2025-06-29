<?php

declare(strict_types=1);

namespace App\Livewire;

final class TextDocument extends BaseBlock
{
    protected array $defaultDisplayOptions = [
        'title'          => 'Sample Title',
        'subtitle'       => 'Optional Subtitle',
        'content'        => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
        'published_date' => null, // or '2024-01-01' if you want a placeholder
        'author'         => 'Anonymous',
        'show_meta'      => true,
    ];
}
