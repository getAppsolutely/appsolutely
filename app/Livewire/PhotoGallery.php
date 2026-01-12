<?php

declare(strict_types=1);

namespace App\Livewire;

final class PhotoGallery extends GeneralBlock
{
    protected array $defaultDisplayOptions = [
        'title'        => 'Photo Gallery',
        'subtitle'     => '',
        'descriptions' => [
            '',
        ],
        'photos' => [
            [
                'url'      => 'assets/images/gallery/photo-1.webp',
                'title'    => '',
                'subtitle' => '',
                'alt'      => 'Gallery photo 1',
                'caption'  => '',
                'link'     => '',
            ],
            [
                'url'      => 'assets/images/gallery/photo-2.webp',
                'title'    => '',
                'subtitle' => '',
                'alt'      => 'Gallery photo 2',
                'caption'  => '',
                'link'     => '',
            ],
            [
                'url'      => 'assets/images/gallery/photo-3.webp',
                'title'    => '',
                'subtitle' => '',
                'alt'      => 'Gallery photo 3',
                'caption'  => '',
                'link'     => '',
            ],
        ],
    ];
}
